<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth/verification.php';

init_json_api(['GET', 'OPTIONS']);

$token = trim((string)($_GET['token'] ?? ''));

if ($token === '') {
    json_error('invalid_verification_token', 'Verification token is required', 400);
}

try {
    $user = auth_verify_email_token($token);

    json_success([
        'ok' => true,
        'verified' => true,
        'user' => auth_user_public_payload($user),
    ]);
} catch (RuntimeException $e) {
    json_error('invalid_verification_token', $e->getMessage(), 400);
} catch (PDOException $e) {
    error_log('[auth-api] verify-email: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
