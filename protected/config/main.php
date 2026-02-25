<?php

$env = static function (string $key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
};

$dbHost = $env('DB_HOST', 'db');
$dbPort = $env('DB_PORT', '3306');
$dbName = $env('DB_NAME', 'books_catalog');
$dbUser = $env('DB_USER', 'books_user');
$dbPassword = $env('DB_PASSWORD', 'books_pass');

return [
    'basePath' => dirname(__DIR__),
    'name' => 'Books Catalog',
    'language' => 'en',
    'sourceLanguage' => 'en',
    'preload' => ['log'],
    'import' => [
        'application.models.*',
        'application.components.*',
        'application.services.*',
    ],
    'defaultController' => 'book',
    'components' => [
        'request' => [
            'enableCsrfValidation' => true,
        ],
        'user' => [
            'allowAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'db' => [
            'connectionString' => "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}",
            'emulatePrepare' => true,
            'username' => $dbUser,
            'password' => $dbPassword,
            'charset' => 'utf8mb4',
            'enableParamLogging' => false,
            'enableProfiling' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => [
                'site/login' => 'site/login',
                'site/logout' => 'site/logout',

                'book/index' => 'book/index',
                'book/view/<id:\\d+>' => 'book/view',
                'book/create' => 'book/create',
                'book/update/<id:\\d+>' => 'book/update',
                'book/delete/<id:\\d+>' => 'book/delete',

                'author/index' => 'author/index',
                'author/view/<id:\\d+>' => 'author/view',
                'author/create' => 'author/create',
                'author/update/<id:\\d+>' => 'author/update',
                'author/delete/<id:\\d+>' => 'author/delete',

                'subscription/create' => 'subscription/create',

                'report/top-authors' => 'report/topAuthors',

                '<controller:\\w+>/<action:\\w+>' => '<controller>/<action>',
                '<controller:\\w+>/<action:\\w+>/<id:\\d+>' => '<controller>/<action>',
            ],
        ],
        'log' => [
            'class' => 'CLogRouter',
            'routes' => [
                [
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info',
                ],
            ],
        ],
    ],
    'params' => [
        'smsPilotApiKey' => $env('SMSPILOT_API_KEY', ''),
        'smsPilotSender' => $env('SMSPILOT_SENDER', 'INFORM'),
    ],
];
