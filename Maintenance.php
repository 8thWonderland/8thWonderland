<?php

/**
 * @version $Id$
 *
 * @copyright 2011
 */

// Sélection de la langue
if (isset($_GET['langue'])) {
    $_SESSION['langue'] = $_GET['langue'];
} else {
    if (!isset($_SESSION['langue'])) {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lg_nav = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        } else {
            $lg_nav = '<inconnue>';
        }
        $lg_nav = explode(',', $lg_nav);
        $tmp = $lg_nav[0];
        $tmp = explode('-', $tmp);
        $_SESSION['langue'] = $tmp[0];
    }
}
if ($_SESSION['langue'] != 'en' && $_SESSION['langue'] != 'de' && $_SESSION['langue'] != 'fr') {
    $_SESSION['langue'] = 'en';
}
include_once 'Intranet/langues/'.$_SESSION['langue'].'.php';
$lang = $array;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<title>8th Wonderland</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" href="Styles/general.css" type="text/css" media="screen" />
		<link rel="shortcut icon" type="image/x-icon" href="Ressources/Images/favicon.png" />
	</head>

	<body>
		<div id="logo" class="Logo_Gris"></div>
		
		<div id="Fond" class="Fond">
			<div id='erreur' class='Panneau_Appli' style='position: fixed; left:25%; right:25%; width:50%; top:120px;'>
				<table class='Panneau_TitreAppli'><tr><td width='100%'></td></tr></table>
				<div id='Contenu' class='Panneau_ContenuAppli' style='background-color:#F9DBAE; color:black; width:100%'>
					<table cellpadding='0' cellspacing='0' style="width:100%"><tr style='height:150px; width:100%;'>
						<td align='center' style='width:50px'><img src='./Ressources/Images/Erreur.png' alt='' style='border:0'></td>
						<td align='center'><?= utf8_decode($lang['site_maintenance']); ?></td></tr></table>
				</div>
			</div>
		</div>
	</body>
</html>