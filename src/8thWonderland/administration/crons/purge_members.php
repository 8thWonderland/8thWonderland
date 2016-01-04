<?php
/**
 * Purge des membres au bout d'un délai défini par DELAY_PURGE.
 *
 * @author: Brennan Waco
 */

// Environnement de l'application
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
defined('APPLI_INI')
    || define('APPLI_INI', APPLICATION_PATH.'config/application.ini');

require_once 'library/application.php';
$appli = new application(APPLICATION_ENV, APPLI_INI);

defined('DELAY_PURGE')                                                          // durée de sauvegarde des comptes inactifs
    || define('DELAY_PURGE', 90);

$db = memory_registry::get('db');
$db_log = new Log('db');

$db->query('DELETE FROM Utilisateurs WHERE DATEDIFF(NOW(), DerConnexion) > '.DELAY_PURGE);

if (!empty($db->error)) {
    // Journal de log
    $db_log->log('ERR: '.$db->error.' ('.$_SERVER['PHP_SELF'].')', Log::ERR);
} else {
    // Journal de log
    $db_log->log('Exécution de '.$_SERVER['PHP_SELF'].'.', Log::INFO);
}
