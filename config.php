<?php
/*
 * config.php — настройки подключения к базе данных.
 *
 * Open Server Panel (локально):  host=localhost, user=root, password='' (пусто),
 *                               база gpu_compare (создайте её в phpMyAdmin).
 *                               Если MySQL слушает только TCP, попробуйте 127.0.0.1.
 * Beget (на хостинге):           host=localhost, user и password — те, что вы
 *                               создали в панели Beget («MySQL» + «Добавить БД»),
 *                               имя базы обычно вида login_dbname.
 */

$DB_HOST = 'localhost';   // если не сработает — поставьте '127.0.0.1'
$DB_NAME = 'gpu_compare';        // на beget может быть, например, 'username_gpu'
$DB_USER = 'root';               // на beget — ваш пользователь MySQL
$DB_PASS = '';                   // на beget — пароль пользователя MySQL

/** Подключение к базе (PDO) или понятная ошибка без белого экрана. */
function db(): PDO
{
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
    static $pdo = null;
    if ($pdo === null) {
        // 'localhost' PHP может трактовать как сокет (unix socket), поэтому
        // при неудаче автоматически пробуем '127.0.0.1' по TCP/3306.
        $hosts = [$DB_HOST];
        if ($DB_HOST === 'localhost') $hosts[] = '127.0.0.1';
        elseif ($DB_HOST === '127.0.0.1') $hosts[] = 'localhost';
        $lastErr = null;
        foreach ($hosts as $h) {
            try {
                $pdo = new PDO("mysql:host=$h;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                break;
            } catch (PDOException $e) {
                $lastErr = $e;
            }
        }
        if ($pdo === null) {
            // Покажем место ошибки вместо пустой страницы (пароль не раскрываем)
            die('Не удалось подключиться к базе данных.<br><br>'
                . 'Сообщение MySQL: <b>' . htmlspecialchars($lastErr->getMessage()) . '</b><br><br>'
                . 'Чек-лист для Open Server Panel:<br>'
                . '1) Запущен модуль <b>MySQL/MariaDB</b> (иконка OSP → «Модули» → MySQL должен стоять флажок; статус — зелёный).<br>'
                . '2) База <b>' . htmlspecialchars($DB_NAME) . '</b> создана в phpMyAdmin (http://localhost/phpmyadmin).<br>'
                . '3) В <b>config.php</b> стоят host=localhost, user=root, password=\'\' (пусто).<br>'
                . '4) Если ошибка [2002] остаётся — порт 3306 занят другим MySQL или служба не стартовала: перезапустите OSP от имени администратора и проверьте, не установлена ли separately MySQL/Mariadb (XAMPP/WAMP) — она может мешать.<br>'
                . '5) После запуска MySQL выполните импорт: http://localhost/import_to_mysql.php');
        }
    }
    return $pdo;
}
