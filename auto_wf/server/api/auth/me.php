<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth/auth.php';

init_json_api(['GET', 'OPTIONS']);

try {
    $user = auth_current_user();

    json_success([
        'authenticated' => $user !== null,
        'user' => $user !== null ? auth_user_public_payload($user) : null,
    ]);
} catch (PDOException $e) {
    error_log('[auth-api] me: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
