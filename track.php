<?php
/*
 * track.php — серверный счётчик популярности видеокарт.
 * Вызывается из JS (fetch POST id=N) каждый раз, когда посетитель добавляет
 * карту N в сравнение. Значения хранятся в таблице gpu_popularity (поле hits).
 */
require __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) { http_response_code(400); echo json_encode(['ok' => false]); exit; }

try {
    $pdo = db();
    // Таблица должна существовать (создаётся import_to_mysql.php); на всякий случай создаём.
    $pdo->exec('CREATE TABLE IF NOT EXISTS gpu_popularity (
        gpu_id INT PRIMARY KEY,
        hits   INT NOT NULL DEFAULT 0,
        CONSTRAINT fk_gpu FOREIGN KEY (gpu_id) REFERENCES gpus(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $sel = $pdo->prepare('SELECT id FROM gpus WHERE id = ?');
    $sel->execute([$id]);
    if (!$sel->fetchColumn()) { http_response_code(404); echo json_encode(['ok' => false]); exit; }

    $upd = $pdo->prepare('INSERT INTO gpu_popularity (gpu_id, hits) VALUES (?, 1)
                           ON DUPLICATE KEY UPDATE hits = hits + 1');
    $upd->execute([$id]);

    $cur = $pdo->prepare('SELECT hits FROM gpu_popularity WHERE gpu_id = ?');
    $cur->execute([$id]);
    echo json_encode(['ok' => true, 'hits' => (int)$cur->fetchColumn()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false]);
}
