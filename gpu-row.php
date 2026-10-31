<?php
/*
 * gpu-row.php — один шаблон строки таблицы «Видеокарта».
 *
 * Это ЕДИНСТВЕННОЕ место, где разметка строки совпадает с дизайном umo-v26.html.
 * Здесь же описано соответствие: столбцы базы данных -> ячейки таблицы сайта.
 * Если захотите поменять, какие данные куда подставляться, правьте только здесь.
 *
 * Вызывается из umo-v26.php (renderGpuRow()) и из gpus.json (formatRow()).
 */

/** Экранирование для HTML (данные приходят из базы). */
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/** Число с разделителем тысяч пробелом: 21760 -> "21 760". */
function fmtInt($n) {
    if ($n === null || $n === '' || !is_numeric($n)) return null;
    return number_format((float)$n, 0, ',', ' ');
}

/** Число с запятой и разделителем тысяч: 1636.8 -> "1 636,8". */
function fmtNum($n, $dec = 1) {
    if ($n === null || $n === '' || !is_numeric($n)) return null;
    return number_format((float)$n, $dec, ',', ' ');
}

/** Заглавные буквы в начале слов: "GT200b" -> "GT200B", "BMG-G21" без изменений. */
function capTokens($s) {
    $s = trim((string)$s);
    return preg_replace_callback('/\b([a-z])(?=[a-z]*\d)/', fn($m) => strtoupper($m[1]), $s);
}

/** Месяц по номеру -> русская аббревиатура. */
function monthRu($m) {
    $a = [1=>'янв',2=>'фев',3=>'мар',4=>'апр',5=>'май',6=>'июн',7=>'июл',8=>'авг',9=>'сен',10=>'окт',11=>'ноя',12=>'дек'];
    return $a[(int)$m] ?? '';
}

/**
 * Короткая «метка» на значке видеокарты (RTX/GTX/RX/ARC...).
 * $full — полное имя из базы (name), $short — отображаемое имя (Videocard Name_perf).
 */
function gpuMark($brand, $full, $short) {
    $s = strtoupper(trim((string)$short));
    // 1) Метка берётся из ФАКТИЧЕСКОГО названия (name): GeForce RTX 5090 -> RTX и т.п.
    $f = trim((string)$full);
    if (preg_match('/\b(RTX|GTX)\b/i', $f, $m)) return strtoupper($m[1]);
    if (preg_match('/\bMXM?\b/i', $s, $m) && preg_match('/\bMX\b/i', $f)) return 'MX';
    if (preg_match('/\b(GT)\s+\d/i', $f, $m)) return 'GT';
    if (preg_match('/^\s*geforce\s+(\d{3,4})/i', $f, $m)) return $m[1];        // GeForce 8800 GT -> "8800"
    if (preg_match('/\b(ARC)\b/i', $f, $m)) return 'ARC';
    if (preg_match('/\b(Radeon)\s*(HD|RX|Pro|VEGA)?\s*([\w-]*)/i', $f, $m)) {
        if (!empty($m[2])) return strtoupper($m[2]);                            // HD / RX / PRO / VEGA
        if (!empty($m[3]) && ctype_digit($m[3])) return strtoupper($m[3]);      // Radeon 625 -> "625"
        return 'R';                                                             // Radeon R9... -> R9 (см. ниже)
    }
    if (preg_match('/\b(R\d+(?:X|XM|GB|MB)?)\b/i', $f, $m)) return strtoupper($m[1]); // Radeon R9 285
    if (preg_match('/\b(UHD|Iris Xe|Iris Pro|Iris|HD|GMA)\b/i', $f, $m)) {
        $t = strtoupper($m[1]); return $t === 'IRIS XE' ? 'XE' : $t;             // Intel UHD -> UHD
    }
    if (preg_match('/^(FirePro|FireStream|Quadro|NVS|Tesla|Titan|CMP|TITAN)\b/i', $f, $m)) return strtoupper($m[1]);
    // 2) Запасные варианты по короткому имени
    if (preg_match('/^(RTX|GTX|GT|MX|NVS|QUADRO|TESLA|TITAN|CMP|ARC)/', $s, $m)) return $m[1];
    if ($brand === 'NVIDIA') return 'NV';
    if ($brand === 'AMD')    return 'AMD';
    if ($brand === 'INTEL')  return 'INTEL';
    return mb_substr($s, 0, 4);
}

/** Цвет класса вендора для имени и значка. */
function brandClass($brand) {
    switch ($brand) {
        case 'NVIDIA': return 'nvidia';
        case 'AMD':    return 'amd';
        case 'INTEL':  return 'intel';
        default:       return 'other';
    }
}

