<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth/password-reset.php';

init_json_api(['GET', 'OPTIONS']);

$token = trim((string)($_GET['token'] ?? ''));

if ($token === '') {
    json_error('invalid_reset_token', 'Reset token is required', 400);
}

try {
    auth_assert_password_reset_token($token);
    json_success(['ok' => true, 'valid' => true]);
} catch (RuntimeException $e) {
    json_error('invalid_reset_token', $e->getMessage(), 400);
} catch (PDOException $e) {
    error_log('[auth-api] reset-password-check: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
