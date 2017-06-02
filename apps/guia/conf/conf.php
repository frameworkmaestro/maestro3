<?php

return [
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Guia',
    'theme' => [
        'name' => 'patternfly',
        'template' => 'index'
    ],
    'options' => [
        'templateEngine' => 'latte',
        'pageTitle' => 'Maestro - Guia do Usuário'
    ],
    'login' => [
        'module' => "",
        'class' => "MAuthDbMd5",
        'check' => false
    ],
    'db' => [
        /* Postgres 
        'exemplos' => [
            'driver' => 'pdo_pgsql',
            'host' => 'localhost',
            'dbname' => 'exemplos',
            'user' => 'postgres',
            'password' => 'pg-admin',
            'formatDate' => 'DD/MM/YYYY',
            'formatTime' => 'HH24:MI:SS',
            'configurationClass' => 'Doctrine\DBAL\Configuration',
        ], */
        /* SQLite  */
        'exemplos' => [
            'driver' => 'sqlite3',
            'path' => Manager::getAppPath('models/sql/exemplos.db'),
            'formatDate' => '%d/%m/%Y',
            'formatTime' => '%H:%M:%S',
            'configurationClass' => 'Doctrine\DBAL\Configuration',
        ],
    ]
];
