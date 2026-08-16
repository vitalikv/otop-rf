<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/security/rate-limit.php';
require_once __DIR__ . '/../../lib/project-access.php';
require_once __DIR__ . '/../../lib/projects.php';

/**
 * Обе стороны превью в одном файле: GET отдаёт картинку, POST её принимает.
 * Это единственная ручка с двумя методами — у неё общий предмет и общая
 * проверка владельца, а разводить чтение и запись по разным адресам ради
 * симметрии с остальными смысла нет.
 */
init_json_api(['GET', 'POST', 'OPTIONS']);

$userId = require_authenticated_user_id();
$id = project_id_param($_GET['id'] ?? null);

if ($id === 0) {
    json_error('project_not_found', 'Project not found', 404);
}

if (get_request_method() === 'GET') {
    try {
        $row = project_load_preview($id, $userId);
        if ($row === null || $row['preview'] === '') {
            // Пустое превью — не ошибка: первое сохранение могло уйти без
            // снимка. Окно рисует на этот ответ плашку «Нет превью».
            json_error('project_not_found', 'Preview not found', 404);
        }

        // Ссылка содержит v=<date>, поэтому картинку можно держать в кэше
        // сутки: перезапись меняет дату, и ссылка становится другой.
        $etag = '"' . $id . '-' . $row['date'] . '"';
        header('Cache-Control: private, max-age=86400');
        header('ETag: ' . $etag);

        $sent = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        if (is_string($sent) && trim($sent) === $etag) {
            http_response_code(304);
            exit;
        }

        header('Content-Type: image/jpeg');
        header('Content-Length: ' . strlen($row['preview']));
        echo $row['preview'];
        exit;
    } catch (PDOException $e) {
        error_log('[projects-api] preview get: ' . $e->getMessage());
        json_error('db_error', 'Database error', 500);
    }
}

rate_limit_require(project_rate_limit_rules('project_save', $userId, 120, 3600));

$declaredLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($declaredLength > PROJECT_PREVIEW_MAX_BYTES) {
    json_error('project_preview_too_large', 'Preview is too large', 413);
}

$binary = (string)file_get_contents('php://input');
if (strlen($binary) > PROJECT_PREVIEW_MAX_BYTES) {
    json_error('project_preview_too_large', 'Preview is too large', 413);
}

if (!project_preview_is_jpeg($binary)) {
    json_error('invalid_project_preview', 'Preview is not a JPEG image', 400);
}

try {
    $project = project_find($id, $userId);
    if ($project === null) {
        json_error('project_not_found', 'Project not found', 404);
    }

    project_store_preview($id, $userId, $binary);

    json_success([
        'ok' => true,
        'previewUrl' => project_preview_url($id, $project['date']),
    ]);
} catch (PDOException $e) {
    error_log('[projects-api] preview post: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
