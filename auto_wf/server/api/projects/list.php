<?php

require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/project-access.php';
require_once __DIR__ . '/../../lib/projects.php';

init_json_api(['GET', 'OPTIONS']);

$userId = require_authenticated_user_id();

try {
    $rows = projects_list($userId);
    $limit = projects_limit_for_user($userId);
    $used = projects_used_count($userId);

    $items = [];
    foreach ($rows as $row) {
        $id = (int)$row['id'];
        $date = (int)$row['date'];
        $items[] = [
            'id' => $id,
            'name' => (string)$row['name'],
            'modified' => $date,
            'size' => (int)$row['size'],
            // Превью не едет в списке: браузер подтянет плитки параллельно и
            // закэширует их по метке версии в ссылке.
            'previewUrl' => project_preview_url($id, $date),
        ];
    }

    json_success([
        'items' => $items,
        'limits' => [
            'max' => $limit,
            'used' => $used,
            'canCreate' => $used < $limit,
        ],
    ]);
} catch (PDOException $e) {
    error_log('[projects-api] list: ' . $e->getMessage());
    json_error('db_error', 'Database error', 500);
}
