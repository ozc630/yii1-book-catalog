<?php

declare(strict_types=1);

$debugFlag = getenv('YII_DEBUG');
$yiiDebug = $debugFlag !== false ? (bool) (int) $debugFlag : true;
defined('YII_DEBUG') or define('YII_DEBUG', $yiiDebug);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

$vendorAutoload = __DIR__ . '/vendor/autoload.php';
$yii = __DIR__ . '/vendor/yiisoft/yii/framework/yii.php';
$config = __DIR__ . '/protected/config/main.php';

if (!file_exists($vendorAutoload) || !file_exists($yii)) {
    http_response_code(500);
    echo 'Dependencies are not installed. Run composer install.';
    exit(1);
}

require_once $vendorAutoload;
require_once $yii;

Yii::createWebApplication($config)->run();
