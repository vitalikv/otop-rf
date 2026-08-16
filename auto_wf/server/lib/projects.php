<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';

/**
 * Работа с таблицей `project` схемы eng_1. Данные проекта лежат прямо в базе:
 * `json longtext` и `preview blob` в той же строке — файлов на диске нет.
 *
 * Владелец входит в условие каждой выборки: «чужой проект» и «нет такого»
 * снаружи неразличимы, оба дают 404. В gl этого не было вовсе — там loadSql.php
 * отдавал json любого проекта по его id.
 */

/** Пока константа: подписки нет, а точка подключения нужна одна. */
const PROJECT_LIMIT_DEFAULT = 10;

const PROJECT_NAME_MAX = 120;

/** Предел тела запроса с проектом. Колонка longtext вмещает 4 ГБ, но столько не нужно. */
const PROJECT_JSON_MAX_BYTES = 4 * 1024 * 1024;

/**
 * Предел превью. Колонка `preview` — mediumblob (миграция 003), в неё влезает
 * 16 МБ, так что число здесь больше не подгонка под хранилище, а защита от
 * заливки мусора: плитка списка — это десятки килобайт, и полмегабайта хватает
 * с запасом даже на вдвое более крупный кадр.
 */
const PROJECT_PREVIEW_MAX_BYTES = 512 * 1024;

/**
 * При лимите в десять проектов это формальность, но запрос без ограничения —
 * мина на будущее: снимут лимит по подписке, и список поедет целиком.
 */
const PROJECT_LIST_LIMIT = 100;

/**
 * Лимит достигнут. Отдельный класс нужен потому, что проверка и вставка идут
 * внутри транзакции: вернуть оттуда «сколько занято» обычным значением можно
 * только вперемешку с успешным ответом.
 */
class ProjectLimitReached extends RuntimeException
{
    /** @var int */
    public $limit;

    /** @var int */
    public $used;

    public function __construct(int $limit, int $used)
    {
        parent::__construct('Project limit reached');
        $this->limit = $limit;
        $this->used = $used;
    }
}

/**
 * Единственное место, где решается предел числа проектов: появится подписка —
 * меняется эта функция, а ручки и клиент остаются как есть.
 */
function projects_limit_for_user(int $userId): int
{
    return PROJECT_LIMIT_DEFAULT;
}

function projects_used_count(int $userId): int
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM `project` WHERE user_id = :uid');
    $stmt->execute(['uid' => $userId]);

    return (int)$stmt->fetchColumn();
}

/**
 * Список без `json` и `preview`: gl ради десяти строк поднимал из базы десять
 * целых проектов вместе с картинками. Размер считается на лету — колонки под
 * него в схеме нет, а LENGTH по longtext дешевле, чем таскать сам текст.
 */
function projects_list(int $userId): array
{
    $stmt = db()->prepare(
        'SELECT id, name, date, LENGTH(json) AS size
         FROM `project`
         WHERE user_id = :uid
         ORDER BY date DESC
         LIMIT ' . PROJECT_LIST_LIMIT
    );
    $stmt->execute(['uid' => $userId]);

    return $stmt->fetchAll();
}

/**
 * Строка проекта без содержимого — для проверки владельца и версии. UPDATE с
 * условием по владельцу без этой выборки не годится: rowCount() = 0 приходит и
 * когда проект чужой, и когда данные не изменились, — эти случаи не различить.
 *
 * @return array{id:int,date:int}|null
 */
function project_find(int $id, int $userId): ?array
{
    $stmt = db()->prepare(
        'SELECT id, date FROM `project` WHERE id = :id AND user_id = :uid LIMIT 1'
    );
    $stmt->execute(['id' => $id, 'uid' => $userId]);
    $row = $stmt->fetch();

    return is_array($row) ? ['id' => (int)$row['id'], 'date' => (int)$row['date']] : null;
}

/** @return array{id:int,name:string,date:int,json:string}|null */
function project_load(int $id, int $userId): ?array
{
    $stmt = db()->prepare(
        'SELECT id, name, date, json FROM `project` WHERE id = :id AND user_id = :uid LIMIT 1'
    );
    $stmt->execute(['id' => $id, 'uid' => $userId]);
    $row = $stmt->fetch();

    if (!is_array($row)) {
        return null;
    }

    return [
        'id' => (int)$row['id'],
        'name' => (string)$row['name'],
        'date' => (int)$row['date'],
        'json' => (string)$row['json'],
    ];
}

/** @return array{preview:string,date:int}|null null — проекта нет или он чужой. */
function project_load_preview(int $id, int $userId): ?array
{
    $stmt = db()->prepare(
        'SELECT preview, date FROM `project` WHERE id = :id AND user_id = :uid LIMIT 1'
    );
    $stmt->execute(['id' => $id, 'uid' => $userId]);
    $row = $stmt->fetch();

    if (!is_array($row)) {
        return null;
    }

    return ['preview' => (string)$row['preview'], 'date' => (int)$row['date']];
}

/**
 * Создание проекта. Проверка лимита и вставка идут одной транзакцией: порознь
 * два одновременных сохранения обошли бы предел на единицу. FOR UPDATE держит
 * посчитанный диапазон строк до конца транзакции.
 *
 * @return array{id:int,date:int}
 * @throws ProjectLimitReached
 */
