<?php

declare(strict_types=1);

error_reporting(E_ALL);

$autoload = dirname(__DIR__) . '/vendor/autoload.php';

if (!file_exists($autoload)) {
    fwrite(STDERR, "Dependencies are not installed. Run composer install.\n");
    exit(1);
}

require_once $autoload;
require_once __DIR__ . '/IntegrationTestCase.php';
