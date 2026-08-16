<?php

require_once __DIR__ . '/env.php';

/**
 * Подключение к базе skeleton_wf. Хост, имя и учётка берутся из настроек —
 * на боевом сервере они другие, а править файл при выкладке не хочется.
 */

function db_config(): array
{
    return [
        'host' => env_value('DB_HOST', '127.0.0.1'),
        'port' => env_value('DB_PORT', '3306'),
        'name' => env_value('DB_NAME', 'skeleton_wf'),
        'user' => env_value('DB_USER', 'root'),
        'password' => env_value('DB_PASSWORD', ''),
        // Таблицы из схемы eng_1 лежат в utf8, а не utf8mb4 — соединение
        // держим в той же кодировке, иначе сравнение почты пойдёт по разным
        // правилам сортировки.
        'charset' => env_value('DB_CHARSET', 'utf8'),
    ];
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $cfg = db_config();
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $cfg['host'],
        $cfg['port'],
        $cfg['name'],
        $cfg['charset']
    );

    $pdo = new PDO($dsn, $cfg['user'], $cfg['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
