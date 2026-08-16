<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/security/rate-limit.php';
require_once __DIR__ . '/../../lib/project-access.php';
require_once __DIR__ . '/../../lib/projects.php';

init_json_api(['POST', 'OPTIONS']);

$userId = require_authenticated_user_id();

rate_limit_require(project_rate_limit_rules('project_save', $userId, 120, 3600));

// Размер проверяется по заголовку — до чтения тела: json_input() тянет
// php://input целиком, и для проектов это уже опасно.
$declaredLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($declaredLength > PROJECT_JSON_MAX_BYTES) {
    json_error('project_too_large', 'Project is too large', 413);
}

$raw = (string)file_get_contents('php://input');
// Заголовку верить нельзя: chunked-запрос приходит вообще без Content-Length.
if (strlen($raw) > PROJECT_JSON_MAX_BYTES) {
    json_error('project_too_large', 'Project is too large', 413);
}

if (!project_json_is_valid($raw)) {
    json_error('invalid_project_data', 'Request body is not a project', 400);
}

$name = project_clean_name((string)($_GET['name'] ?? ''));
if (!project_name_is_valid($name)) {
    json_error('invalid_project_name', 'Project name is empty or too long', 400);
}

$id = project_id_param($_GET['id'] ?? null);
$base = project_id_param($_GET['base'] ?? null);

try {
    if ($id === 0) {
        $created = project_create($userId, $name, $raw);

        json_success([
            'id' => $created['id'],
            'name' => $name,
            'modified' => $created['date'],
        ]);
    }

    $project = project_find($id, $userId);
    if ($project === null) {
        json_error('project_not_found', 'Project not found', 404);
    }

    // Оптимистичная блокировка: клиент присылает дату той версии, которую
    // открывал. Разошлась — проект перезаписан из другой вкладки, и окно
    // спрашивает подтверждение вместо того, чтобы молча затереть чужую работу.
    if ($base !== 0 && $base !== $project['date']) {
        json_error('project_changed', 'Project was changed elsewhere', 409, [
            'modified' => $project['date'],
        ]);
    }

    $date = project_overwrite($id, $userId, $name, $raw);

    json_success(['id' => $id, 'name' => $name, 'modified' => $date]);
} catch (ProjectLimitReached $e) {
    json_error('project_limit_reached', 'Project limit reached', 403, [
        'limit' => $e->limit,
        'used' => $e->used,
    ]);
} catch (PDOException $e) {
    error_log('[projects-api] save: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
