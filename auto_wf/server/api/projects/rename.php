<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/security/rate-limit.php';
require_once __DIR__ . '/../../lib/project-access.php';
require_once __DIR__ . '/../../lib/projects.php';

init_json_api(['POST', 'OPTIONS']);

$userId = require_authenticated_user_id();

rate_limit_require(project_rate_limit_rules('project_save', $userId, 120, 3600));

$body = json_input();
$id = project_id_param($body['id'] ?? null);
$name = project_clean_name((string)($body['name'] ?? ''));

if (!project_name_is_valid($name)) {
    json_error('invalid_project_name', 'Project name is empty or too long', 400);
}

try {
    if ($id === 0 || project_find($id, $userId) === null) {
        json_error('project_not_found', 'Project not found', 404);
    }

    // Дата обновляется вместе с именем: список отсортирован по ней, и
    // переименованный проект — тоже изменённый.
    $date = project_rename($id, $userId, $name);

    json_success(['id' => $id, 'name' => $name, 'modified' => $date]);
} catch (PDOException $e) {
    error_log('[projects-api] rename: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