/**
 * Собрать строку таблицы из записи базы данных $g (ассоциативный массив).
 *
 * СООТВЕТСТВИЕ СТОЛБЦОВ БАЗЫ -> ЯЧЕЙКА ТАБЛИЦЫ (порядок ячеек = порядок <th> вUMO):
 *  [0] td.remove-cell      — пусто (JS добавит кнопки «убрать/избранное»)
 *  [1] Видеокарта          — short_name + "brand · arch_name" + метка значка
 *  [2] Релиз               — release_date ("30.01.2025" -> "янв 2025")
 *  [3] Техпроцесс / ядро   — process_size / gpu_core
 *  [4] Частота ядра        — base_clock / boost_clock
 *  [4] Шейдеры / ROP / TMU — cores / rop_blocks / tmu_blocks
 *  [5] GPixel/s / GTexel/s — pixel_rate / texture_rate
 *  [6] Тип памяти/объём/частота — memory_type / memory_mb / mem_freq_mhz
 *  [7] Шина / пропускная   — bus_width / bandwidth
 *  [8] TDP / реком. БП     — tdp_watt / psu_watt
 *  [9] Тензор./RT-ядра     — tensor_cores / rt_cores
 *  [10] Вендор / ядро      — brand / gpu_core
 *  [11] Интерфейс / DirectX— bus_interface / directx
 * Атрибуты строки: data-brand, data-year, data-new, data-perf (Passmark G3D Mark).
 */
