<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth/auth.php';

/**
 * Отдаёт чанк приложения вошедшему. Гостю кода редактора не достаётся вовсе:
 * файлы лежат вне корня домена, в server/private/ui, а сюда их запрашивают по
 * прежнему адресу /assets/gated-<hash>.js — правило в корневом .htaccess
 * перехватывает его. Прежний URL важен: внутри чанка есть относительные
 * импорты соседних файлов (docs/auth-gate-plan.md).
 *
 * init_json_api() здесь не зовут: она ставит заголовки JSON, а ответ — скрипт.
 */

init_error_handling();

$chunkDir = __DIR__ . '/../../private/ui';

function chunk_deny(int $status): void
{
    http_response_code($status);
    header('Cache-Control: no-store');
    exit;
}

if (!in_array(get_request_method(), ['GET', 'HEAD'], true)) {
    chunk_deny(405);
}

// Имя берётся из пути, а не из параметра запроса: параметр клиент дописал бы
// сам поверх нашего, а путь переписан правилом .htaccess и подделке не
// поддаётся. Регулярка заодно отсекает переходы по каталогам.
$path = parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
$name = is_string($path) ? basename($path) : '';

if (preg_match('/^gated-[A-Za-z0-9_-]+\.js$/', $name) !== 1) {
    chunk_deny(404);
}

try {
    $user = auth_current_user();
} catch (PDOException $e) {
    error_log('[ui-chunk] session: ' . $e->getMessage());
    chunk_deny(500);
}

if ($user === null) {
    chunk_deny(401);
}

$file = $chunkDir . '/' . $name;

if (!is_file($file)) {
    chunk_deny(404);
}

// Чужому кешу чанк не достаётся: приватный кеш браузера отдаёт его только этой
// же сессии. Если повторная загрузка бандла при каждом входе станет мешать,
// сюда добавляется ETag с max-age — гостю ручка всё равно ответит 401.
header('Content-Type: text/javascript; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Content-Length: ' . (string)filesize($file));

if (get_request_method() === 'HEAD') {
    exit;
}

readfile($file);
