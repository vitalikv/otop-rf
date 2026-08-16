<?php

require_once __DIR__ . '/../env.php';

/**
 * Настройки писем. Ссылки ведут в само приложение с query-параметрами: базу
 * берём из APP_BASE_URL, иначе из Origin (в разработке это http://localhost:3000
 * — заголовок доходит через прокси Vite), иначе из HTTP_HOST.
 *
 * В подпапке сайта Origin не годится: в нём только домен, а приложение живёт в
 * /auto_wf — ссылка из письма увела бы в корень сайта. Поэтому там APP_BASE_URL
 * задан явно (docs/deploy-otop-rf.md).
 */

function mail_app_base_url(): string
{
    $configured = trim(env_value('APP_BASE_URL'));
    if ($configured !== '') {
        return rtrim($configured, '/');
    }

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (is_string($origin) && $origin !== '') {
        return rtrim($origin, '/');
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return rtrim($scheme . '://' . $host, '/');
}

function mail_from_email(): string
{
    $configured = trim(env_value('MAIL_FROM_EMAIL'));
    if ($configured !== '') {
        return $configured;
    }

    $host = preg_replace('/:\d+$/', '', (string)($_SERVER['HTTP_HOST'] ?? 'localhost'));

    return 'no-reply@' . $host;
}

function mail_from_name(): string
{
    return trim(env_value('MAIL_FROM_NAME', 'Тёплый пол'));
}
