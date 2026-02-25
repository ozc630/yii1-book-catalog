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
    'name' => 'Books Catalog Console',
    'import' => [
        'application.models.*',
        'application.components.*',
        'application.services.*',
    ],
    'commandMap' => [
        'migrate' => [
            'class' => 'system.cli.commands.MigrateCommand',
            'migrationPath' => 'application.migrations',
            'migrationTable' => 'yii_migration',
        ],
    ],
    'components' => [
        'db' => [
            'connectionString' => "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}",
            'emulatePrepare' => true,
            'username' => $dbUser,
            'password' => $dbPassword,
            'charset' => 'utf8mb4',
        ],
    ],
    'params' => [
        'smsPilotApiKey' => $env('SMSPILOT_API_KEY', ''),
        'smsPilotSender' => $env('SMSPILOT_SENDER', 'INFORM'),
    ],
];
