<?php
/*
 * config.php — настройки подключения к базе данных.
 *
 * Open Server Panel (локально):  host=localhost, user=root, password='' (пусто),
 *                               база gpu_compare (создайте её в phpMyAdmin).
 * Beget (на хостинге):           host=localhost, user и password — те, что вы
 *                               создали в панели Beget («MySQL» + «Добавить БД»),
 *                               имя базы обычно вида login_dbname.
 */

$DB_HOST = 'localhost';
$DB_NAME = 'gpu_compare';        // на beget может быть, например, 'username_gpu'
$DB_USER = 'root';               // на beget — ваш пользователь MySQL
$DB_PASS = '';                   // на beget — пароль пользователя MySQL

/** Подключение к базе (PDO) или понятная ошибка без белого экрана. */
function db(): PDO
{
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // Покажем место ошибки вместо пустой страницы (пароль не раскрываем)
            die('Не удалось подключиться к базе данных. Проверьте настройки в config.php. Детали: '
                . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}
