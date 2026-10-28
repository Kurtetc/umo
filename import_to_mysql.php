<?php
/*
 * import_to_mysql.php — загрузка видеокарт из bd.csv в MySQL.
 *
 * Как использовать:
 *   1) Отредактируйте настройки подключения ниже (как в config.php).
 *   2) Создайте базу данных (например, gpu_compare) с кодировкой utf8mb4.
 *   3) Запустите ОДИН раз:
 *        - через Open Server Panel:    php import_to_mysql.php
 *        - или в браузере:             http://localhost/import_to_mysql.php
 *      Таблица gpus будет пересоздана и заполнена заново из CSV.
 *   4) После импорта файл на боевом сайте можно удалить (или оставить
 *      для периодического обновления базы — повторный запуск перезапишет данные).
 */

// ---- Настройки подключения (совпадают с config.php) ----
$DB_HOST = 'localhost';
$DB_NAME = 'gpu_compare';
$DB_USER = 'root';
$DB_PASS = '';           // в Open Server Panel у root по умолчанию пустой пароль
$CSV     = __DIR__ . '/bd.csv';

// ---- Подключение ----
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Не удалось подключиться к MySQL: " . $e->getMessage() . "\n");
}

if (!is_file($CSV)) die("Не найден файл: $CSV\n");

// ---- Создание таблицы ----
$pdo->exec('DROP TABLE IF EXISTS gpus');
$pdo->exec('CREATE TABLE gpus (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL COMMENT "Полное название, напр. NVIDIA GeForce RTX 5090",
    short_name      VARCHAR(255) NOT NULL COMMENT "Короткое имя для отображения (Videocard Name_perf)",
    perf_score      INT          COMMENT "Passmark G3D Mark — шкала производительности",
    vendor_code     VARCHAR(16)  COMMENT "nvd / amd / int",
    brand           VARCHAR(16)  COMMENT "NVIDIA / AMD / INTEL",
    arch_name       VARCHAR(64)  COMMENT "Архитектура (Blackwell 2.0, RDNA 4.0...)",
    release_date    DATE         NULL COMMENT "Дата релиза",
    process_size    VARCHAR(32)  COMMENT "Техпроцесс (5 nm)",
    cores           INT          NULL COMMENT "Шейдерные/унифицированные блоки",
    tensor_cores    INT          NULL,
    rt_cores        INT          NULL,
    base_clock      INT          NULL COMMENT "МГц",
    boost_clock     INT          NULL COMMENT "МГц",
    memory_mb       INT          NULL COMMENT "Объём видеопамяти в МБ",
    memory_type     VARCHAR(32)  COMMENT "GDDR7, HBM2...",
    bus_width       INT          NULL COMMENT "бит",
    bus_interface   VARCHAR(64)  COMMENT "PCIe 5.0 x16...",
    bandwidth       DECIMAL(10,3) NULL COMMENT "Пропускная способность, ГБ/с",
    tdp_watt        INT          NULL,
    psu_watt        INT          NULL COMMENT "Рекомендуемый БП",
    power_connectors VARCHAR(64),
    mem_freq_mhz    INT          NULL COMMENT "Эффективная частота памяти, МГц",
    gpu_core        VARCHAR(64)  COMMENT "Название ядра (GB202...)",
    rop_blocks      INT          NULL COMMENT "Блоки растеризации",
    pixel_rate      DECIMAL(10,2) NULL COMMENT "Гпиксель/с",
    tmu_blocks      INT          NULL COMMENT "Текстурные блоки",
    texture_rate    DECIMAL(10,2) NULL COMMENT "Гтексель/с",
    directx         VARCHAR(16)  COMMENT "7 / 10.1 / 12.2...",
    is_new          TINYINT(1) DEFAULT 0 COMMENT "1 = новый продукт (бейдж NEW)",
    INDEX (brand), INDEX (perf_score), INDEX (release_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

// ---- Чтение CSV ----
// Функции преобразования «сырых» значений CSV в значения для базы.
function num($s) {                     // "1 792" / "423,6" -> float, '' -> null
    $s = trim(str_replace([' ', "\xc2\xa0", ','], ['', '', '.'], (string)$s));
    $s = preg_replace('/[^\d.\-]/', '', $s);
    return ($s === '' || $s === '-' || $s === '.') ? null : (float)$s;
}
function parseDate($s) {               // "30.01.2025" -> "2025-01-30", иначе null
    $s = trim((string)$s);
    if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $s, $m)) return "{$m[3]}-{$m[2]}-{$m[1]}";
    return null;
}
function bwToGBs($s) {                 // "1.79 TB/s" -> 1790 (ГБ/с), "Dependent" -> null
    $s = strtolower(trim((string)$s));
    if (preg_match('/^([\d.,]+)\s*(gb|tb)/', $s, $m)) {
        $v = num($m[1]);
        return $v === null ? null : round($m[2] === 'tb' ? $v * 1000 : $v, 3);
    }
    return null;
}
function watt($s) {                    // "575 W" / "unknown" -> 575 / null
    return num(preg_replace('/[^0-9.,]/', '', (string)$s));
}
function ramMB($ram, $type) {          // объём в МБ: <16 считаем гигабайтами (старые карты в MB уже > 16)
    $v = num($ram);
    if ($v === null) return null;
    if (stripos((string)$type, 'shared') !== false) return 0; // системная память
    return (int)round($v < 16 ? $v * 1024 : $v);
}
function dxVer($s) {                   // "12,2" -> "12.2"
    $s = trim(str_replace(',', '.', (string)$s));
    return $s === '' ? null : $s;
}

