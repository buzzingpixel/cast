<?php

declare(strict_types=1);

ini_set('display_errors', 'On');
ini_set('html_errors', '0');
error_reporting(-1);

define('TESTS_BASE_PATH', __DIR__);

define('TESTING_APP_PATH', dirname(__DIR__));
define('BASEPATH', TESTING_APP_PATH);

define('APP_BASE_PATH', TESTING_APP_PATH);

define('PATH_THIRD', 'pathThirdTest');

require dirname(__DIR__) . '/vendor/autoload.php';
