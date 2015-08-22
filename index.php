<?php

require_once 'vendor/autoload.php';

use Wonderland\Library\Application;

define('PUBLIC_ROOT', 'http://127.0.0.1/Developpement/PHP/8thWonderland/site_23-04-2012/src/8thWonderland/');
define ( 'CSS_PATH', PUBLIC_ROOT . 'public/css/' );
define ( 'IMG_PATH', PUBLIC_ROOT . 'public/images/' );
define ( 'ICO_PATH', PUBLIC_ROOT . 'public/icones/' );
define ( 'JS_PATH', PUBLIC_ROOT . 'public/js/' );

$application = new Application();
$application->init();
$application->run();