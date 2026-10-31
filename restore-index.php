<?php
/*
 * restore-index.php — ВРЕМЕННЫЙ скрипт восстановления index.php из base64.
 * Как использовать:
 *  1) Сохраните этот файл в корень сайта (C:\OSPanel\home\mysite.ru).
 *  2) Создайте рядом файл index.b64 и вставьте в НЕГО все 4 части base64
 *     из чата ПОДРЯД (одной строкой или несколько строк — неважно, пробелы
 *     и переносы игнорируются). Файл должен содержать ТОЛЬКО base64-текст.
 *  3) Откройте в браузере http://localhost/restore-index.php
 *     -> напишет «OK ...» и размер файла. Проверьте сайт (Ctrl+F5).
 *  4) УДАЛИТЕ restore-index.php и index.b64 с сервера.
 */
$b64file = __DIR__ . '/index.b64';
if (!is_file($b64file)) die('Не найден index.b64 рядом с этим скриптом. Создайте его и вставьте туда части base64 из чата.');
$data = preg_replace('/\s+/', '', file_get_contents($b64file));
$bin = base64_decode($data, true);
if ($bin === false) die('base64 повреждён — что-то потерялось при копировании. Скопируйте части заново, без пропусков.');
if (strlen($bin) !== 31974) {
    echo 'ВНИМАНИЕ: размер '.strlen($bin).' байт, ожидалось 31974. Похоже, часть потерялась. Записываю как есть — проверьте php -l / белый экран.<br>';
}
file_put_contents(__DIR__ . '/index.php', $bin);
echo 'OK: записан index.php ('.strlen($bin).' байт). Обновите сайт Ctrl+F5 и удалите restore-index.php + index.b64.';
