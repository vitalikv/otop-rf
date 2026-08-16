<?php

require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth/auth.php';

/**
 * Проекты живут в аккаунте: гостю на любой ручке отвечают 401, а клиент по
 * этому коду открывает окно входа общим перехватом. Проверка стоит первой в
 * каждом файле api/projects — до разбора параметров и до чтения тела.
 */

function require_authenticated_user(): array
{
    try {
        $user = auth_current_user();
    } catch (PDOException $e) {
        error_log('[projects-api] session: ' . $e->getMessage());
        json_error('db_error', 'Database error', 500);
    }

    if ($user === null) {
        json_error('auth_required', 'Authentication required', 401);
    }

    return $user;
}

function require_authenticated_user_id(): int
{
    return (int)require_authenticated_user()['id'];
}

/**
 * Идентификатор проекта из запроса. Строка «12abc» до базы не доходит: у неё
 * тот же ответ, что и у чужого проекта, — 404, и различить их снаружи нельзя.
 */
function project_id_param($value): int
{
    if (is_int($value)) {
        return $value > 0 ? $value : 0;
    }
    if (is_string($value) && ctype_digit($value)) {
        return (int)$value;
    }
    return 0;
}
