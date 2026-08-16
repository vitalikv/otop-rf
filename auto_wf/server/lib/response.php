<?php

/**
 * Общая обвязка JSON-ручек: заголовки, CORS, приём тела и формат ответов.
 *
 * Ошибка всегда выглядит как {"error": "...", "code": "..."}. Текст английский
 * и нужен только для отладки — пользователю показывается русская фраза,
 * подобранная клиентом по машинному коду.
 */

require_once __DIR__ . '/env.php';

function get_request_method(): string
{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    return is_string($method) && $method !== '' ? strtoupper($method) : 'GET';
}

function get_request_origin(): ?string
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? null;
    return is_string($origin) && $origin !== '' ? $origin : null;
}

function get_request_ip(): string
{
    $forwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if (is_string($forwardedFor) && $forwardedFor !== '') {
        $parts = explode(',', $forwardedFor);
        $candidate = trim($parts[0] ?? '');
        if ($candidate !== '') {
            return $candidate;
        }
    }

    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
    return is_string($remoteAddr) && $remoteAddr !== '' ? $remoteAddr : 'unknown';
}

function allowed_cors_origins(): array
{
    $configured = env_value('ALLOWED_ORIGINS');
    if ($configured !== '') {
        $origins = array_filter(array_map('trim', explode(',', $configured)));
        return array_values(array_unique($origins));
    }

    // Vite отдаёт приложение на localhost:3000 и проксирует /api/auth на этот
    // домен; прямые заходы на домен OpenServer тоже нужны для проверки ручек.
    return [
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://skeleton-wf',
        'https://skeleton-wf',
    ];
}

function is_allowed_cors_origin(string $origin): bool
{
    return in_array($origin, allowed_cors_origins(), true);
}

/**
 * Warning из PHP допишется в тело ответа перед JSON, и клиент упадёт на разборе
 * с невнятной ошибкой. Поэтому вывод ошибок в ответ выключен, а всё уходит в лог
 * рядом со счётчиками лимита.
 */
function init_error_handling(): void
{
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../storage/php-error.log');

    set_exception_handler(function ($error) {
        error_log('[auth-api] uncaught: ' . (string)$error);
        json_error('server_error', 'Internal server error', 500);
    });

    register_shutdown_function(function () {
        $last = error_get_last();
        if ($last === null || !in_array($last['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            return;
        }
        if (headers_sent()) {
            return;
        }
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Internal server error', 'code' => 'server_error']);
    });
}

/**
 * Сессионная cookie с SameSite=Lax от CSRF защищает, но это единственный слой.
 * Изменяющие запросы дополнительно сверяются с Sec-Fetch-Site и Origin: свой
 * запрос из браузера идёт как same-origin, чужая страница — как cross-site.
 */
function assert_same_origin_request(): void
{
    if (in_array(get_request_method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
        return;
    }

    $fetchSite = $_SERVER['HTTP_SEC_FETCH_SITE'] ?? null;
    if (is_string($fetchSite) && $fetchSite !== '' && !in_array($fetchSite, ['same-origin', 'same-site', 'none'], true)) {
        json_error('origin_not_allowed', 'Cross-site request is not allowed', 403);
    }

    $origin = get_request_origin();
    if ($origin !== null && !is_allowed_cors_origin($origin)) {
        json_error('origin_not_allowed', 'Origin is not allowed', 403);
    }
}

function init_json_api(array $allowedMethods): void
{
    init_error_handling();

    $origin = get_request_origin();

    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

    if ($origin !== null && is_allowed_cors_origin($origin)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Vary: Origin');
    }

    if (get_request_method() === 'OPTIONS') {
        if ($origin !== null && !is_allowed_cors_origin($origin)) {
            http_response_code(403);
            exit;
        }
        http_response_code(204);
        exit;
    }

    if ($origin !== null && !is_allowed_cors_origin($origin)) {
        json_error('origin_not_allowed', 'Origin is not allowed', 403);
    }

    if (!in_array(get_request_method(), $allowedMethods, true)) {
        json_error('method_not_allowed', 'Method not allowed', 405);
    }

    assert_same_origin_request();
}

function json_input(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function json_success(array $payload = [], int $status = 200): void
{
    json_response($payload, $status);
}

function json_error(string $code, string $message, int $status = 400, array $extra = []): void
{
    json_response(array_merge(['error' => $message, 'code' => $code], $extra), $status);
}
