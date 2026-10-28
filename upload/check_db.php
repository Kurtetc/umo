<?php
/*
 * check_db.php — диагностика подключения к MySQL для Open Server Panel.
 * Откройте в браузере: http://localhost/check_db.php
 * Он покажет: какой PHP, какие расширения есть, по каким адресам реально
 * отвечает MySQL (в OSP это может быть 127.0.0.1, а не localhost!) и попробует
 * подключиться к базе gpu_compare по каждому адресу.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo '<pre>';
echo "PHP: " . PHP_VERSION . "\n";
echo "Расширение pdo_mysql: " . (extension_loaded('pdo_mysql') ? 'ЕСТЬ' : 'НЕТ!!! включите в OSP: Конфигурация -> PHP -> extensions') . "\n\n";

// --- Какие адреса вообще слушают MySQL-порты на машине ---
echo "Проверяем TCP-порты 3306 / 33060 на разных адресах:\n";
$candidates = ['127.0.0.1', 'localhost', '127.0.1.31', '::1'];
$openPorts = [];
foreach ($candidates as $host) {
    foreach ([3306, 33060] as $port) {
        $fp = @fsockopen($host, $port, $errno, $errstr, 1);
        if ($fp) {
            echo "  OK  : $host:$port отвечает\n";
            fclose($fp);
            if ($port === 3306) $openPorts[] = $host;
        } else {
            echo "  fail: $host:$port — $errstr\n";
        }
    }
}
if (!$openPorts) {
    echo "\nПорт 3306 ни на одном адресе не отвечает => MySQL в OSP НЕ запущен\n"
       . "(или висит, или порт изменён). Перезапустите OSP от администратора,\n"
       . "включите модуль MySQL и проверьте лог: OSP -> Логи -> mysql-error.log\n";
}

// --- Пробуем подключиться PDO к каждому отвечающему адресу ---
require __DIR__ . '/config.php'; // берём DB_USER / DB_PASS из config.php

// Набор пар «логин/пароль», которые стоит попробовать (Open Server по умолчанию
// ставит root с пустым паролем, но иногда пароль бывает задан).
$creds = [
    ['root', ''],                 // стандарт Open Server Panel
    ['root', $DB_PASS],            // то, что сейчас в config.php
];
if ($DB_USER !== 'root') array_unshift($creds, [$DB_USER, $DB_PASS]);

$hostsToTry = $openPorts ?: [$DB_HOST, '127.0.0.1', 'localhost'];
$good = null; // [host, user, pass]
foreach (array_unique($hostsToTry) as $h) {
    foreach ($creds as [$u, $p]) {
        $tag = "$u@" . ($p === '' ? '(без пароля)' : '(с паролем)');
        try {
            $pdo = new PDO("mysql:host=$h;charset=utf8mb4", $u, $p,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $ver = $pdo->query('SELECT VERSION()')->fetchColumn();
            $dbs = $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
            echo "  OK  : $h под логином $tag — MySQL $ver\n";
            echo "        Базы: " . implode(', ', $dbs) . "\n";
            $has = in_array($DB_NAME, $dbs);
            echo "        База '$DB_NAME' " . ($has ? 'ЕСТЬ' : 'НЕТ') . "\n";
            if (!$has) {
                $pdo->exec("CREATE DATABASE `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                echo "        Создал базу '$DB_NAME' автоматически.\n";
            }
            $good = [$h, $u, $p];
            break 2;
        } catch (PDOException $e) {
            echo "  fail: $h / $tag — " . $e->getMessage() . "\n";
        }
    }
}

if ($good) {
    echo "\n==> Рабочие настройки: host='{$good[0]}', user='{$good[1]}', password='" . $good[2] . "'.\n";
    if ($good[0] !== $DB_HOST || $good[1] !== $DB_USER || $good[2] !== $DB_PASS) {
        echo "    В config.php сейчас другие значения — замените их на указанные выше.\n";
    } else {
        echo "    Config.php уже настроен правильно ✔\n";
    }
    echo "Теперь запустите импорт: http://localhost/import_to_mysql.php\n";
} else {
    echo "\nНи одна комбинация логина/пароля не подошла. Откройте phpMyAdmin\n";
    echo "(http://localhost/phpmyadmin) — там вы точно видите, какой пароль работает,\n";
    echo "и пропишите его в config.php (\$DB_PASS).\n";
}
echo '</pre>';