$VENDOR = ['nvd' => 'NVIDIA', 'nvidia' => 'NVIDIA', 'amd' => 'AMD', 'int' => 'INTEL'];

$sth = $pdo->prepare('INSERT INTO gpus (name, short_name, perf_score, vendor_code, brand, arch_name,
    release_date, process_size, cores, tensor_cores, rt_cores, base_clock, boost_clock, memory_mb,
    memory_type, bus_width, bus_interface, bandwidth, tdp_watt, psu_watt, power_connectors,
    mem_freq_mhz, gpu_core, rop_blocks, pixel_rate, tmu_blocks, texture_rate, directx, is_new)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');

$fp = fopen($CSV, 'r');
if (!$fp) die("Не удалось открыть $CSV\n");
$header = fgetcsv($fp, 0, ';'); // заголовок не используем напрямую — порядок столбцов фиксирован
$yearNow = (int)date('Y');
$count = 0;
$pdo->beginTransaction();
while (($c = fgetcsv($fp, 0, ';')) !== false) {
    if (count($c) < 27 || trim($c[0]) === '') continue;
    $rel  = parseDate($c[5]);
    $year = $rel ? (int)substr($rel, 0, 4) : null;
    $sth->execute([
        trim($c[0]),                                  // name
        trim($c[1]),                                  // Videocard Name_perf
        num($c[2]),                                   // Passmark G3D Mark
        strtolower(trim($c[3])),                      // vendor (код)
        $VENDOR[strtolower(trim($c[3]))] ?? 'OTHER',  // brand
        trim($c[4]),                                  // arch_name
        $rel,                                         // release_date
        trim($c[6]),                                  // proc_process_size
        num($c[7]),                                   // cores
        num($c[8]),                                   // tensor_cores
        num($c[9]),                                   // rt_cores
        num($c[10]),                                  // base_clock
        num($c[11]),                                  // boost_clock
        ramMB($c[12], $c[13]),                        // ram (+ram_type) -> МБ
        trim($c[13]),                                 // ram_type
        num($c[14]),                                  // bus_width
        trim($c[15]),                                 // bus_interface
        bwToGBs($c[16]),                              // ram_bandwidth -> ГБ/с
        watt($c[17]),                                 // tdp
        watt($c[18]),                                 // suggested_psu
        trim($c[19]),                                 // power_connectors
        num($c[20]),                                  // Memory frequency
        trim($c[21]),                                 // nucleus
        num($c[22]),                                  // Rasterization blocks
        num($c[23]),                                  // pixelRate
        num($c[24]),                                  // Texture blocks
        num($c[25]),                                  // textureRate
        dxVer($c[26]),                                // directX
        ($year && $year >= $yearNow - 1) ? 1 : 0,     // is_new
    ]);
    $count++;
}
$pdo->commit();
fclose($fp);

echo "Готово: импортировано карточек — $count\n";
echo "Таблица: gpus (база $DB_NAME)\n";
