<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/security/rate-limit.php';
require_once __DIR__ . '/../../lib/project-access.php';
require_once __DIR__ . '/../../lib/projects.php';

init_json_api(['POST', 'OPTIONS']);

$userId = require_authenticated_user_id();

rate_limit_require(project_rate_limit_rules('project_delete', $userId, 60, 3600));

$body = json_input();
$id = project_id_param($body['id'] ?? null);

try {
    if ($id === 0 || project_find($id, $userId) === null) {
        json_error('project_not_found', 'Project not found', 404);
    }

    project_delete($id, $userId);

    json_success(['ok' => true]);
} catch (PDOException $e) {
    error_log('[projects-api] delete: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
