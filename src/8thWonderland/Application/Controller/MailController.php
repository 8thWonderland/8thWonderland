<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;
use Wonderland\Application\Model\Mailer;

class MailController extends ActionController
{
    /** @var string **/
    protected $contactStatus;
    /** @var string **/
    protected $defaultFile;

    public function envoiAction()
    {
        $translate = $this->application->get('translator');
        $this->contactStatus = $translate->translate('mail_ok');
        $this->process();

        $this->defaultFile = APPLICATION_PATH.'views/communications/contactus.view';

        return $this->render('informations/menu_presentation');
    }

    protected function process()
    {
        $email = $_POST['mail_sender'];
        $res = null;
        $user = null;
        $domain = null;
        $MXHost = null;
        preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $email, $res);

        $translator = $this->application->get('translator');

        if (!$res || $res[0] !== $email || empty($email)) {
            $this->contactStatus = $translator->translate('mail_invalide');
        } else {
            list($user, $domain) = explode('@', $email);
            if (!getmxrr($domain, $MXHost)) {
                $this->contactStatus = $translator->translate('mail_invalide');
            } else {
                $mail = new Mailer();
                $mail->addRecipient('developpeurs@8thwonderland.com', '');
                $mail->addFrom($email, '');
                $mail->addSubject($_POST['mail_title'], '');
                $mail->html =
                    '<table><tr><td>'.utf8_decode('Identité').' :<br/>====================</td></tr>'.
                    "<tr><td>{$_POST['mail_sender']}<br/></td></tr>".
                    '<tr><td>Message :<br/>====================</td></tr>'.
                    '<tr><td>'.nl2br(htmlspecialchars(utf8_decode($_POST['mail_message']))).'</td></tr></table>'
                ;
                if (!$mail->send()) {
                    $this->contactStatus = $mail->errorLog();
                }
            }
        }
    }
}
