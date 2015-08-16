<?php
/**
 * Page principale
 *
 * @author: BrennanWaco - waco.brennan@gmail.com
 *
 */

// D�finition des chemins de l'application
// =======================================
// Environnement de l'application
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
// Architecture Serveur
defined('APPLICATION_PATH')
    || define ('APPLICATION_PATH', realpath(dirname(__FILE__)) . "/application/");
defined('VIEWS_PATH')
	|| define ( 'VIEWS_PATH', $_SERVER['DOCUMENT_ROOT'] . "/application/views/");
defined('APPLI_INI')
    || define('APPLI_INI', APPLICATION_PATH . 'config/application.ini');
// Ressources
defined('CSS_PATH')
    || define ( 'CSS_PATH', '/public/css/' );
defined('IMG_PATH')
    || define ( 'IMG_PATH', '/public/images/' );
defined('ICO_PATH')
    || define ( 'ICO_PATH', '/public/icones/' );
defined('JS_PATH')
    || define ( 'JS_PATH', '/public/js/' );
	
$_SERVER['DOCUMENT_ROOT'] = 'C:\Developpement\PHP\8thWonderland\site_23-04-2012';
	
/*
// Ressources
defined('MAGASIN_PATH')
	|| define ( 'MAGASIN_PATH', '/public/images/magasin/' );

// Motion
defined('MOTIONS_PATH')
	|| define ('MOTIONS_PATH', $_SERVER['DOCUMENT_ROOT'] . "/Intranet/motions/");*/



require_once 'library/application.php';
$appli = new application(APPLICATION_ENV, APPLI_INI);
$appli->run();

exit(0);

?>