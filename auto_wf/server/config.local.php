<?php

/**
 * Рабочие настройки выкладки: этот файл `npm run deploy` кладёт в
 * release/auto_wf/server/config.local.php, а оттуда — на сайт.
 *
 * В репозиторий он не попадает (.gitignore): в нём пароль базы боевого сервера.
 * Структура и смысл ключей — в server/config.example.php, порядок источников —
 * в server/lib/env.php, разбор по разделам — в docs/deploy-otop-rf.md.
 *
 * Файл один на обе установки: общее сверху, различия — в секции 'hosts' по
 * имени хоста. Локальный otop-rf ходит в MySQL OpenServer без пароля, боевой
 * домен — с паролем; всё остальное (база, каталог приложения) совпадает.
 */

return [
    'DB_HOST'     => '127.0.0.1',
    'DB_PORT'     => '3306',
    'DB_NAME'     => 'skeleton_wf',
    'DB_USER'     => 'root',
    'DB_PASSWORD' => '',

    // Путь каталога, а не адрес входа /calculator/auto_wf: к этому значению
    // дописывается /verify-email.html?token=... и /reset-password.html?token=...
    'APP_BASE_URL'    => 'http://otop-rf/auto_wf',
    'APP_COOKIE_PATH' => '/auto_wf',

    // Без этой строки список остаётся дефолтным (localhost:3000, skeleton-wf) и
    // все POST с otop-rf получают 403 origin_not_allowed.
    'ALLOWED_ORIGINS' => 'http://otop-rf',

    'MAIL_FROM_EMAIL' => 'no-reply@otop-rf',
    'MAIL_FROM_NAME'  => 'Тёплый пол',

    'hosts' => [
        // Локальный сайт под OpenServer: значения верхнего уровня уже про него,
        // профиль оставлен явно — чтобы при переносе пароля наверх локальная
        // установка не унаследовала боевой.
        'otop-rf' => [
            'DB_PASSWORD' => '',
        ],

        // Боевой сервер. Домен кириллический, и Apache видит его в punycode —
        // ключ и Origin пишутся так же (docs/deploy-otop-rf.md, раздел 8).
        'xn------6cdcklga3agac0adveeerahel6btn3c.xn--p1ai' => [
            // Тот же пароль root, что у сайта в include/bd.php.
            'DB_PASSWORD' => 'ns62QYhqMf',

            // На Linux 'localhost' — это unix-сокет, а '127.0.0.1' — TCP, и для
            // MySQL это разные хосты пользователя: при skip-name-resolve
            // 'root'@'localhost' по TCP не проходит, а ошибка выглядит как
            // неверный пароль. Сайт ходит через localhost (include/bd.php) —
            // ходим так же.
            'DB_HOST' => 'localhost',

            // Сайт открывается и по http, и по https с валидным сертификатом.
            // В письмах — https, в разрешённых источниках — обе схемы: Origin
            // приходит тот, по которому пользователь открыл страницу.
            'APP_BASE_URL'    => 'https://xn------6cdcklga3agac0adveeerahel6btn3c.xn--p1ai/auto_wf',
            'ALLOWED_ORIGINS' => 'https://xn------6cdcklga3agac0adveeerahel6btn3c.xn--p1ai,'
                . 'http://xn------6cdcklga3agac0adveeerahel6btn3c.xn--p1ai',
            'MAIL_FROM_EMAIL' => 'no-reply@xn------6cdcklga3agac0adveeerahel6btn3c.xn--p1ai',
        ],
    ],
];
