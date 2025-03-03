<?php

return [

    /**
     * Путь до БД, где организована очередь звонков
     */

    'sqlite' => [
        'dbPath' => __DIR__ . '/../resources/' . $_ENV['DB_PATH']
    ],
];