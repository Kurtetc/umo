<?php
/* ТЕСТОВЫЙ файл (не для боевого сервера): проверяет gpu-row.php на данных из bd.csv,
 * эмулируя MySQL через SQLite. Запуск: php _test_render.php  -> печатает несколько строк таблицы. */
require __DIR__ . '/gpu-row.php';

function num($s) { $s = trim(str_replace([' ', "\xc2\xa0", ','], ['', '', '.'], (string)$s)); $s = preg_replace('/[^\d.\-]/', '', $s); return ($s === '' || $s === '-' || $s === '.') ? null : (float)$s; }
function parseDate($s) { $s = trim((string)$s); if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $s, $m)) return "{$m[3]}-{$m[2]}-{$m[1]}"; return null; }
function bwToGBs($s) { $s = strtolower(trim((string)$s)); if (preg_match('/^([\d.,]+)\s*(gb|tb)/', $s, $m)) { $v = num($m[1]); return $v === null ? null : round($m[2] === 'tb' ? $v * 1000 : $v, 3); } return null; }
function watt($s) { return num(preg_replace('/[^0-9.,]/', '', (string)$s)); }
function ramMB($ram, $type) { $v = num($ram); if ($v === null) return null; if (stripos((string)$type, 'shared') !== false) return 0; return (int)round($v < 16 ? $v * 1024 : $v); }
function dxVer($s) { $s = trim(str_replace(',', '.', (string)$s)); return $s === '' ? null : $s; }
$VENDOR = ['nvd' => 'NVIDIA', 'nvidia' => 'NVIDIA', 'amd' => 'AMD', 'int' => 'INTEL'];

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('CREATE TABLE gpus (name TEXT, short_name TEXT, perf_score INT, vendor_code TEXT, brand TEXT, arch_name TEXT, release_date TEXT, process_size TEXT, cores INT, tensor_cores INT, rt_cores INT, base_clock INT, boost_clock INT, memory_mb INT, memory_type TEXT, bus_width INT, bus_interface TEXT, bandwidth REAL, tdp_watt INT, psu_watt INT, power_connectors TEXT, mem_freq_mhz INT, gpu_core TEXT, rop_blocks INT, pixel_rate REAL, tmu_blocks INT, texture_rate REAL, directx TEXT, is_new INT)');
$sth = $pdo->prepare('INSERT INTO gpus VALUES (' . rtrim(str_repeat('?,', 29), ',') . ')');

$fp = fopen(__DIR__ . '/bd.csv', 'r');
fgetcsv($fp, 0, ';');
$yearNow = (int)date('Y');
$n = 0;
while (($c = fgetcsv($fp, 0, ';')) !== false) {
    if (count($c) < 27 || trim($c[0]) === '') continue;
    $rel = parseDate($c[5]);
    $year = $rel ? (int)substr($rel, 0, 4) : null;
    $sth->execute([trim($c[0]), trim($c[1]), num($c[2]), strtolower(trim($c[3])),
        $VENDOR[strtolower(trim($c[3]))] ?? 'OTHER', trim($c[4]), $rel, trim($c[6]),
        num($c[7]), num($c[8]), num($c[9]), num($c[10]), num($c[11]), ramMB($c[12], $c[13]),
        trim($c[13]), num($c[14]), trim($c[15]), bwToGBs($c[16]), watt($c[17]), watt($c[18]),
        trim($c[19]), num($c[20]), trim($c[21]), num($c[22]), num($c[23]), num($c[24]), num($c[25]),
        dxVer($c[26]), ($year && $year >= $yearNow - 1) ? 1 : 0]);
    $n++;
}
fclose($fp);
echo "Импортировано строк: $n\n";

$rows = $pdo->query('SELECT * FROM gpus ORDER BY release_date IS NULL, release_date DESC, name')->fetchAll(PDO::FETCH_ASSOC);
echo "Прочитано из «БД»: " . count($rows) . "\n\n=== Примеры строк ===\n";
foreach ($rows as $r) {
    if (in_array(trim($r['short_name']), ['GeForce RTX 5090', 'Radeon RX 9070 XT', 'Intel Arc B580', 'GeForce GTX 285', 'Radeon HD 4870', 'Intel HD Graphics 3000 Mobile'])) {
        echo renderGpuRow($r) . "\n\n";
    }
}
// проверка уникальности имён для JS-поиска по имени
$names = array_map(fn($r) => trim($r['short_name']), $rows);
$dups = array_count_values($names);
$dups = array_filter($dups, fn($v) => $v > 1);
echo "Дублирующихся отображаемых имён (важно для JS): " . count($dups) . "\n";
echo "Примеры дублей: " . implode(', ', array_slice(array_keys($dups), 0, 8)) . "\n";
