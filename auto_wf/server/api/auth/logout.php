<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth/session.php';

init_json_api(['POST', 'OPTIONS']);

auth_logout_user();

json_success(['ok' => true]);
