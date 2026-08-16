<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/security/rate-limit.php';
require_once __DIR__ . '/../../lib/auth/validation.php';
require_once __DIR__ . '/../../lib/auth/auth.php';

init_json_api(['POST', 'OPTIONS']);

$body = json_input();
$email = trim((string)($body['email'] ?? ''));
$password = (string)($body['password'] ?? '');

rate_limit_require(rate_limit_rules('auth_login', $email, 10, 5, 15 * 60));

if (!auth_validate_email($email) || !auth_validate_password($password)) {
    json_error('invalid_credentials', 'Invalid email or password', 401);
}

try {
    $user = auth_verify_credentials($email, $password);
    if ($user === null) {
        json_error('invalid_credentials', 'Invalid email or password', 401);
    }

    // Вход до подтверждения почты запрещён — как в обоих проектах-образцах.
    if (!auth_is_email_verified($user)) {
        json_error('email_not_verified', 'Email is not verified', 403);
    }

    auth_login_user((int)$user['id'], (string)$user['pass']);

    json_success([
        'authenticated' => true,
        'user' => auth_user_public_payload($user),
    ]);
} catch (PDOException $e) {
    error_log('[auth-api] login: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
