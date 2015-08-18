<?php
/**
 * Purge des données utilisées pour le 'mot de passe oublié' au bout d'un délai défini par DELAY_PURGE
 *
 * @author: Brennan Waco
 *
 */

// Environnement de l'application
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
defined('APPLI_INI')
    || define('APPLI_INI', $_SERVER['DOCUMENT_ROOT'] . '/application/config/application.ini');

require_once $_SERVER['DOCUMENT_ROOT'] . '/library/application.php';
$appli = new application(APPLICATION_ENV, APPLI_INI);

defined('DELAY_PURGE')                                                          // durée de sauvegarde des recovery
	|| define ('DELAY_PURGE', 3);

$db = memory_registry::get("db");
$db_log = new Log("db");


$db->_query("DELETE FROM recovery WHERE DATEDIFF(NOW(), daterecovery) > " . DELAY_PURGE);

if (!empty($db->error)) {
    // Journal de log
    $db_log->log("ERR: " . $db->error . " (" . $_SERVER['PHP_SELF'] . ")", Log::ERR);
} else {
    // Journal de log
    $db_log->log("Exécution de " . $_SERVER['PHP_SELF'] . ".", Log::INFO);
}
?>
