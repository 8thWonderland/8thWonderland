<?php

require_once 'vendor/autoload.php';

ini_set('display_errors', 1);

$rootPath = 
    (basename($_SERVER['REQUEST_URI']) === 'index.php')
    ? dirname($_SERVER['REQUEST_URI']) . '/'
    : $_SERVER['REQUEST_URI']
;

if($rootPath === '//') {
    $rootPath = '/';
}

define ('CSS_PATH', "{$rootPath}src/8thWonderland/public/css/");
define ('IMG_PATH', "{$rootPath}src/8thWonderland/public/images/");
define ('ICO_PATH', "{$rootPath}src/8thWonderland/public/icones/");
define ('JS_PATH', "{$rootPath}src/8thWonderland/public/js/");

$application = new Wonderland\Library\Application();
$application->init();
$application->run();