<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/security/rate-limit.php';
require_once __DIR__ . '/../../lib/auth/validation.php';
require_once __DIR__ . '/../../lib/auth/auth.php';
require_once __DIR__ . '/../../lib/auth/verification.php';

init_json_api(['POST', 'OPTIONS']);

$body = json_input();
$email = trim((string)($body['email'] ?? ''));
$password = (string)($body['password'] ?? '');
$confirmPassword = array_key_exists('confirmPassword', $body) ? (string)$body['confirmPassword'] : null;

rate_limit_require(rate_limit_rules('auth_register', $email, 5, 3, 60 * 60));

if (!auth_validate_email($email)) {
    json_error('invalid_email', 'Invalid email', 400);
}

if (!auth_validate_password($password)) {
    json_error('invalid_password', 'Password length is out of range', 400);
}

if ($confirmPassword !== null && $password !== $confirmPassword) {
    json_error('passwords_mismatch', 'Passwords do not match', 400);
}

try {
    // Быстрый путь: обычно занятая почта отсеивается здесь. Последняя линия —
    // уникальный индекс: два одновременных запроса иначе завели бы два аккаунта.
    if (auth_find_user_by_email($email) !== null) {
        json_error('email_taken', 'Email is already in use', 409);
    }

    $user = auth_create_user($email, $password);
    $delivery = auth_send_verification_email($user);

    json_success([
        'authenticated' => false,
        'user' => null,
        'verificationRequired' => true,
        'email' => (string)$user['mail'],
        'delivery' => ['sent' => (bool)($delivery['sent'] ?? false)],
    ], 201);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        json_error('email_taken', 'Email is already in use', 409);
    }
    error_log('[auth-api] register: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
