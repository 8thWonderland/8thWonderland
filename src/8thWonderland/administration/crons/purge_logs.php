<?php
/**
 * Purge des logs au bout d'un délai défini par DELAY_PURGE
 *
 * @author: Brennan Waco
 *
 */

// Environnement de l'application
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
defined('APPLI_INI')
    || define('APPLI_INI', $_SERVER['DOCUMENT_ROOT'] . '/application/config/application.ini');
defined('LIBRARY')
    || define('LIBRARY', $_SERVER['DOCUMENT_ROOT'] . '/library/');


require_once LIBRARY . 'application.php';
$appli = new application(APPLICATION_ENV, APPLI_INI);

defined('DELAY_PURGE')                                                          // durée de sauvegarde des logs
	|| define ('DELAY_PURGE', 7);

$db = memory_registry::get("db");
$db_log = new Log("db");

$db->_query("DELETE FROM logs WHERE DATEDIFF(NOW(), timelogs) > " . DELAY_PURGE);

if (!empty($db->error)) {
    // Journal de log
    $db_log->log("ERR: " . $db->error . " (" . $_SERVER['PHP_SELF'] . ")", Log::ERR);
} else {
    // Journal de log
    $db_log->log("Exécution de " . $_SERVER['PHP_SELF'] . ".", Log::INFO);
}

?>
