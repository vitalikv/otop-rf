<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/validation.php';

/**
 * Работа с таблицей `user` схемы eng_1. Отличий от образца два: почта лежит в
 * колонке `mail`, а подтверждение почты — флагом `active` вместо даты.
 *
 * Колонки перечисляются поимённо: хеш пароля нужен только на входе, и таскать
 * его в каждом запросе незачем.
 */

/** Хеш несуществующего пароля для холостой проверки — см. auth_verify_credentials. */
const AUTH_DUMMY_PASSWORD_HASH = '$2y$10$usesomesillystringfoeleganttoolongforuseinacaseC7WEyzhK4Y6C';

function auth_is_email_verified(array $user): bool
{
    return (int)($user['active'] ?? 0) === 1;
}

function auth_user_public_payload(array $user): array
{
    return [
        'id' => (int)$user['id'],
        'email' => (string)$user['mail'],
        'emailVerified' => auth_is_email_verified($user),
    ];
}

function auth_find_user_by_email(string $email): ?array
{
    $stmt = db()->prepare(
        'SELECT id, mail, active
         FROM `user`
         WHERE mail = :mail
         LIMIT 1'
    );
    $stmt->execute(['mail' => auth_normalize_email($email)]);
    $user = $stmt->fetch();

    return is_array($user) ? $user : null;
}

function auth_find_user_by_id(int $id): ?array
{
    $stmt = db()->prepare(
        'SELECT id, mail, active
         FROM `user`
         WHERE id = :id
         LIMIT 1'
    );
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();

    return is_array($user) ? $user : null;
}

/**
 * Пароль хранится хешем: в gl он лежал открытым текстом, но база отдельная и
 * совместимость с gl-скриптами не нужна.
 *
 * Дата пишется как "Y-m-d H:i:s", а не "Y-m-d-G-i" из gl: колонка строковая в
 * обоих случаях, но у формата gl "10" лексикографически меньше "9", и сортировка
 * по дате врёт.
 */
function auth_create_user(string $email, string $password): array
{
    $stmt = db()->prepare(
        'INSERT INTO `user` (mail, pass, date, token, active)
         VALUES (:mail, :pass, :date, NULL, 0)'
    );
    $stmt->execute([
        'mail' => auth_normalize_email($email),
        'pass' => password_hash($password, PASSWORD_DEFAULT),
        'date' => date('Y-m-d H:i:s'),
    ]);

    $user = auth_find_user_by_id((int)db()->lastInsertId());
    if ($user === null) {
        throw new RuntimeException('Failed to load created user');
    }

    return $user;
}

/**
 * Ветка «пользователя нет» тоже гоняет password_verify по фиктивному хешу:
 * иначе ответ для несуществующей почты приходит заметно быстрее, и по таймингу
 * перебираются существующие адреса.
 */
function auth_verify_credentials(string $email, string $password): ?array
{
    $stmt = db()->prepare(
        'SELECT id, mail, active, pass
         FROM `user`
         WHERE mail = :mail
         LIMIT 1'
    );
    $stmt->execute(['mail' => auth_normalize_email($email)]);
    $user = $stmt->fetch();

    if (!is_array($user)) {
        password_verify($password, AUTH_DUMMY_PASSWORD_HASH);
        return null;
    }

    if (!password_verify($password, (string)$user['pass'])) {
        return null;
    }

    return $user;
}

function auth_mark_email_verified(int $userId): void
{
    $stmt = db()->prepare('UPDATE `user` SET active = 1 WHERE id = :id');
    $stmt->execute(['id' => $userId]);
}

function auth_update_password(int $userId, string $password): string
{
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = db()->prepare('UPDATE `user` SET pass = :pass WHERE id = :id');
    $stmt->execute(['pass' => $hash, 'id' => $userId]);

    return $hash;
}

/**
 * Метка пароля сверяется на стороне базы: так сам хеш не покидает её и не живёт
 * в памяти PHP дольше нужного. Не сошлась — пароль сменили, и сессия
 * недействительна.
 */
function auth_current_user(): ?array
{
    $session = auth_session_read();
    if ($session === null || $session['mark'] === null) {
        return null;
    }

    $stmt = db()->prepare(
        'SELECT id, mail, active
         FROM `user`
         WHERE id = :id
           AND LEFT(SHA2(pass, 256), 16) = :mark
         LIMIT 1'
    );
    $stmt->execute([
        'id' => $session['user_id'],
        'mark' => $session['mark'],
    ]);

    $user = $stmt->fetch();

    return is_array($user) ? $user : null;
}
