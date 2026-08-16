<?php

/**
 * Настройки серверной части: база, адрес приложения, разрешённые источники,
 * почта. Раньше их читали три одинаковые обёртки над getenv() — по одной в
 * db.php, mail/config.php и response.php.
 *
 * На собственном домене этого хватало: дефолты совпадали с локальной
 * установкой. В подпапке чужого сайта настройки нужны обязательно (иначе POST
 * упираются в 403, а ссылки в письмах ведут в корень сайта), и задавать их
 * через окружение Apache — значит зависеть от того, как собран PHP на сервере:
 * под FastCGI SetEnv доходит не всегда.
 *
 * Поэтому первым источником стал config.local.php рядом с кодом — он лежит
 * внутри каталога приложения, переезжает вместе с ним и переживает выкладку
 * (docs/deploy-otop-rf.md). Окружение остаётся вторым источником: на серверах,
 * где переменные задают снаружи, ничего менять не придётся.
 *
 * Один и тот же каталог приложения живёт на нескольких хостах: локальный
 * otop-rf под OpenServer и боевой домен, где у MySQL свой пароль. Поэтому в
 * конфиге есть секция 'hosts' — набор переопределений на имя хоста, из которого
 * открыто приложение (по образцу otop-rf/include/bd.php, где пароль базы точно
 * так же выбирается по $_SERVER['SERVER_NAME']). Общие ключи лежат на верхнем
 * уровне, различия — в секции; файл получается один на все установки, и
 * выкладка сводится к копированию каталога.
 */

/**
 * Имя хоста, на котором открыто приложение, — в нижнем регистре и без порта.
 *
 * SERVER_NAME впереди HTTP_HOST сознательно: заголовок Host приходит от
 * клиента, и по нему можно было бы выбрать чужой профиль настроек (например
 * подсунуть локальный, где база без пароля). SERVER_NAME задаётся конфигурацией
 * веб-сервера; при UseCanonicalName Off Apache всё равно берёт его из Host, но
 * администратор может это зафиксировать, а обратного пути нет.
 *
 * @return string Пустая строка в CLI — там профиль хоста не применяется.
 */
function env_host(): string
{
    foreach (['SERVER_NAME', 'HTTP_HOST'] as $key) {
        $value = $_SERVER[$key] ?? '';
        if (!is_string($value) || trim($value) === '') {
            continue;
        }

        $host = strtolower(trim($value));

        // IPv6 приходит как [::1]:80 — двоеточия внутри скобок не порт.
        if ($host[0] !== '[') {
            $colon = strrpos($host, ':');
            if ($colon !== false) {
                $host = substr($host, 0, $colon);
            }
        }

        if ($host !== '') {
            return $host;
        }
    }

    return '';
}

/**
 * Ключ секции 'hosts' — одно имя хоста или несколько через запятую.
 */
function env_host_matches(string $pattern, string $host): bool
{
    if ($host === '') {
        return false;
    }

    foreach (explode(',', $pattern) as $name) {
        if (strtolower(trim($name)) === $host) {
            return true;
        }
    }

    return false;
}

/**
 * @return array<string, scalar> Настройки из config.local.php с наложенным
 *                               профилем текущего хоста; пустой массив, если
 *                               файла нет — это нормально в разработке.
 */
function env_values(): array
{
    static $values = null;

    if ($values !== null) {
        return $values;
    }

    $file = __DIR__ . '/../config.local.php';
    $loaded = is_file($file) ? require $file : [];
    $loaded = is_array($loaded) ? $loaded : [];

    $hosts = $loaded['hosts'] ?? null;
    unset($loaded['hosts']);

    $values = $loaded;

    if (is_array($hosts)) {
        $host = env_host();
        foreach ($hosts as $pattern => $overrides) {
            if (is_array($overrides) && env_host_matches((string)$pattern, $host)) {
                $values = array_merge($values, $overrides);
            }
        }
    }

    return $values;
}

/**
 * Порядок источников: config.local.php → окружение → дефолт.
 *
 * Пустое значение считается незаданным — и в файле, и в окружении. Иначе
 * забытая строка `'DB_NAME' => ''` увела бы подключение в базу без имени с
 * невнятной ошибкой вместо явного дефолта.
 *
 * Отсюда же правило для профилей хостов: пустая строка в профиле не возвращает
 * общее значение, а сбрасывает ключ в дефолт. Так на локальном хосте пишется
 * `'DB_PASSWORD' => ''` при непустом пароле на верхнем уровне.
 */
function env_value(string $key, string $default = ''): string
{
    $values = env_values();

    if (isset($values[$key]) && is_scalar($values[$key])) {
        $configured = trim((string)$values[$key]);
        if ($configured !== '') {
            return $configured;
        }
    }

    $fromEnv = getenv($key);

    return is_string($fromEnv) && $fromEnv !== '' ? $fromEnv : $default;
}
