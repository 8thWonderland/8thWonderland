<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Application\Model\Mailer;

use Wonderland\Library\Exception\BadRequestException;

class AuthenticateController extends ActionController {
    public function connectAction() {
        $request = $this->getJsonRequest();
        $memberManager = $this->application->get('member_manager');
        $translate = $this->application->get('translator');
        
        if (($member = $memberManager->getMemberByLoginAndPassword($request['login'], hash('sha512', $request['password']))) !== null) {
            $db = $this->application->get('database_connection');
            // Enregistrement de la date et heure de la connexion         
            $statement = $db->prepare(
                'UPDATE users SET last_connected_at = NOW() WHERE id = :id'
            );
            if ($statement->execute(['id' => $member->getId()]) === false)    {
                // log d'échec de mise à jour
                $logger = $this->application->get('logger');
                $logger->setWriter('db');
                $logger->log("Echec de l'update de la connexion ({$member->getIdentity()})", Log::ERR);
            }
            $this->application->get('session')->set('__id__', $member->getId());
            $translate->setUserLang($member->getLanguage());
            $this->redirect('intranet/index');
        } else {
            header("{$_SERVER['SERVER_PROTOCOL']} 400 Bad Request");
            echo json_encode([
                'errors' => [$translate->translate('connection_failed')]
            ]);
            exit;
        }
    }

    public function logoutAction() {
        $this->application->get('session')->delete('__id__');
        header('Location: ' . $_SERVER['PHP_SELF']);
    }
    
    public function subscribeAction() {
        $request = $this->getJsonRequest();
        
        if (
            empty($request['login']) || empty($request['password']) || 
            empty($request['confirmation_password']) || empty($request['email']) ||
            empty($request['country_id']) || empty($request['region_id'])
        ) {
            throw new BadRequestException($this->application->get('translator')->translate('fields_empty'));
        }
        header('Content-Type: application/json; charset=UTF-8');
        
        $result = $this->application->get('member_manager')->create(
            $request['login'],
            $request['password'],
            $request['confirmation_password'],
            $request['email'],
            $request['country_id'],
            $request['region_id']
        );
        // In case of errors, the manager returns an array containing it
        if($result !== true) {
            header("{$_SERVER['SERVER_PROTOCOL']} 400 Bad Request");
            echo json_encode([
                'errors' => $result
            ]);
            exit;
        }
        header("{$_SERVER['SERVER_PROTOCOL']} 201 Created");
    }
    
    public function displayForgetPasswordAction() {
        $this->render('members/forget_password');
    }
    
    public function forgetPasswordAction() {
        $translate = $this->application->get('translator');
        
        if (empty($_POST['login'])) {
            $err_msg = $translate->translate('fields_empty');
        } else {
            $email = $this->application->get('database_connection')->prepareStatement(
                "SELECT email FROM users WHERE login = :login"
            , ['login' => $_POST['login']])->fetch(\PDO::FETCH_ASSOC)['email'];
            if (count($email) === 0) {
                $err_msg = $translate->translate('no_result');
            } else {
                $code_generated = $this->generateCode($_POST['login']);
                
                $mail = new Mailer();
                $mail->addRecipient($email[0]['email'], '');
                $mail->addFrom('developpeurs@8thwonderland.com', '');
                $mail->addSubject('8thwonderland - ' . $translate->translate('forget_pwd'),'');
                $mail->html = $translate->translate('mail_forgetpwd') . $code_generated;
                if (!$mail->envoi()) {
                    $err_msg = $mail->errorLog();
                }
            }
        }
        
        if (!empty($err_msg)) {
            $this->display(
                '<div class="error" style="padding:3px"><table style="width:70%"><tr>' .
                '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                '<td><span style="font-size: 13px;">' . $err_msg . '</span></td>' .
                '</tr></table></div>'
            );
        } else {
            $this->display(
                '<form id="form_forgetpwdCode" name="form_forgetpwdCode" enctype="application/x-www-form-urlencoded" action="" method="post" ' .
                'onSubmit=\'sendForm("/authenticate/valid_codeforgetpwd", "form_forgetpwdCode", "reponse_forgetpwdcode"); return false;\' >' .
                '<table><tr><td>' . $translate->translate("code_forgetpwd") . '</td>' .
                '<td><input type="text" name="code" id="code" style="width:70%" /></td>' .
                '<tr><td colspan="2" align="center"><input type="submit" name="btn_forgetpwdcode" id="btn_forgetpwdcode" value="' . $translate->translate('btn_codeforgetpwd') . '"></td>' .
                '</tr>' .
                '<tr><td><input id="memo_login" name="memo_login" type="hidden" value="' . $_POST['login'] . '"/></td><td id="reponse_forgetpwdcode"></td></tr>' .
                '</table></form>'
            );
        }
    }
    
