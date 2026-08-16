<?php

require_once __DIR__ . '/../db.php';

/**
 * Одноразовые ссылки из писем. В базу кладётся только sha256 от токена — сам
 * токен уходит в письмо и больше нигде не хранится.
 *
 * Токены двух типов живут независимо: запрос сброса пароля не гасит
 * неиспользованную ссылку подтверждения почты.
 */

const AUTH_TOKEN_TYPE_EMAIL_VERIFICATION = 'email_verification';
const AUTH_TOKEN_TYPE_PASSWORD_RESET = 'password_reset';

function auth_token_hash(string $token): string
{
    return hash('sha256', $token);
}

function auth_token_lifetime(string $type): int
{
    return $type === AUTH_TOKEN_TYPE_PASSWORD_RESET ? 60 * 60 : 24 * 60 * 60;
}

function auth_revoke_tokens(int $userId, string $type): void
{
    $stmt = db()->prepare(
        'UPDATE user_token
         SET used = 1
         WHERE user_id = :user_id
           AND type = :type
           AND used = 0'
    );
    $stmt->execute([
        'user_id' => $userId,
        'type' => $type,
    ]);
}

/**
 * @return array ['token' => сырой токен для письма, 'expires' => unix-время]
 */
function auth_create_token(int $userId, string $type): array
{
    // Отработавшие строки чистятся здесь же — отдельный крон ради этого
    // заводить не за чем. Заодно уходят висячие записи удалённых пользователей.
    //
    // Погашенные (used = 1) удаляются наравне с просроченными: повторный переход
    // по такой ссылке и так упирается в «строки нет», а разницы между «нет» и
    // «есть, но использована» снаружи не видно.
    $cleanup = db()->prepare('DELETE FROM user_token WHERE expires < :now OR used = 1');
    $cleanup->execute(['now' => time()]);

    // Прежние ссылки того же типа гасятся после уборки: их очередь придёт со
    // следующим выпуском.
    auth_revoke_tokens($userId, $type);

    $token = bin2hex(random_bytes(32));
    $expires = time() + auth_token_lifetime($type);

    $stmt = db()->prepare(
        'INSERT INTO user_token (user_id, type, token_hash, expires, used)
         VALUES (:user_id, :type, :token_hash, :expires, 0)'
    );
    $stmt->execute([
        'user_id' => $userId,
        'type' => $type,
        'token_hash' => auth_token_hash($token),
        'expires' => $expires,
    ]);

    return [
        'token' => $token,
        'expires' => $expires,
    ];
}

/**
 * Поиск идёт по равенству хеша, тип проверяется отдельным условием.
 *
 * @return array|null ['id' => id токена, 'user_id' => ..., 'mail' => ..., 'active' => ...]
 */
function auth_find_active_token(string $rawToken, string $type): ?array
{
    if ($rawToken === '') {
        return null;
    }

    $stmt = db()->prepare(
        'SELECT t.id, t.user_id, u.mail, u.active
         FROM user_token t
         INNER JOIN `user` u ON u.id = t.user_id
         WHERE t.token_hash = :token_hash
           AND t.type = :type
           AND t.used = 0
           AND t.expires > :now
         LIMIT 1'
    );
    $stmt->execute([
        'token_hash' => auth_token_hash($rawToken),
        'type' => $type,
        'now' => time(),
    ]);

    $row = $stmt->fetch();

    return is_array($row) ? $row : null;
}

function auth_mark_token_used(int $tokenId): void
{
    $stmt = db()->prepare('UPDATE user_token SET used = 1 WHERE id = :id AND used = 0');
    $stmt->execute(['id' => $tokenId]);
}
