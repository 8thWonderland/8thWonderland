<?php
/**
 * Envoi un mail aux citoyens qui vont devenir "absent"
 * Ce script est normalement appellé toutes les heures par cron.php.
 *
 * @author: Oros - oros.citizen@hotmail.fr
 *
 * Dernière modification : 18/04/2011
 */
$actif = true;

if (!$actif) {
    exit;
}
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Gestion_Erreurs.php';

// Gestion des erreurs
// -------------------
function Traite_Erreurs($p_type, $p_msg, $p_file, $p_line, $p_context)
{
    throw new g_Erreurs($p_type, $p_msg, $p_file, $p_line, $p_context);
}
set_error_handler('Traite_Erreurs');

try {
    $nbmailenvoye = 0;

        // récupère les utilisateurs qui ne se sont pas connectés
        // depuis 3 semaines - 1 jour
        foreach (Utilisateurs::_GetAbtents() as $utisilateur) {
            switch ($utisilateur['Langue']) {
                case 'fr':
                        $objet_message = 'Objet : 8th Wonderland - inactivité';
                        $message = 'Bonjour '.$utisilateur['Login'].'

Vous ne vous êtes pas connectés depuis 3 semaines.
Suite à la motion du 30 Mars 2011, "MOTION CONSTITUTIONNELLE - Réforme du système de vote 2",
si vous ne vous reconnectez pas au site, vous serez considéré comme absent.

Pour vous connecter au site : http://www.8thwonderland.com

En cas de perte de vos accès : http://www.8thwonderland.com/Perte_Acces.php

Si vous ne souhaitez plus être citoyen, vous-pouvez vous dénaturaliser en allant dans "Mon passeport" puis cliquer sur "supprimer".


Pensez-y. Nous avons besoin de vous!';
                    break;

/*                case "de":
                         $objet_message="";
                         $message="";
                    break;
*/
                case 'en':
                default:
                         $objet_message = 'Subject : 8th Wonderland - inactivity';
                         $message = 'Hello '.$utisilateur['Login'].'

You did not connecte for 3 weeks.
Following the motion of March 30, 2011 "CONSTITUTIONAL MOTION - Reforming the voting system 2",
if you do not reconnect to the site, you will be considered absent.

To connect to the site: http://www.8thwonderland.com

If you losed your access: http://www.8thwonderland.com/Perte_Acces.php

If you no longer wish to be a citizen, you can denaturalize by going to "My passport" then click "delete".


Think about it. We need you!';

                    break;
            }

            $headers = 'From: "8th Wonderland"<postmaster@8thwonderland.com>'."\n";
            $headers .= 'Content-Type: text/html; charset="utf-8"'."\n";
            $headers .= 'Content-Transfer-Encoding: 8bit';

            ++$nbmailenvoye;

           // echo $nbmailenvoye ." " .$utisilateur['IDUser']. " | ".$utisilateur['Login']. " | ".$utisilateur['Email']."<br/>\n";

            if (mail($utisilateur['Email'], $objet_message, nl2br($message), $headers)) { // envoyer message
                 Utilisateurs::_SetAbtents($utisilateur['IDUser'], true);
            }

            // on n'envoit pas plus de 400 mails
            if ($nbmailenvoye >= 400) {
                break;
            }
        }
} catch (g_Erreurs $Err) {
    $headers = 'From: "Journal d\'erreur de 8th Wonderland"<postmaster@8thwonderland.com>'."\n";
    $headers .= 'Content-Type: text/html; charset="utf-8"'."\n";
    $headers .= 'Content-Transfer-Encoding: 8bit';

    $mail_Destinataire = 'oros.citizen@hotmail.fr, waco.brennan@gmail.com';

    $objet_message = 'Erreur avec le mail pour les absents';

    $message = "Rapport d'erreur de l'envoit du mail aux absents le ".date('d-m-Y H:i:s')."\n\n".$Err->Affiche_Erreur_Complet();
    mail($mail_Destinataire, $objet_message, nl2br($message), $headers);
}
