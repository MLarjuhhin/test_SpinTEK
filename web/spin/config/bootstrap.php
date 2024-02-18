<?php

require_once __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;

date_default_timezone_set('Europe/Tallinn');
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Логирование
$logDirectory = '/var/www/public/spin/log';
if (!is_dir($logDirectory)) {
    mkdir($logDirectory, 0755, true);
}
ini_set('error_log', $logDirectory.'/'.date("Y-m-d").'-php-error.log');

// Обработчики ошибок и исключений
require_once __DIR__ . '/handlers.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
