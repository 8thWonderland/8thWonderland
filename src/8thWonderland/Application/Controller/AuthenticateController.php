<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Auth;

use Wonderland\Library\Database\Mysqli;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Mailer;

use Wonderland\Library\Memory\Registry;

/**
 * Gestion des connexions/deconnexions des utilisateurs
 *
 * @author Brennan
 */
class AuthenticateController extends ActionController {

    public function connectAction()
    {
        $this->viewParameters['appli_status'] = 1;
        $valid = $this->_process($_POST['login'], $_POST['password']);

        if ($valid)
        {
            $auth = Auth::getInstance();
            $db = Registry::get('db');
            $member = Member::getInstance();
            
            
            // Enregistrement de la date et heure de la connexion
            // ==================================================            
            $db->query("UPDATE Utilisateurs SET DerConnexion=NOW() WHERE IDUser = " . $auth->_getIdentity());
            if ($db->affected_rows == 0)    {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update de la connexion (" . $member->identite . ")", Log::ERR);
            }
            
            
            // Mémorisation de l'ID du membre
            // ==============================
            Registry::set("__login__", $member->identite); // indipensable pour l'identification au forum
            $translate = Registry::get('translate');
            $translate->setUserLang($member->langue);
            $this->redirect("Intranet/index");
        }
        else
        {
            $translate = Registry::get("translate");
            $this->viewParameters['translate'] = $translate;
            $this->display(json_encode(array("status" => 0, "reponse" => '<span style="color: red;">' . $translate->translate('connexion_nok') . '</span>')));
        }

    }


