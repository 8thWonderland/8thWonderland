<?php

require_once 'vendor/autoload.php';

ini_set('display_errors', 1);

define ( 'CSS_PATH', $_SERVER['REQUEST_URI'] . 'src/8thWonderland/public/css/' );
define ( 'IMG_PATH', $_SERVER['REQUEST_URI'] . 'src/8thWonderland/public/images/' );
define ( 'ICO_PATH', $_SERVER['REQUEST_URI'] . 'src/8thWonderland/public/icones/' );
define ( 'JS_PATH', $_SERVER['REQUEST_URI'] . 'src/8thWonderland/public/js/' );

$application = new Wonderland\Library\Application();
$application->init();
$application->run();