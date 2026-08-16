<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/tokens.php';
require_once __DIR__ . '/../mail/mailer.php';

function auth_send_verification_email(array $user): array
{
    $tokenData = auth_create_token((int)$user['id'], AUTH_TOKEN_TYPE_EMAIL_VERIFICATION);

    return mail_send_email_verification((string)$user['mail'], (string)$tokenData['token']);
}

function auth_verify_email_token(string $token): array
{
    $tokenRow = auth_find_active_token($token, AUTH_TOKEN_TYPE_EMAIL_VERIFICATION);
    if ($tokenRow === null) {
        throw new RuntimeException('Invalid or expired verification token');
    }

    $userId = (int)$tokenRow['user_id'];

    auth_mark_email_verified($userId);
    auth_mark_token_used((int)$tokenRow['id']);
    auth_revoke_tokens($userId, AUTH_TOKEN_TYPE_EMAIL_VERIFICATION);

    $user = auth_find_user_by_id($userId);
    if ($user === null) {
        throw new RuntimeException('User not found');
    }

    return $user;
}

/**
 * Ответ ручки одинаков в любом случае, поэтому здесь тихо выходим и когда почты
 * нет, и когда она уже подтверждена: иначе по ответу проверяют, кто
 * зарегистрирован.
 */
function auth_resend_verification_email(string $email): void
{
    $user = auth_find_user_by_email($email);
    if ($user === null || auth_is_email_verified($user)) {
        return;
    }

    auth_send_verification_email($user);
}
