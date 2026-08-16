<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth/validation.php';
require_once __DIR__ . '/../../lib/auth/password-reset.php';

init_json_api(['POST', 'OPTIONS']);

$body = json_input();
$token = trim((string)($body['token'] ?? ''));
$password = (string)($body['password'] ?? '');
$confirmPassword = (string)($body['confirmPassword'] ?? '');

if ($token === '') {
    json_error('invalid_reset_token', 'Reset token is required', 400);
}

if (!auth_validate_password($password)) {
    json_error('invalid_password', 'Password length is out of range', 400);
}

if ($password !== $confirmPassword) {
    json_error('passwords_mismatch', 'Passwords do not match', 400);
}

try {
    auth_reset_password_by_token($token, $password);
    json_success(['ok' => true]);
} catch (RuntimeException $e) {
    json_error('invalid_reset_token', $e->getMessage(), 400);
} catch (PDOException $e) {
    error_log('[auth-api] reset-password: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
