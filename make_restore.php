<?php
// restore.php — генерируется вместе с архивом; проверяет целостность файлов.
$files = ["config.php","import_to_mysql.php","gpu-row.php","check_db.php","index.php","bd.csv"];
$out = "<?php\n/* restore.php — восстановление index.php и bd.csv из base64-блоков.\n * Использование: скопируйте блоки в переменные ниже (или сохраните этот файл\n * рядом с 4 маленькими php-файлами, а блоки вставьте вместо заглушек).\n */\nerror_reporting(E_ALL); ini_set('display_errors','1');\n\n";
foreach (["index_b64" => "index.php", "bd_b64" => "bd.csv"] as $var => $fname) {
    $out .= "\$$var = <<<B64_\n" . base64_encode(gzcompress(file_get_contents($fname), 9)) . "\nB64_;\n";
}
$out .= '
function writeRestored(string $b64, string $target): void {
    $raw = @gzuncompress(base64_decode(preg_replace(\'/\s+/\', "", $b64)));
    if ($raw === false || strlen($raw) < 100) { die("Не удалось распаковать $target — блок повреждён (пропущены/добавлены символы).\\n"); }
    file_put_contents(__DIR__ . "/" . $target, $raw);
    echo "Записан $target (" . strlen($raw) . " байт)<br>";
}
writeRestored($index_b64, "index.php");
writeRestored($bd_b64, "bd.csv");
echo "<hr>Проверка:<br>";
foreach (["index.php","bd.csv"] as $f) {
    echo $f . ": " . (file_exists(__DIR__."/$f") ? filesize(__DIR__."/$f") . " байт, md5=" . md5_file(__DIR__."/$f") : "НЕТ") . "<br>";
}
';
file_put_contents("restore.php", $out);
echo "restore.php size: ".filesize("restore.php")."\n";
