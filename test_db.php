<?php
echo "<h3>Диагностика MySQL</h3>";

// 1. Проверяем, видит ли PHP расширения
echo "PDO: " . (extension_loaded('pdo') ? '✅' : '❌') . "<br>";
echo "pdo_mysql: " . (extension_loaded('pdo_mysql') ? '✅' : '❌') . "<br>";
echo "mysqli: " . (extension_loaded('mysqli') ? '✅' : '❌') . "<br><hr>";

// 2. Пробуем разные варианты подключения
$variants = [
    ['host' => '127.0.0.1', 'port' => 3306, 'user' => 'root', 'pass' => ''],
    ['host' => '127.0.0.1', 'port' => 3306, 'user' => 'root', 'pass' => 'root'],
    ['host' => 'localhost', 'port' => 3306, 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'port' => 3306, 'user' => 'root', 'pass' => 'root'],
    ['host' => '127.0.0.1', 'port' => 3307, 'user' => 'root', 'pass' => ''],
];

foreach ($variants as $i => $v) {
    try {
        $dsn = "mysql:host={$v['host']};port={$v['port']};charset=utf8mb4";
        new PDO($dsn, $v['user'], $v['pass']);
        echo "✅ Вариант #" . ($i+1) . " РАБОТАЕТ: host={$v['host']}, port={$v['port']}, pass='" . ($v['pass']?:'(пусто)') . "'<br>";
    } catch (PDOException $e) {
        echo "❌ Вариант #" . ($i+1) . ": " . $e->getMessage() . "<br>";
    }
}