function renderGpuRow(array $g): string {
    $brand = $g['brand'] ?? 'OTHER';
    $cls   = brandClass($brand);
    $short = trim($g['short_name'] ?: $g['name']);
    $full  = $g['name'];

    // --- Релиз: "янв 2025" + data-year ---
    $year = null; $relTxt = null;
    if (!empty($g['release_date'])) {
        $ts = strtotime($g['release_date']);
        if ($ts) {
            $year   = (int)date('Y', $ts);
            $relTxt = monthRu((int)date('n', $ts)) . ' ' . $year;
        }
    }

    // --- Техпроцесс / название ядра ---
    $proc = trim((string)($g['process_size'] ?? ''));
    $coreN = capTokens(trim((string)($g['gpu_core'] ?? '')));
    $procCore = implode(' · ', array_filter([$proc ?: null, $coreN ?: null])) ?: '—';

    // --- Частота ядра: "2 017 / 2 407" или одна цифра ---
    $bc = fmtInt($g['base_clock'] ?? null);
    $bo = fmtInt($g['boost_clock'] ?? null);
    $clocks = $bc !== null && $bo !== null ? "$bc / $bo" : ($bc ?? $bo);

    // --- Шейдеры / ROP / TMU ---
    $parts = array_filter([$bc!==null||true?'':''], 'strlen'); // не используется
    $shaders = implode(' / ', array_map(fn($v) => $v ?? '—', [
        fmtInt($g['cores'] ?? null), fmtInt($g['rop_blocks'] ?? null), fmtInt($g['tmu_blocks'] ?? null),
    ]));
    $shadersAllNull = ($g['cores'] ?? null) === null && ($g['rop_blocks'] ?? null) === null && ($g['tmu_blocks'] ?? null) === null;
    if ($shadersAllNull) $shaders = '—';

    // --- GPixel/s / GTexel/s ---
    $px = fmtNum($g['pixel_rate'] ?? null);
    $tx = fmtNum($g['texture_rate'] ?? null);
    $rates = (($px ?? $tx) !== null) ? ($px ?? '—') . ' / ' . ($tx ?? '—') : '—';

    // --- Тип памяти / объём / частота ---
    $mt = trim((string)($g['memory_type'] ?? ''));
    $mb = $g['memory_mb'] ?? null;
    $memSize = null;
    if ($mb !== null && $mb !== '') {
        $mb = (int)$mb;
        if ($mb === 0) $memSize = 'разделённая';
        elseif ($mb >= 1024) {
            $gb = $mb / 1024;
            $memSize = rtrim(rtrim(number_format($gb, 2, ',', ''), '0'), ',') . ' ГБ';
        } else $memSize = fmtInt($mb) . ' МБ';
    }
    $mf = fmtInt($g['mem_freq_mhz'] ?? null);
    $memFreq = null;
    if ($mf !== null) {
        $mfv = (float)str_replace([' ', "\xc2\xa0"], '', $mf);
        // Эффективные частоты памяти кратны 8 или 1000 -> показываем в Гбит/с (как в дизайне), иначе — МГц
        $isEffective = $mfv >= 1600 && (fmod($mfv, 8) < 0.001 || fmod($mfv, 1000) < 0.001);
        $memFreq = $isEffective
            ? rtrim(rtrim(number_format($mfv / 1000, 2, ',', ''), '0'), ',') . ' Гбит/с'
            : $mf . ' МГц';
    }
    $memCell = implode(' / ', array_filter([$mt ?: null, $memSize, $memFreq], fn($v)=>$v!==null&&$v!=='')) ?: '—';

    // --- Шина / пропускная способность ---
    $bw = fmtInt($g['bus_width'] ?? null);
    $bd = $g['bandwidth'] ?? null;
    $bdTxt = null;
    if ($bd !== null && $bd !== '' && (float)$bd > 0) {
        // как в дизайне: всегда ГБ/с с разделителем тысяч ("1 792 ГБ/с")
        $dec = fmod((float)$bd, 1) == 0 ? 0 : 1;
        $bdTxt = number_format((float)$bd, $dec, ',', ' ') . ' ГБ/с';
    }
    $busCell = (($bw ?? $bdTxt) !== null) ? ($bw !== null ? "$bw бит" : '—') . ' / ' . ($bdTxt ?? '—') : '—';

    // --- TDP / рекомендуемый БП ---
    $tdp = fmtInt($g['tdp_watt'] ?? null);
    $psu = fmtInt($g['psu_watt'] ?? null);
    if ($tdp !== null || $psu !== null) {
        if ($tdp !== null && $psu !== null) $powerCell = "$tdp Вт / $psu Вт";
        elseif ($tdp !== null) $powerCell = "$tdp Вт";
        else $powerCell = "— Вт / $psu Вт";
    } else $powerCell = '—';

    // --- Тензорные / RT-ядра (у AMD — AI, у Intel — XMX) ---
    $tc = fmtInt($g['tensor_cores'] ?? null);
    $rc = fmtInt($g['rt_cores'] ?? null);
    // как в дизайне: у NVIDIA просто "680 / 170", у AMD "128 AI / 64 RT", у Intel "160 XMX / 20 RT"
    $suffix = $brand === 'AMD' ? ' AI' : ($brand === 'INTEL' ? ' XMX' : '');
    $rtSuffix = $brand === 'NVIDIA' ? '' : ' RT';
    if ($tc !== null || $rc !== null) {
        $tensorCell = $tc !== null ? ($tc . $suffix . ' / ' . ($rc !== null ? $rc . $rtSuffix : '—'))
                                  : ('— / ' . $rc . $rtSuffix);
    } else $tensorCell = '—';

    // --- Вендор / название ядра ---
    $vendorCore = implode(' / ', array_filter([
        $brand === 'OTHER' ? null : $brand,
        trim((string)($g['gpu_core'] ?? '')) ?: null,
    ])) ?: '—';
    $vendorCore = preg_replace_callback('/\/\s*(.+)$/u', fn($m) => '/ ' . capTokens($m[1]), $vendorCore);

    // --- Интерфейс / DirectX ---
    $iface = trim((string)($g['bus_interface'] ?? ''));
    $dx = trim((string)($g['directx'] ?? ''));
    $dxTxt = $dx !== '' ? 'DX ' . $dx : null;
    $ifaceCell = implode(' / ', array_filter([$iface ?: null, $dxTxt], fn($v)=>$v!==null)) ?: '—';

    // --- Архитектура в подписи имени: "RDNA 4.0" -> "RDNA 4", "Blackwell 2.0" -> "Blackwell" ---
    $arch = trim((string)($g['arch_name'] ?? ''));
    if ($brand === 'NVIDIA') {                                // имена архитектур NVIDIA без версии
        $arch = preg_replace('/\s+\d[\d.]*$/', '', $arch);   // "Turing 2.0" -> "Turing", "Maxwell 1.0" -> "Maxwell"
    } else {                                                  // AMD/Intel: убираем только хвост ".0"
        $arch = preg_replace('/\.0$/', '', $arch);            // "RDNA 4.0" -> "RDNA 4", "GCN 5.1" без изменений
    }
    $subSmall = $brand === 'OTHER' ? $arch : trim("$brand · $arch", ' ·');

    $isNew = !empty($g['is_new']) ? 'yes' : 'no';
    $perf  = isset($g['perf_score']) && $g['perf_score'] !== null && $g['perf_score'] !== '' ? (int)$g['perf_score'] : 0;
    $mark  = gpuMark($brand, $full, $short);

    $dataAttrs = 'data-brand="' . e($brand) . '"'
        . ' data-year="' . ($year !== null ? (int)$year : '') . '"'
        . ' data-new="' . $isNew . '"'
        . ' data-perf="' . $perf . '"'
        . ' data-id="' . (isset($g['id']) ? (int)$g['id'] : 0) . '"'
        . ' data-popularity="' . (isset($g['popularity']) ? (int)$g['popularity'] : 0) . '"';

    return '<tr data-catalog="1" ' . $dataAttrs . '>'
        . '<td class="remove-cell"></td>'
        . '<td><div class="gpu-name"><span class="gpu-mark ' . $cls . '-mark">' . e($mark) . '</span>'
          . '<span><span class="brand-name ' . $cls . '">' . e($short) . '</span>'
          . '<small>' . e($subSmall) . '</small></span></div></td>'
        . '<td>' . e($relTxt ?? '—') . '</td>'
        . '<td>' . e($procCore) . '</td>'
        . '<td>' . e($clocks ?? '—') . '</td>'
        . '<td>' . e($shaders) . '</td>'
        . '<td>' . e($rates) . '</td>'
        . '<td>' . e($memCell) . '</td>'
        . '<td>' . e($busCell) . '</td>'
        . '<td>' . e($powerCell) . '</td>'
        . '<td>' . e($tensorCell) . '</td>'
        . '<td>' . e($vendorCore) . '</td>'
        . '<td>' . e($ifaceCell) . '</td>'
        . '</tr>';
}

/** Все строки каталога сразу (скрыты через CSS по атрибуту data-catalog="1"). */
function renderGpuRows(array $gpus): string {
    $out = '';
    foreach ($gpus as $g) $out .= renderGpuRow($g);
    return $out;
}
