<?php

require_once 'vendor/autoload.php';

ini_set('display_errors', 1);

/*
 * Set default timezone
 */
date_default_timezone_set('Europe/Paris');

$rootPath = 
    (!isset($_SERVER['BASE']))
    ? '/'
    : $_SERVER['BASE'] . '/'
;

define ('VIEW_PATH', $rootPath);
define ('CSS_PATH', "{$rootPath}src/8thWonderland/public/css/");
define ('IMG_PATH', "{$rootPath}src/8thWonderland/public/images/");
define ('ICO_PATH', "{$rootPath}src/8thWonderland/public/icones/");
define ('JS_PATH', "{$rootPath}src/8thWonderland/public/js/");

$application = new Wonderland\Library\Application();
$application->init();
$application->run();