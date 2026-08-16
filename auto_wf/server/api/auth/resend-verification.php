<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/security/rate-limit.php';
require_once __DIR__ . '/../../lib/auth/validation.php';
require_once __DIR__ . '/../../lib/auth/verification.php';

init_json_api(['POST', 'OPTIONS']);

$body = json_input();
$email = trim((string)($body['email'] ?? ''));

rate_limit_require(rate_limit_rules('auth_resend_verification', $email, 5, 3, 60 * 60));

if (!auth_validate_email($email)) {
    json_error('invalid_email', 'Invalid email', 400);
}

try {
    // Ответ один и тот же, есть такая почта или нет: иначе по нему проверяют,
    // кто зарегистрирован.
    auth_resend_verification_email($email);
    json_success(['ok' => true]);
} catch (PDOException $e) {
    error_log('[auth-api] resend-verification: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
