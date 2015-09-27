<?php

namespace Wonderland\Application\Manager;

class MailManager {
    protected $recipientlist;
    protected $subject;
    protected $hfrom;
    protected $hbcc;
    protected $hcc;
    protected $deliveryreceipt;

    protected $Xsender;
    protected $ErrorsTo;
    protected $returnpath;
    protected $XMailer = 'PHP';
    protected $XPriority = 3;

    protected $set_mode='php';

    protected $text;
    protected $html;
    protected $attachement = array();
    protected $htmlAttachement = array();

    protected $recipient;

    protected $body;
    protected $headers;
    protected $error_log;
    protected $connect;

    protected $default_charset = 'iso-8859-1';

    protected $B1B = "----=_001";
    protected $B2B = "----=_002";
    protected $B3B = "----=_003";

    public function simpleMail() {
        $this->attachement = array();
        $this->htmlAttachement = array();
    }

    /**
     * @param string $address
     * @return boolean
     */
    public function checkAddress($address) {
        if (!preg_match('`([[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,4}))`', $address) ) {
            $this->errorLog("l'adresse $address est invalide");
            return false;
        }
        return true;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function checkName($name) {
        if ( !preg_match("`[0-9a-zA-Z\.\-_ ]*`" , $name ) ) {
            $this->errorLog(" le pseudo $name est invalide\n");
            return false;
        }
        return true;
    }

    /**
     * @param string $address
     * @param string $name
     * @return string|boolean
     */
    public function formatAddress($address, $name) {
        if (!$this->checkAddress($address) || !$this->checkName($name)) {
            return false;
        }
        if (empty($name)) {
            return $address;
        }
        return "$name <$address>";
    }

    /**
     * @param string $newRecipient
     * @param string $name
     * @return boolean
     */
    public function addRecipient($newRecipient, $name = '') {
        if (!($tmp = $this->formatAddress($newRecipient, $name))) {
            $this->errorLog(' To: error');
            return false;
        }
        $this->recipientlist[] = [
            'mail' => $newRecipient,
            'nameplusmail' => $tmp
        ];
        return true;
    }

    /**
     * @param string $bcc
     * @param string $name
     * @return boolean
     */
    public function addBcc($bcc, $name = '') {
        if (!($tmp = $this->formatAddress($bcc,$name))) {
            $this->errorLog(' Bcc: error');
            return false;
        }
        if (!empty($this->hbcc)) {
            $this->hbcc.= ',';
        }
        $this->hbcc.= $tmp;
        return true;
    }

    /**
     * @param string $cc
     * @param string $name
     * @return boolean
     */
    public function adDcc($cc, $name='') {
        if (!($tmp = $this->formatAddress($cc, $name))) {
            $this->errorLog(" Cc: error\n");
            return false;
        }
        if (!empty($this->hcc)) {
            $this->hcc .= ',';
        }
        $this->hcc .= $tmp;
        return true;
    }

    /**
     * @param string $subject
     */
    public function addSubject($subject) {
        if (!empty($subject)) {
            $this->subject = $subject;
        }
    }
    
    /**
     * @param string $from
     * @param string $name
     * @return boolean
     */
    public function addFrom($from, $name='') {
        if (!($tmp = $this->formatAddress($from, $name))) {
            $this->errorLog(" From: error");
            return false;
        }
        $this->hfrom = $tmp;
        return true;
    }

    /**
     * @param string $return
     * @return boolean
     */
    public function addReturnPath($return) {
        if (!($tmp = $this->formatAddress($return, ''))) {
            $this->errorLog("Return-Path: error");
            return false;
        }
        $this->returnpath = $return;
        return true;
    }

    /**
     * @param string $replyTo
     * @return boolean
     */
    public function addReplyTo($replyTo) {
        if (!($tmp = $this->formatAddress($replyTo, ''))) {
            $this->errorLog(" Reply-To: error");
            return false;
        }
        $this->returnpath = $tmp;
        return true;
    }

    /**
     * @param string $deliveryReceipt
     * @return boolean
     */
    public function addDeliveryReceipt($deliveryReceipt) {
        if (!($tmp = $this->formatAddress($deliveryReceipt,''))) {
            $this->errorLog(" Disposition-Notification-To: error");
            return false;
        }
        $this->deliveryreceipt = $tmp;
        return true;
    }

    /**
     * @param string $filename
     */
    public function addAttachement($filename) {
        array_push($this->attachement, ['filename' => $filename]);
    }

    /**
     * @param string $filename
     * @param string $cid
     * @param string $contentType
     */
    public function addHtmlAttachement($filename, $cid = '', $contentType = '') {
        array_push($this->htmlAttachement , [
            'filename' => $filename,
            'cid' => $cid,
            'contenttype' => $contentType
        ]);
    }

    /**
     * @param array $attachement
     * @param string $boundary
     * @return string
     */
    public function writeAttachement(&$attachement, $boundary) {
        $message = '';
        $inline = [];
        if (empty($attachement)) {
            return $message;
        }
        foreach($attachement as $AttmFile){
            $patharray = explode ("/", $AttmFile['filename']);
            $FileName = $patharray[count($patharray)-1];

            // If duplicate images are embedded, they may show up as attachments, so remove them.
            if (in_array($AttmFile['filename'], $inline)) {
                continue;
            }
            $message .= "\n--$boundary\n";

            if (!empty($AttmFile['cid'])) {
                $inline[] = $AttmFile['filename'];
                $message .=
                    "Content-Type: {$AttmFile['contenttype']};\n name=\"$FileName\"\n" .
                    "Content-Transfer-Encoding: base64\n" .
                    "Content-ID: <{$AttmFile['cid']}>\n" .
                    "Content-Disposition: inline;\n filename=\"$FileName\"\n\n"
                ;
            } else {
                $message .=
                    "Content-Type: application/octetstream;\n name=\"$FileName\"\n" .
                    "Content-Transfer-Encoding: base64\n" .
                    "Content-Disposition: attachment;\n filename=\"$FileName\"\n\n"
                ;
            }
            $fd = fopen($AttmFile['filename'], 'rb');
            $FileContent = chunk_split(base64_encode(
                fread($fd, filesize($AttmFile['filename']))
            ));
            fclose ($fd);
            $message .= "$FileContent\n\n";
        }
        return $message . "\n--$boundary--\n";
    }

    /**
     * @param string $value
     * @return string
     */
    public function bodyLineWrap($value) {
        return wordwrap($value, 78, "\n ");
    }

    /**
     * @return string
     */
    public function makeBody() {
        $message='';
        if (!$this->html && $this->text && !empty($this->attachement)) {
            //Messages start with text/html alternatives in OB
            $message =
                "This is a multi-part message in MIME format.\n" .
                "\n--{$this->B1B}\n" .
                "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
                "Content-Transfer-Encoding: quoted-printable\n\n" .
                // plaintext goes here
                $this->bodyLineWrap($this->text)."\n\n" .
                $this->writeAttachement($this->attachement, $this->B1B)
            ;
        }
        elseif (!$this->html && $this->text && empty($this->attachement)) {
            // plaintext goes here
            $message .= "{$this->BodyLineWrap($this->text)}\n\n";
        }
        elseif ($this->html) {

            //Messages start with text/html alternatives in OB
            $message =
                "This is a multi-part message in MIME format.\n" .
                "\n--{$this->B1B}\n" .
                "Content-Type: multipart/related;\n\t boundary=\"{$this->B2B}\"\n\n" .
                //plaintext section
                "\n--{$this->B2B}\n" .
                "Content-Type: multipart/alternative;\n\t boundary=\"{$this->B3B}\"\n\n" .
                //plaintext section
                "\n--{$this->B3B}\n" .
                "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
                "Content-Transfer-Encoding: quoted-printable\n\n" .
                // plaintext goes here
                $this->bodyLineWrap($this->text)."\n\n" .
                // html section
                "\n--{$this->B3B}\n" .
                "Content-Type: text/html; charset=\"iso-8859-1\"\n" .
                "Content-Transfer-Encoding: base64\n\n" .
                // html goes here
                chunk_split(base64_encode($this->html))."\n\n" .
                // end of text
                "\n--{$this->B3B}--\n"
            ;
            // html attachments
            $message.=
                (empty($this->htmlAttachement))
                ? "\n--".$this->B2B."--\n"
                : $this->writeattachement( $this->htmlAttachement,$this->B2B)
            ;
            // attachments
            $message .=
                (empty($this->attachement))
                ? "\n--{$this->B1B}--\n"
                : $this->writeattachement($this->attachement, $this->B1B)
            ;
        }
        $this->body = $message;
        return $message;
    }

    /**
     * @param string $field
     * @param string $value
     * @return string
     */
    public function makeHeaderField($field, $value) {
        return wordwrap("$field: $value", 78, "\n ")."\r\n";
    }

    /**
     * @param string $field
     * @param string $value
     */
    public function addHeaderField($field, $value) {
        $this->headers .= $this->makeHeaderField($field, $value);
    }

    /**
     * @return array
     */
    public function setHeaders() {
        $this->headers = '';

        if (empty($this->recipientlist)) {
            $this->errorLog("destinataire manquant");
            return false;
        }
        $this->addHeaderField("To",$this->recipient);

        if (empty($this->subject)) {
            $this->errorLog('sujet manquant');
            return false;
        }
        if ($this->set_mode!='php' ) {
            $this->addHeaderField("Subject", $this->subject);
        }

        # Date: Mon, 03 Nov 2003 20:48:06 +0100
        $this->addHeaderField('Date', date ('r'));
        $this->addHeaderField('X-Sender', (!empty($this->Xsender)) ? $this->Xsender : $this->hfrom);
        $this->addHeaderField('Errors-To', (!empty($this->ErrorsTo)) ? $this->ErrorsTo : $this->hfrom);
        $this->addHeaderField('Return-Path', (!empty($this->returnpath)) ? $this->returnpath : $this->hfrom);

        if (!empty($this->XMailer)) {
            $this->addHeaderField('X-Mailer',$this->XMailer);
        }
        if (!empty($this->XPriority)) {
            $this->addHeaderField('X-Priority', $this->XPriority);
        }
        if (!empty($this->hfrom)) {
            $this->addHeaderField('From', $this->hfrom);
        }
        if (!empty($this->replyto)) {
            $this->addHeaderField('Reply-To', $this->replyto);
        }
        if (!empty($this->deliveryreceipt)) {
            $this->addHeaderField('Disposition-Notification-To', $this->deliveryreceipt);
        }
        $this->headers .= "MIME-Version: 1.0\r\n";

        if (!$this->html && $this->text && !empty($this->attachement)) {
            $this->headers .= "Content-Type: multipart/mixed;\r\n\t boundary=\"{$this->B1B}\"\r\n";
        } elseif (!$this->html && $this->text && empty($this->attachement)) {
            $this->headers .= "Content-Type: text/plain; charset=utf8; format=flowed\r\n";
            $this->headers .= "Content-Transfer-Encoding: 8bit\r\n";
        } elseif ($this->html) {
            if (!$this->text) {
                $this->text = 'HTML only!';
            }
            $this->headers .="Content-Type: multipart/mixed;\r\n\t boundary=\"{$this->B1B}\"\r\n";
        }
        if (!empty($this->hcc)) {
            $this->addHeaderField('Cc', $this->hcc);
        }
        if (!empty($this->hbcc)) {
            $this->addHeaderField('Bcc', $this->hbcc);
        }
        return $this->headers;
    }

    /**
     * @return boolean
     */
    public function send() {
        $this->makeBody();
        $this->setHeaders();
        switch($this->set_mode)	{
            case 'php' : $this->phpMail(); break;
            case 'socket' : $this->socketMailLoop(); break;
        }
        return true;
    }

    /**
     * @return boolean
     */
    public function phpMail() {
        // DOLCHANGE LDR Fix the To in header was not filled
        foreach ($this->recipientlist as $key => $to)
        {
            $this->recipient = ($this->recipient?$this->recipient.', ':'').$to['mail'];
        }
        foreach ($this->recipientlist as $key => $to)
        {
            if ( mail($to['mail'], $this->subject, $this->body, $this->setHeaders() , '')) {
                $this->errorLog("envoi vers {$to['nameplusmail']} reussi");
            } else {
                $this->errorLog("envoi vers {$to['nameplusmail']} echoue");
            }
        }
        return true;
    }

    /**
     * @return boolean|string
     */
    public function SocketStart() {
        if (!$this->connect = fsockopen(ini_get('SMTP'), ini_get('smtp_port'), $errno, $errstr, 30))  {
            $this->errorLog('Could not talk to the sendmail server!');
            return false;
        };
        return fgets($this->connect, 1024);
    }

    /**
     * @return boolean
     */
    public function SocketStop() {
        fclose($this->connect);
        return true;
    }

    /**
     * @param string $in
     * @param string $wait
     * @return boolean|string
     */
    public function SocketSend($in, $wait = '') {
        fputs($this->connect, $in, strlen($in));
        $this->errorLog($in);	// DOLCHANGE LDR Add debug
        if(empty($wait)) {
            $rcv = fgets($this->connect, 1024);
            $this->errorLog("($rcv)");	// DOLCHANGE LDR Add debug
            return $rcv;
        }
        return true;
    }

    public function socketMailStart() {
        $this->SocketStart();
        $serv =
            (!empty($_SERVER['SERVER_NAME']))
            ? $_SERVER['SERVER_NAME']
            : 'unknown'
        ;
        $this->SocketSend("HELO $serv\r\n", '250');
    }

    /**
     * @param string $to
     * @return boolean
     */
    public function socketMailSend($to) {
        // $this->recipient = $to;
        // DOLCHANGE LDR Must not reset this property
        $this->errorLog("Socket vers $to");
        // DOLCHANGE LDR: From has to be the raw email address, strip the "name" off
        $fromArray = explode(' ', $this->hfrom);
        $from = (empty($fromArray[1]) ? $fromArray[0] : $fromArray[1]);
        
        $this->SocketSend("MAIL FROM: $from\r\n", '250');
        $this->SocketSend("RCPT TO: <$to>\r\n", '250');
        $this->SocketSend("DATA\r\n", '354');
        $this->SocketSend($this->cleanMailDataString($this->headers)."\r\n".$this->cleanMailDataString($this->body)."\r\n", '250');	// DOLCHANGE LDR Must wait return 250
        $this->SocketSend(".\r\n" );
        $this->SocketSend("RSET\r\n" );

        $this->errorLog("Fin de l'envoi vers $to");

        return true;
    }

    /**
     * @return boolean
     */
    public function socketMailStop() {
        $this->SocketSend("QUIT\r\n");
        $this->SocketStop();
        return true;
    }

    public function socketMailLoop() {
        $this->socketMailStart();
        // DOLCHANGE LDR Fix the To in header was not filled
        foreach ($this->recipientlist as $key => $to)
        {
            $this->recipient = ($this->recipient ? $this->recipient . ', ' : '') . $to['mail'];
        }
        foreach ($this->recipientlist as $key => $to)
        {
            $this->setHeaders();
            $this->socketMailSend($to['mail']);
        }
        $this->socketMailStop();
    }

    /**
     * @param string $msg
     * @return boolean|string
     */
    public function errorLog($msg = '') {
        if(!empty($msg)) {
            $this->error_log .= $msg;
            return true;
        }
        return $this->error_log;
    }

    public function cleanMailDataString($data) {
        return preg_replace(
            "/\n\./",
            "\n..",
            preg_replace(
                "/\n\n/",
                "\n\r\n",
                preg_replace(
                    "/([^\r]{1})\n/",
                    "\\1\r\n",
                    $data
                )
            )
        );
    }
}