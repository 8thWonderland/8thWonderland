<?php

/**
 * Gestion des actions liées à la messagerie
 *
 * @author Brennan
 */


class mail extends controllers_action {
    
    public function envoiAction()
    {
        $translate = memory_registry::get("translate");
        $this->contact_status = $translate->msg('mail_ok');
        $this->_process();

        $this->_view['translate'] = $translate;
        $this->default_file = APPLICATION_PATH . "views/communications/contactus.view";
        $this->render("informations/menu_presentation");
    }
    
    
    protected function _process()
    {
        $email = $_POST['mail_sender'];
        $res = null; $user = null; $domain = null; $MXHost = null;
        $Modele = "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
        preg_match($Modele, $email, $res);
        
        if (!$res || $res[0] != $email || empty($email))	{   $this->contact_status = memory_registry::get("translate")->msg('mail_invalide');     }
        else
        {
            list($user, $domain) = explode("@",$email);
            if(!getmxrr($domain, $MXHost))		{   $this->contact_status = memory_registry::get("translate")->msg('mail_invalide');	}
            else {
                $mail = mailer::getInstance();
                $mail -> addrecipient('developpeurs@8thwonderland.com','');
                $mail -> addfrom($email,'');
                $mail -> addsubject($_POST['mail_title'],'');
                $message = "<table>" .
                    "<tr><td>" . utf8_decode('Identité') . " :<br/>====================</td></tr>" .
                    "<tr><td>" . $_POST['mail_sender'] . "<br/></td></tr>" .
                    "<tr><td>Message :<br/>====================</td></tr>" .
                    "<tr><td>" . nl2br(htmlspecialchars(utf8_decode($_POST['mail_message']))) . "</td></tr>" .
                    "</table>";
                $mail->html = $message;
                if (!$mail->envoi())        {   $this->contact_status = $mail->error_log();    }
            }
        }
    }
}
?>
