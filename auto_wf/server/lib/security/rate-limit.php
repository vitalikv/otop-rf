<?php

require_once __DIR__ . '/../response.php';

/**
 * Лимит попыток на файлах: в схеме eng_1 таблицы rate_limits нет, а заводить её
 * ради счётчика не хочется. Интерфейс тот же, что у табличного варианта в
 * warm-floor-3, — пороги переносятся как есть.
 *
 * Один файл на пару «действие + ключ», запись под flock: параллельные попытки
 * не затирают счётчик друг друга.
 */

function rate_limit_dir(): string
{
    return __DIR__ . '/../../storage/rate-limit';
}

function rate_limit_file(string $action, string $scope): string
{
    return rate_limit_dir() . '/' . hash('sha256', $action . '|' . $scope) . '.json';
}

function rate_limit_consume(
    string $action,
    string $scope,
    int $maxAttempts,
    int $windowSeconds,
    ?int $blockSeconds = null
): array {
    $blockSeconds = $blockSeconds ?? $windowSeconds;
    $now = time();

    $dir = rate_limit_dir();
    if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
        error_log('[auth-api] rate limit dir is not writable: ' . $dir);
        return ['allowed' => true, 'retryAfter' => 0];
    }

    $handle = @fopen(rate_limit_file($action, $scope), 'c+');
    if ($handle === false) {
        // Сбой файловой системы не должен запирать вход всем сразу: пропускаем.
        error_log('[auth-api] rate limit file is not writable for action ' . $action);
        return ['allowed' => true, 'retryAfter' => 0];
    }

    try {
        flock($handle, LOCK_EX);

        $raw = stream_get_contents($handle);
        $state = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
        if (!is_array($state)) {
            $state = [];
        }

        $hits = (int)($state['hits'] ?? 0);
        $windowStartedAt = (int)($state['windowStartedAt'] ?? $now);
        $blockedUntil = isset($state['blockedUntil']) ? (int)$state['blockedUntil'] : 0;

        if ($blockedUntil > $now) {
            return ['allowed' => false, 'retryAfter' => max(1, $blockedUntil - $now)];
        }

        if (($now - $windowStartedAt) >= $windowSeconds) {
            $hits = 0;
            $windowStartedAt = $now;
            $blockedUntil = 0;
        }

        $hits++;
        if ($hits > $maxAttempts) {
            $blockedUntil = $now + $blockSeconds;
        }

        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, (string)json_encode([
            'hits' => $hits,
            'windowStartedAt' => $windowStartedAt,
            'blockedUntil' => $blockedUntil,
        ]));
        fflush($handle);

        if ($blockedUntil > $now) {
            return ['allowed' => false, 'retryAfter' => max(1, $blockedUntil - $now)];
        }

        return ['allowed' => true, 'retryAfter' => 0];
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

function rate_limit_require(array $rules): void
{
    foreach ($rules as $rule) {
        $result = rate_limit_consume(
            (string)$rule['action'],
            (string)$rule['scope'],
            (int)$rule['maxAttempts'],
            (int)$rule['windowSeconds'],
            array_key_exists('blockSeconds', $rule) ? (int)$rule['blockSeconds'] : null
        );

        if ($result['allowed']) {
            continue;
        }

        header('Retry-After: ' . (string)$result['retryAfter']);
        json_error('rate_limited', 'Too many requests', 429, [
            'retryAfter' => (int)$result['retryAfter'],
        ]);
    }
}

/**
 * Пороги из warm-floor-3: по IP шире, по паре «IP + почта» — уже, чтобы
 * перебор пароля к конкретному аккаунту упирался раньше.
 *
 * @return array правила для rate_limit_require
 */
function rate_limit_rules(string $action, string $email, int $ipAttempts, int $emailAttempts, int $windowSeconds): array
{
    $ip = get_request_ip();

    return [
        [
            'action' => $action . '_ip',
            'scope' => $ip,
            'maxAttempts' => $ipAttempts,
            'windowSeconds' => $windowSeconds,
            'blockSeconds' => $windowSeconds,
        ],
        [
            'action' => $action . '_ip_email',
            'scope' => $ip . '|' . mb_strtolower(trim($email)),
            'maxAttempts' => $emailAttempts,
            'windowSeconds' => $windowSeconds,
            'blockSeconds' => $windowSeconds,
        ],
    ];
}
