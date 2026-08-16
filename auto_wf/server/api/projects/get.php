<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/project-access.php';
require_once __DIR__ . '/../../lib/projects.php';

init_json_api(['GET', 'OPTIONS']);

$userId = require_authenticated_user_id();
$id = project_id_param($_GET['id'] ?? null);

if ($id === 0) {
    json_error('project_not_found', 'Project not found', 404);
}

try {
    $project = project_load($id, $userId);
    if ($project === null) {
        json_error('project_not_found', 'Project not found', 404);
    }

    $json = $project['json'];
    // Строка в колонке проверена при записи; если она всё же испорчена, ответ
    // без этой проверки перестал бы быть JSON целиком — и клиент упал бы на
    // разборе вместо внятного «Проект повреждён».
    if ($json === '' || $json[0] !== '{') {
        error_log('[projects-api] get: broken json in project ' . $id);
        json_error('project_corrupted', 'Stored project is not readable', 500);
    }

    // Ответ склеивается вручную: проект в базе — валидный JSON, и разбирать его
    // ради того, чтобы тут же собрать обратно, незачем. warm-floor-3 делает
    // именно это, тратя двойную память на каждое открытие.
    http_response_code(200);
    echo '{"id":' . $project['id']
        . ',"name":' . json_encode($project['name'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        . ',"modified":' . $project['date']
        . ',"data":' . $json . '}';
    exit;
} catch (PDOException $e) {
    error_log('[projects-api] get: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