    public function validCodeForgetPasswordAction() {
        $translate = $this->application->get('translator');
        
        if (empty($_POST['code'])) {
            $err_msg = $translate->translate('fields_empty');
        } else {
            $db = $this->application->get('database_connection');
            if ($db->count('recovery', " WHERE code={$_POST['code']} AND login='{$_POST['memo_login']}'") === 0) {
                $err_msg = $translate->translate('no_result');
            } else {
                $email = $db->select("SELECT email FROM Utilisateurs WHERE login='{$_POST['memo_login']}'");
                if (count($email) === 0) {
                    $err_msg = $translate->translate('no_result');
                } else {
                    $new_pwd = $this->createPassword();
                    $mail = new Mailer();
                    $mail->addRecipient($email[0]['email'], '');
                    $mail->addFrom('developpeurs@8thwonderland.com', '');
                    $mail->addSubject('8thwonderland - ' . $translate->translate('forget_pwd'), '');
                    $mail->html = $translate->translate('mail_newpwd') . $new_pwd;
                    if (!$mail->envoi()) {
                        $err_msg = $mail->error_log();
                    } else {
                        $db->query(
                            "UPDATE Utilisateurs SET password='" . hash('sha512', $new_pwd) . "' WHERE login='{$_POST['memo_login']}'"
                        );
                        if ($db->affected_rows == 0) {
                            // log d'échec de mise à jour
                            $logger = $this->application->get('logger');
                            $logger->setWriter('db');
                            $logger->log("Echec de changement du mot de passe ({$_POST['memo_login']})", Log::ERR);
                        }
                    }
                }
            }
        }
        
        if (!empty($err_msg)) {
           $this->display(
                '<div class="error" style="padding:3px"><table style="width:70%"><tr>' .
                '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                '<td><span style="font-size: 13px;">' . $err_msg . '</span></td>' .
                '</tr></table></div>'
            );
        } else {
           $this->display(
                '<div class="info" style="padding:3px"><table style="width:70%"><tr>' .
                '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:24px;"/></td>' .
                '<td><span style="font-size: 13px;">' . $translate->translate('reponse_newpwd') . '</span></td>' .
                '</tr></table></div>'
            );
        }
    }
    
    /**
     * @param string $login
     * @return int
     */
    protected function generateCode($login) {
        $db = $this->application->get('database_connection');
        $code = rand(10000, 99999);
        while ($db->count('recovery', " WHERE code=" . $code) > 0) {
            $code = rand(10000, 99999);
        }
        $req =
            ($db->count('recovery', " WHERE login='$login'") > 0)
            ? "UPDATE recovery SET code = $code"
            : "INSERT INTO recovery (login, code) VALUES ('$login', $code)"
        ;
        $db->query($req);
        
        return $code;
    }
    
    /**
     * @return string
     */
    protected function createPassword() {
	$chars = '234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$password = '';
	for ($i = 0; $i <= 8; ++$i) {
            $password .= $chars{mt_rand(0, strlen($chars) - 1)};
	}
	return $password;
    }
}
