<?php

require_once __DIR__ . '/../env.php';

/**
 * Сессия авторизации. Хранит только id пользователя и короткую метку от текущего
 * хеша пароля: по ней смена пароля обесценивает все ранее выданные сессии —
 * иначе сброс пароля не выгонял бы того, кто увёл доступ.
 */

const AUTH_SESSION_NAME = 'skeleton_wf_session';

/**
 * Путь куки. На своём домене это корень, а в подпапке чужого сайта — каталог
 * приложения: иначе кука уходит с каждым запросом к страницам сайта, к его
 * картинкам и калькуляторам. Имя куки уникально, так что дело не в конфликте, а
 * в том, что рассылать её незачем (docs/deploy-otop-rf.md).
 */
function auth_session_cookie_path(): string
{
    return env_value('APP_COOKIE_PATH', '/');
}

function is_https_request(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }

    $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
    return is_string($forwardedProto) && strtolower($forwardedProto) === 'https';
}

function auth_session_configure(): void
{
    session_name(AUTH_SESSION_NAME);

    $secure = is_https_request();
    $path = auth_session_cookie_path();

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => $path,
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        return;
    }

    // До PHP 7.3 отдельного параметра samesite нет, и его дописывают в путь —
    // в заголовок Set-Cookie строка попадает как есть.
    session_set_cookie_params(0, $path . '; samesite=Lax', '', $secure, true);
}

/**
 * @param bool $create false — не заводить сессию для гостя: каждый визит
 *                     незалогиненного посетителя иначе создаёт файл в temp.
 */
function auth_session_start(bool $create = true): bool
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return true;
    }

    if (!$create && !isset($_COOKIE[AUTH_SESSION_NAME])) {
        return false;
    }

    auth_session_configure();
    session_start();

    return true;
}

/**
 * Читает сессию и сразу отпускает её файл: до конца запроса PHP держит его
 * заблокированным, и параллельные запросы одного пользователя встают в очередь.
 *
 * @return array|null ['user_id' => int, 'mark' => string|null]
 */
function auth_session_read(): ?array
{
    if (!auth_session_start(false)) {
        return null;
    }

    $userId = $_SESSION['user_id'] ?? null;
    $mark = $_SESSION['pass_mark'] ?? null;

    session_write_close();

    if (is_string($userId) && ctype_digit($userId)) {
        $userId = (int)$userId;
    }
    if (!is_int($userId) || $userId <= 0) {
        return null;
    }

    return [
        'user_id' => $userId,
        'mark' => is_string($mark) ? $mark : null,
    ];
}

function auth_password_mark(string $passwordHash): string
{
    return substr(hash('sha256', $passwordHash), 0, 16);
}

function auth_login_user(int $userId, string $passwordHash): void
{
    auth_session_start();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['pass_mark'] = auth_password_mark($passwordHash);
    session_write_close();
}

function auth_logout_user(): void
{
    if (!auth_session_start(false)) {
        return;
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            (bool)$params['secure'],
            (bool)$params['httponly']
        );
    }

    session_destroy();
}
