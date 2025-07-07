<?php

return [
    'default' => 'sqlite',
    'sqlite' => [
        'dbPath' => __DIR__ . '/../resources/' . $_ENV['DB_PATH']
    ],
];