function project_create(int $userId, string $name, string $json): array
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $count = $pdo->prepare('SELECT COUNT(*) FROM `project` WHERE user_id = :uid FOR UPDATE');
        $count->execute(['uid' => $userId]);
        $used = (int)$count->fetchColumn();
        $limit = projects_limit_for_user($userId);

        if ($used >= $limit) {
            $pdo->rollBack();
            throw new ProjectLimitReached($limit, $used);
        }

        $date = time();
        $insert = $pdo->prepare(
            'INSERT INTO `project` (user_id, name, json, preview, date)
             VALUES (:uid, :name, :json, NULL, :date)'
        );
        $insert->execute([
            'uid' => $userId,
            'name' => $name,
            'json' => $json,
            'date' => $date,
        ]);

        $id = (int)$pdo->lastInsertId();
        $pdo->commit();

        return ['id' => $id, 'date' => $date];
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/** Перезапись найденного проекта. Владелец уже проверен выборкой. */
function project_overwrite(int $id, int $userId, string $name, string $json): int
{
    $date = time();
    $stmt = db()->prepare(
        'UPDATE `project` SET name = :name, json = :json, date = :date
         WHERE id = :id AND user_id = :uid'
    );
    $stmt->execute([
        'name' => $name,
        'json' => $json,
        'date' => $date,
        'id' => $id,
        'uid' => $userId,
    ]);

    return $date;
}

function project_rename(int $id, int $userId, string $name): int
{
    $date = time();
    $stmt = db()->prepare(
        'UPDATE `project` SET name = :name, date = :date WHERE id = :id AND user_id = :uid'
    );
    $stmt->execute(['name' => $name, 'date' => $date, 'id' => $id, 'uid' => $userId]);

    return $date;
}

/**
 * Удаление жёсткое: строка уходит целиком, мусора не остаётся. Мягкое удаление
 * в warm-floor-3 понадобилось из-за файлов на диске — здесь файлов нет.
 */
function project_delete(int $id, int $userId): void
{
    $stmt = db()->prepare('DELETE FROM `project` WHERE id = :id AND user_id = :uid');
    $stmt->execute(['id' => $id, 'uid' => $userId]);
}

/**
 * Превью пишется отдельным запросом после сохранения, и `date` при этом не
 * трогается: ссылка на картинку содержит его меткой версии, а клиент уже держит
 * это значение как базу для следующей перезаписи.
 */
function project_store_preview(int $id, int $userId, string $binary): void
{
    $stmt = db()->prepare(
        'UPDATE `project` SET preview = :preview WHERE id = :id AND user_id = :uid'
    );
    $stmt->bindValue('preview', $binary, PDO::PARAM_LOB);
    $stmt->bindValue('id', $id, PDO::PARAM_INT);
    $stmt->bindValue('uid', $userId, PDO::PARAM_INT);
    $stmt->execute();
}

function project_preview_url(int $id, int $date): string
{
    return '/api/projects/preview?id=' . $id . '&v=' . $date;
}

/**
 * Чистка имени. Колонка `name` — text в кодировке utf8: четырёхбайтные символы
 * в неё не лезут, и при нестрогом sql_mode MySQL обрежет строку по первому
 * такому символу. Управляющие символы убираем заодно — в имени им делать нечего.
 */
function project_clean_name(string $name): string
{
    if (!mb_check_encoding($name, 'UTF-8')) {
        return '';
    }

    $clean = preg_replace('/[\x{0000}-\x{001F}\x{007F}]+/u', ' ', $name);
    $clean = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', (string)$clean);
    $clean = preg_replace('/\s+/u', ' ', (string)$clean);

    return trim((string)$clean);
}

function project_name_is_valid(string $name): bool
{
    return $name !== '' && mb_strlen($name) <= PROJECT_NAME_MAX;
}

/**
 * Проверка тела сохранения. Разбирать проект на сервере незачем ни разу за весь
 * путь: строка ложится в колонку как пришла и такой же уходит обратно. Нужно
 * лишь убедиться, что это валидный JSON и что это наш проект.
 */
function project_json_is_valid(string $raw): bool
{
    if ($raw === '' || $raw[0] !== '{') {
        return false;
    }

    // PHP 8.3+: проверка без построения дерева в памяти.
    $valid = function_exists('json_validate')
        ? json_validate($raw, 64)
        : json_decode($raw) !== null;

    if (!$valid) {
        return false;
    }

    // Формат стоит первым полем объекта — полную проверку структуры делает
    // клиент, у него для этого есть isProjectData.
    return preg_match('/"format"\s*:\s*"skeleton-wf"/', substr($raw, 0, 200)) === 1;
}

/** JPEG начинается с SOI и маркера — иначе в колонку уедет что угодно. */
function project_preview_is_jpeg(string $binary): bool
{
    return strncmp($binary, "\xFF\xD8\xFF", 3) === 0;
}

/**
 * Лимит попыток по идентификатору пользователя, а не по IP: за одним IP сидит
 * офис, и сохранение одного человека запирало бы работу остальным.
 */
function project_rate_limit_rules(string $action, int $userId, int $maxAttempts, int $windowSeconds): array
{
    return [
        [
            'action' => $action,
            'scope' => 'user:' . $userId,
            'maxAttempts' => $maxAttempts,
            'windowSeconds' => $windowSeconds,
            'blockSeconds' => $windowSeconds,
        ],
    ];
}
