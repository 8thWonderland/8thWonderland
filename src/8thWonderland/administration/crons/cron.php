<?php
/**
 * Execution de taches cron
 *
 * @author: Brennan Waco
 *
 */
 
// Définition des chemins absolus
// ==============================
// Architecture Serveur
defined('CLASSES_PATH')
	|| define ('CLASSES_PATH', './classes/');
set_include_path(CLASSES_PATH);


// Configuration de la Base de données
defined('BDD_SERVER')	|| define ( 'BDD_SERVER', 'mysql5-2.300gp' ); // adresse du serveur
defined('BDD_NAME')	|| define ( 'BDD_NAME', 'thwonderbdd' ); // nom de la base
defined('BDD_USER')	|| define ( 'BDD_USER', 'thwonderbdd' ); // nom d'utilisateur
defined('BDD_PWD')	|| define ( 'BDD_PWD', 'j5pk8yG6pc7' ); // mot de passe


// Chargement de l'autoloader
// ==========================
require_once CLASSES_PATH . 'loader/autoloader.php';
$loader = Autoloader::getInstance();


$db = cl_database::Initialise('Mysql', BDD_SERVER, BDD_USER, BDD_PWD, BDD_NAME);
$t = explode(" ", microtime());


// Suppression des citoyens qui ne se sont pas connectés il y a plus de 6 mois
// ---------------------------------------------------------------------------
$date_limite = intval($t[1]) - (60 * 60 * 24 * 182.625);		// 6 mois en arrière
$nb_CptSuppr = $db->Supprimer("Utilisateurs", "DerConnexion<" . $date_limite);


// Suppression des Tasks résolues depuis plus de 10 jours
// ------------------------------------------------------
$date_limite = intval($t[1]) - (60 * 60 * 24 * 10);		// 10 jours en arrière
$nb_TasksSuppr = $db->Supprimer("Tasks", "RE_Date IS NOT NULL AND RE_Date<" . $date_limite);

?>