    public function logoutAction()
    {
        // Verrouillage de la déconnexion. -- Pas compris pourquoi ?! --
        //$auth = $this->_getAuthAdapter();
        //$auth->logout();
    }
    
    
    public function subscribeAction()
    {
        $translate = Registry::get("translate");
        $err_msg = '';
        
        // Controle si tous les champs sont saisis
        // =======================================
        if (!isset($_POST['login']) || empty($_POST['login']) || !isset($_POST['password']) || empty($_POST['password']) || 
            !isset($_POST['identity']) || empty($_POST['identity']) || !isset($_POST['gender']) || empty($_POST['gender']) ||
            !isset($_POST['mail']) || empty($_POST['mail']) || !isset($_POST['lang']) || empty($_POST['lang'])) {
                $err_msg = $translate->translate('fields_empty');
        } else {
            $db = Registry::get('db');
            
            // Controle de l'identite
            if (Member::ctrlIdentity($_POST["identity"])) {
                if ($db->count("Utilisateurs", " WHERE Identite='" . $_POST['identity'] . "'") > 0) {
                    $err_msg .= $translate->translate('identity_exist') . "<br/>";
                }
            } else {
                $err_msg = $translate->translate('identity_invalid') . "<br/>";
            }
            
            // Controle de l'existence du mail
            if (!Member::ctrlMail($_POST["mail"])) {
                $err_msg .= $translate->translate('mail_invalid') . "<br/>";
            }
        }
        
        if (!empty($err_msg)) {
            $this->display('<div class="error" style="padding:3px"><table><tr>' .
                   '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                   '<td><span style="font-size: 13px;">' . $err_msg . '</span></td>' .
                   '</tr></table></div>');
        } else {
            // Enregistrement des infos du membre
            // ==================================
            $db->query("INSERT INTO Utilisateurs (Login, Password, Identite, Sexe, Email, Langue, Region, Inscription) " .
                        "VALUES ('" . $_POST['login'] . "', '" . hash('sha512', $_POST['password']) . "', '" . $_POST['identity'] . "', " . 
                        ($_POST['gender']-1) . ", '" . $_POST['mail'] . "', '" . $_POST['lang'] . "', -2, NOW())");
            if ($db->affected_rows == 0)    {
                $err_msg .= $translate->translate('error') . "<br/>";
            }
            if (empty($err_msg))    {   $err_msg = $translate->translate('subscribe_ok');     }
            $this->display('<div class="info" style="height:28px;"><table><tr>' .
                   '<td><img alt="info" src="' . ICO_PATH . '32x32/valid.png" style="width:24px;"/></td>' .
                   '<td><span style="font-size: 13px;">' . $err_msg . '</span></td>' .
                   '</tr></table></div>');
        }
    }
    
    
    public function display_forgetpasswordAction()
    {
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->render("members/forget_password");
    }
    
    
    public function forget_passwordAction()
    {
        $translate = Registry::get("translate");
        $err_msg = '';
        if (!isset($_POST['login']) || empty($_POST['login']))    {   $err_msg = $translate->translate('fields_empty');     }
        else {
            $db = Mysqli::getInstance();
            $email = $db->select("SELECT email FROM Utilisateurs WHERE login='" . $_POST['login'] . "'");
            if (count($email) ==0)             {   $err_msg = $translate->translate('no_result');     }
            else {
                $code_generated = $this->generate_code($_POST['login']);
                
                $mail = Mailer::getInstance();
                $mail -> addrecipient($email[0]['email'],'');
                $mail -> addfrom('developpeurs@8thwonderland.com','');
                $mail -> addsubject('8thwonderland - ' . $translate->translate('forget_pwd'),'');
                $mail->html = $translate->translate('mail_forgetpwd') . $code_generated;
                if (!$mail->envoi())        {   $err_msg = $mail->error_log();    }
            }
        }
        
        if (!empty($err_msg)) {
           $this->display('<div class="error" style="padding:3px"><table style="width:70%"><tr>' .
               '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
               '<td><span style="font-size: 13px;">' . $err_msg . '</span></td>' .
               '</tr></table></div>');
        } else {
            $this->display('<form id="form_forgetpwdCode" name="form_forgetpwdCode" enctype="application/x-www-form-urlencoded" action="" method="post" ' .
                'onSubmit=\'Envoi_form("/authenticate/valid_codeforgetpwd", "form_forgetpwdCode", "reponse_forgetpwdcode"); return false;\' >' .
                '<table><tr><td>' . $translate->translate("code_forgetpwd") . '</td>' .
                '<td><input type="text" name="code" id="code" style="width:70%" /></td>' .
                '<tr><td colspan="2" align="center"><input type="submit" name="btn_forgetpwdcode" id="btn_forgetpwdcode" value="' . $translate->translate('btn_codeforgetpwd') . '"></td>' .
                '</tr>' .
                '<tr><td><input id="memo_login" name="memo_login" type="hidden" value="' . $_POST['login'] . '"/></td><td id="reponse_forgetpwdcode"></td></tr>' .
                '</table></form>');
        }
    }
    
    
    public function valid_codeforgetpwdAction()
    {
        $translate = Registry::get("translate");
        $err_msg = '';
        if (!isset($_POST['code']) || empty($_POST['code']))    {   $err_msg = $translate->translate('fields_empty');     }
        else {
            $db = Mysqli::getInstance();
            if ($db->count("recovery", " WHERE code=" . $_POST['code'] . " AND login='" . $_POST['memo_login'] . "'") == 0) {   $err_msg = $translate->translate('no_result');     }
            else {
                $email = $db->select("SELECT email FROM Utilisateurs WHERE login='" . $_POST['memo_login'] . "'");
                if (count($email) ==0)             {   $err_msg = $translate->translate('no_result');     }
                else {
                    $new_pwd = $this->createPassword();
                    $mail = Mailer::getInstance();
                    $mail -> addrecipient($email[0]['email'],'');
                    $mail -> addfrom('developpeurs@8thwonderland.com','');
                    $mail -> addsubject('8thwonderland - ' . $translate->translate('forget_pwd'),'');
                    $mail->html = $translate->translate('mail_newpwd') . $new_pwd;
                    if (!$mail->envoi())        {   $err_msg = $mail->error_log();    }
                    else {
                        $req = "UPDATE Utilisateurs SET password='" . hash('sha512', $new_pwd) . "' WHERE login='" . $_POST['memo_login'] . "'";
                        $db->query($req);
                        if ($db->affected_rows == 0) {
                            // log d'échec de mise à jour
                            $db_log = new Log("db");
                            $db_log->log("Echec de changement du mot de passe (" . $_POST['memo_login'] . ")", Log::ERR);
                        }
                    }
                }
            }
        }
        
        if (!empty($err_msg)) {
           $this->display('<div class="error" style="padding:3px"><table style="width:70%"><tr>' .
               '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
               '<td><span style="font-size: 13px;">' . $err_msg . '</span></td>' .
               '</tr></table></div>');
        } else {
           $this->display('<div class="info" style="padding:3px"><table style="width:70%"><tr>' .
               '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:24px;"/></td>' .
               '<td><span style="font-size: 13px;">' . $translate->translate('reponse_newpwd') . '</span></td>' .
               '</tr></table></div>');
        }
    }
    
    
    protected function generate_code($login)
    {
        $db = Mysqli::getInstance();
        $code = rand(10000, 99999);
        while ($db->count("recovery", " WHERE code=" . $code) > 0)
        {
            $code = rand(10000, 99999);
        }
        if ($db->count("recovery", " WHERE login='" . $login . "'") > 0)    {      $req = "UPDATE recovery SET code=" . $code;     }
        else                                                                {      $req = "INSERT INTO recovery (login, code) VALUES ('" . $login . "', " . $code . ")";     }
        $db->query($req);
        
        return $code;
    }
    
    
    protected function createPassword() 
    {
	$chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$i = 0;
	$password = "";
	while ($i <= 8) {
		$password .= $chars{mt_rand(0,strlen($chars)-1)};
		$i++;
	}
	return $password;
    }


    private function _process($login, $password)
    {
        $auth = $this->_getAuthAdapter();
        $connected = $auth->authenticate($login, hash('sha512', $password));

        if ($connected != true)
        {
            // log d'échec de connexion
            (isset($_SERVER['REMOTE_ADDR']))?$ip = $_SERVER['REMOTE_ADDR']:$ip='inconnu';
            $db_log = new Log("db");
            $db_log->log("Echec de la connexion (" . $ip . ")", Log::WARN);
        }

        return $connected;
    }


    private function _getAuthAdapter()
    {
        $auth = Auth::getInstance(Registry::get("db"));
        $auth->setTableName('Utilisateurs');
        $auth->setIdentityColumn('Login');
        $auth->setCredentialColumn('Password');

        return $auth;
    }

}
?>
