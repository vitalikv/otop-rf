<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/security/rate-limit.php';
require_once __DIR__ . '/../../lib/auth/validation.php';
require_once __DIR__ . '/../../lib/auth/password-reset.php';

init_json_api(['POST', 'OPTIONS']);

$body = json_input();
$email = trim((string)($body['email'] ?? ''));

rate_limit_require(rate_limit_rules('auth_forgot_password', $email, 5, 3, 60 * 60));

if ($email !== '' && !auth_validate_email($email)) {
    json_error('invalid_email', 'Invalid email', 400);
}

try {
    // Ответ не должен раскрывать, существует ли почта, поэтому он одинаков и
    // для пустого поля, и для незнакомого адреса.
    if ($email !== '') {
        auth_send_password_reset_email($email);
    }

    json_success(['ok' => true]);
} catch (PDOException $e) {
    error_log('[auth-api] forgot-password: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
