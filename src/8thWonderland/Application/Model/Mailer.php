<?php

namespace Wonderland\Application\Model;

/**
 * class mailer
 *
 * Classe gérant les envois de mails de l'application
 *
 */
class Mailer {

	protected static $_instance;		// Singleton de la classe
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
	protected $htmlattachement = array();

	protected $recipient;

	protected $body;
	protected $headers;
	protected $error_log;
	protected $connect;

	protected $default_charset = 'iso-8859-1';

	protected $B1B = "----=_001";
	protected $B2B = "----=_002";
	protected $B3B = "----=_003";
	

	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	
	public function simplemail() {
		$this -> attachement = array();
		$this -> htmlattachement = array();
	}

	public function checkaddress($address) {
		if ( preg_match('`([[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,4}))`', $address) ) {
			return TRUE;
		} else {
			$this->error_log("l'adresse $address est invalide"); return FALSE;
		}
	}

	public function checkname($name) {
		if ( preg_match("`[0-9a-zA-Z\.\-_ ]*`" , $name ) ) {
			return TRUE;
		} else {
			$this->error_log(" le pseudo $name est invalide\n"); return FALSE;
		}
	}

	public function makenameplusaddress($address,$name) {
		if ( !$this->checkaddress($address) ) return FALSE;
		if ( !$this->checkname($name) ) return FALSE;
		if ( empty($name) ) { return $address; }
		else { $tmp=$name." <".$address.">"; return $tmp; }
	}

	public function addrecipient($newrecipient,$name='') {
		$tmp=$this->makenameplusaddress($newrecipient,$name);
		if ( !$tmp ) { $this->error_log(" To: error"); return FALSE; }
		$this->recipientlist[] = array( 'mail'=>$newrecipient, 'nameplusmail' => $tmp );
		return TRUE;
	}

	public function addbcc($bcc,$name='') {
		$tmp=$this->makenameplusaddress($bcc,$name);
		if ( !$tmp ) { $this->error_log(" Bcc: error"); return FALSE; }
		if ( !empty($this->hbcc)) $this->hbcc.= ",";
		$this->hbcc.= $tmp;
		return TRUE;
	}

	public function addcc($cc,$name='') {
		$tmp=$this->makenameplusaddress($cc,$name);
		if ( !$tmp ) { $this->error_log(" Cc: error\n"); return FALSE; }
		if (!empty($this->hcc)) $this->hcc.= ",";
		$this->hcc.= $tmp;
		return TRUE;
	}

	public function addsubject($subject) {
		if (!empty($subject)) $this->subject = $subject;
	}

	public function addfrom($from,$name='') {
		$tmp=$this->makenameplusaddress($from,$name);
		if ( !$tmp ) { $this->error_log(" From: error"); return FALSE; }
		$this->hfrom = $tmp;
		return TRUE;
	}

	public function addreturnpath($return) {
		$tmp=$this->makenameplusaddress($return,'');
		if ( !$tmp ) { $this->error_log("Return-Path: error"); return FALSE; }
		$this->returnpath = $return;
		return TRUE;
	}

	public function addreplyto($replyto) {
		$tmp=$this->makenameplusaddress($replyto,'');
		if ( !$tmp ) { $this->error_log(" Reply-To: error"); return FALSE; }
		$this->returnpath = $tmp;
		return TRUE;
	}

	public function adddeliveryreceipt($deliveryreceipt) {
		$tmp=$this->makenameplusaddress($deliveryreceipt,'');
		if ( !$tmp ) { $this->error_log(" Disposition-Notification-To: error"); return FALSE; }
		$this->deliveryreceipt = $tmp;
		return TRUE;
	}


	// les attachements
	public function addattachement($filename) {
		array_push($this->attachement, array('filename' => $filename));
	}

	// les attachements html
	public function addhtmlattachement($filename,$cid='',$contenttype='') {
		array_push ( $this -> htmlattachement ,
		array ( 'filename'=>$filename ,
                    'cid'=>$cid ,
                    'contenttype'=>$contenttype )
		);
	}

	public function writeattachement(&$attachement,$B) {
		$message = '';
		$inline = array();
		if ( !empty($attachement) ) {
			foreach($attachement as $AttmFile){
				$patharray = explode ("/", $AttmFile['filename']);
				$FileName = $patharray[count($patharray)-1];

				// If duplicate images are embedded, they may show up as attachments, so remove them.
				if (!in_array($AttmFile['filename'],$inline))
				{
					$message .= "\n--".$B."\n";

					if (!empty($AttmFile['cid'])) {
						$inline[] = $AttmFile['filename'];
						$message .= "Content-Type: {$AttmFile['contenttype']};\n name=\"".$FileName."\"\n";
						$message .= "Content-Transfer-Encoding: base64\n";
						$message .= "Content-ID: <{$AttmFile['cid']}>\n";
						$message .= "Content-Disposition: inline;\n filename=\"".$FileName."\"\n\n";
					} else {
						$message .= "Content-Type: application/octetstream;\n name=\"".$FileName."\"\n";
						$message .= "Content-Transfer-Encoding: base64\n";
						$message .= "Content-Disposition: attachment;\n filename=\"".$FileName."\"\n\n";
					}

					$fd=fopen ($AttmFile['filename'], "rb");
					$FileContent=fread($fd,filesize($AttmFile['filename']));
					fclose ($fd);

					$FileContent = chunk_split(base64_encode($FileContent));
					$message .= $FileContent;
					$message .= "\n\n";
				}
			}
			$message .= "\n--".$B."--\n";
		}
		return $message;
	}

	public function BodyLineWrap($Value) {
		return wordwrap($Value, 78, "\n ");
	}

	public function makebody() {
		$message='';
		if ( !$this->html && $this->text && !empty($this->attachement) ) {

			//Messages start with text/html alternatives in OB
			$message ="This is a multi-part message in MIME format.\n";
			$message.="\n--".$this->B1B."\n";

			$message.="Content-Type: text/plain; charset=\"iso-8859-1\"\n";
			$message.="Content-Transfer-Encoding: quoted-printable\n\n";
			// plaintext goes here
			$message.=$this->BodyLineWrap($this->text)."\n\n";

			$message.=$this->writeattachement($this->attachement,$this->B1B);

		}
		elseif ( !$this->html && $this->text && empty($this->attachement) ) {

			// plaintext goes here
			$message.=$this->BodyLineWrap($this->text)."\n\n";
		}
		elseif ( $this->html ) {

			//Messages start with text/html alternatives in OB
			$message ="This is a multi-part message in MIME format.\n";
			$message.="\n--".$this->B1B."\n";

			$message.="Content-Type: multipart/related;\n\t boundary=\"".$this->B2B."\"\n\n";
			//plaintext section
			$message.="\n--".$this->B2B."\n";

			$message.="Content-Type: multipart/alternative;\n\t boundary=\"".$this->B3B."\"\n\n";
			//plaintext section
			$message.="\n--".$this->B3B."\n";

			$message.="Content-Type: text/plain; charset=\"iso-8859-1\"\n";
			$message.="Content-Transfer-Encoding: quoted-printable\n\n";
			// plaintext goes here
			$message.=$this->BodyLineWrap($this->text)."\n\n";

			// html section
			$message.="\n--".$this->B3B."\n";
			$message.="Content-Type: text/html; charset=\"iso-8859-1\"\n";
			$message.="Content-Transfer-Encoding: base64\n\n";
			// html goes here
			$message.=chunk_split(base64_encode($this->html))."\n\n";

			// end of text
			$message.="\n--".$this->B3B."--\n";

			// attachments html
			if (empty($this->htmlattachement)) {
				$message.="\n--".$this->B2B."--\n";
			} else {
				$message.=$this->writeattachement( $this->htmlattachement,$this->B2B);
			}

			// attachments
			if (empty($this->attachement)) {
				$message.="\n--".$this->B1B."--\n";
			} else {
				$message.=$this->writeattachement($this->attachement,$this->B1B);
			}

		}

		$this->body = $message;

		return $message;

	}

	// Mail Headers Methods

	public function MakeHeaderField($Field,$Value) {
		return wordwrap($Field.": ".$Value, 78, "\n ")."\r\n";
	}

	public function AddField2Header($Field,$Value) {
		$this->headers .= $this->MakeHeaderField($Field,$Value);
	}

	public function makeheader() {

		$this->headers = '';

		if ( empty($this->recipientlist) ) { $this->error_log("destinataire manquant"); return FALSE; }
		else { // DOLCHANGE LDR Fix missing recipients in header
			$this->AddField2Header("To",$this->recipient);
		}

		if ( empty($this->subject) ) {
			$this->error_log("sujet manquant");
			return FALSE;
		} else {
			if ($this->set_mode!='php' ) {
				$this->AddField2Header("Subject", $this->subject);
			}
		}


		# Date: Mon, 03 Nov 2003 20:48:06 +0100
		$this->AddField2Header("Date", date ('r'));

		if ( !empty($this->Xsender) ) { $this->AddField2Header("X-Sender",$this->Xsender); }
		else { $this->AddField2Header("X-Sender",$this->hfrom); }

		if ( !empty($this->ErrorsTo) ) { $this->AddField2Header("Errors-To",$this->ErrorsTo); }
		else { $this->AddField2Header("Errors-To",$this->hfrom); }

		if ( !empty($this->XMailer) ) $this->AddField2Header("X-Mailer",$this->XMailer);

		if ( !empty($this->XPriority) ) $this->AddField2Header("X-Priority",$this->XPriority);

		if ( !empty($this->hfrom) ) $this->AddField2Header("From",$this->hfrom);

		if ( !empty($this->returnpath) ) { $this->AddField2Header("Return-Path",$this->returnpath); }
		else { $this->AddField2Header("Return-Path",$this->hfrom); }

		if ( !empty($this->replyto) ) $this->AddField2Header("Reply-To",$this->replyto);

		if ( !empty($this->deliveryreceipt) ) $this->AddField2Header("Disposition-Notification-To",$this->deliveryreceipt);

		$this->headers .="MIME-Version: 1.0\r\n";

		if ( !$this->html && $this->text && !empty($this->attachement) ) {
			$this->headers .= "Content-Type: multipart/mixed;\r\n\t boundary=\"".$this->B1B."\"\r\n";
		} elseif ( !$this->html && $this->text && empty($this->attachement) ) {
			$this->headers .="Content-Type: text/plain; charset=utf8; format=flowed\r\n";
			$this->headers .="Content-Transfer-Encoding: 8bit\r\n";
		} elseif ( $this->html ) {
			if ( !$this->text ) { $this->text="HTML only!"; }
			$this->headers .="Content-Type: multipart/mixed;\r\n\t boundary=\"".$this->B1B."\"\r\n";
		}

		if ( !empty($this->hcc) ) $this->AddField2Header("Cc",$this->hcc);
		if ( !empty($this->hbcc) ) $this->AddField2Header("Bcc",$this->hbcc);

		return $this->headers;
	}

	public function Envoi() {
		$this->makebody();
		$this->makeheader();
		switch($this->set_mode)	{
			case 'php' : $this->phpmail(); break;
			case 'socket': $this->socketmailloop(); break;
		}

		return TRUE;
	}

	// Mail send by PHPmail

	public function phpmail() {
		global $conf;
		// DOLCHANGE LDR Fix the To in header was not filled
		foreach ($this->recipientlist as $key => $to)
		{
			$this->recipient = ($this->recipient?$this->recipient.', ':'').$to['mail'];
		}
		foreach ($this->recipientlist as $key => $to)
		{
			$bounce = '';
			/*if ($conf->global->MAIN_MAIL_ALLOW_SENDMAIL_F)
			{
				// DOLCHANGE LDR
				// le return-path dans les header ne fonctionne pas avec tous les MTA
				// Le passage par -f est donc possible si la constante MAIN_MAIL_ALLOW_SENDMAIL_F est definie.
				// La variable definie pose des pb avec certains sendmail securisee (option -f refusee car dangereuse)
				$bounce = $this->returnpath != '' ? "-f {$this->returnpath}" : "";
			}*/
			// $this->recipient = $to['mail'];		DOLCHANGE LDR Fix the To in header was not filled
			if ( mail($to['mail'], $this->subject, $this->body, $this->makeheader() , $bounce ) ) {
				$this->error_log("envoi vers {$to['nameplusmail']} reussi");
			} else {
				$this->error_log("envoi vers {$to['nameplusmail']} echoue");
			}
		}
		return TRUE;
	}

	// Socket Function

	public function SocketStart() {
		if (!$this->connect = fsockopen (ini_get("SMTP"), ini_get("smtp_port"), $errno, $errstr, 30))  {
			$this->error_log("Could not talk to the sendmail server!"); return FALSE;
		};
		return fgets($this->connect, 1024);
	}

	public function SocketStop() {
		fclose($this->connect);
		return TRUE;
	}

	public function SocketSend($in,$wait='') {
		fputs($this->connect, $in, strlen($in));
		//echo $in;
		$this->error_log($in);	// DOLCHANGE LDR Add debug
		//flush();
		if(empty($wait)) {
			$rcv = fgets($this->connect, 1024);
			$this->error_log('('.$rcv.')');	// DOLCHANGE LDR Add debug
			return $rcv;
		}
		return TRUE;
	}

	// Mail Socket

	public function socketmailstart() {

		$this->SocketStart();
		if (!isset($_SERVER['SERVER_NAME'])  || empty($_SERVER['SERVER_NAME'])) { $serv = 'unknown'; }
		else { $serv = $_SERVER['SERVER_NAME']; }
		// DOLCHANGE LDR
		$serv = ini_get('SMTP');
		$this->SocketSend("HELO $serv\r\n",'250');
	}

	public function socketmailsend($to) {

		// $this->recipient = $to;	// DOLCHANGE LDR Must not reset this property
		$this->error_log("Socket vers $to");

		// DOLCHANGE LDR: From has to be the raw email address, strip the "name" off
		$fromarray=explode(' ',$this->hfrom);
		$from=(empty($fromarray[1])?$fromarray[0]:$fromarray[1]);
		$this->SocketSend( "MAIL FROM: ".$from."\r\n", '250');
		$this->SocketSend( "RCPT TO: <".$to.">\r\n", '250');
		$this->SocketSend( "DATA\r\n", '354');
		$this->SocketSend( $this->CleanMailDataString($this->headers)."\r\n".$this->CleanMailDataString($this->body)."\r\n", '250');	// DOLCHANGE LDR Must wait return 250
		$this->SocketSend( ".\r\n" );
		$this->SocketSend( "RSET\r\n" );

		$this->error_log("Fin de l'envoi vers $to");

		return TRUE;
	}

	public function socketmailstop() {
		$this->SocketSend("QUIT\r\n");
		$this->SocketStop();
		return TRUE;
	}

	public function socketmailloop() {
		$this->socketmailstart();
		// DOLCHANGE LDR Fix the To in header was not filled
		foreach ($this->recipientlist as $key => $to)
		{
			$this->recipient = ($this->recipient?$this->recipient.', ':'').$to['mail'];
		}
		foreach ($this->recipientlist as $key => $to)
		{
			$this->makeheader();
			$this->socketmailsend($to['mail']);
		}
		$this->socketmailstop();
	}

	// Misc.

	public function error_log($msg='') {
		if(!empty($msg)) {
			$this->error_log .= $msg;
			return TRUE;
		}
		return $this->error_log;
	}

	public function CleanMailDataString($data) {
		$data = preg_replace("/([^\r]{1})\n/", "\\1\r\n", $data);
		$data = preg_replace("/\n\n/", "\n\r\n", $data);
		$data = preg_replace("/\n\./", "\n..", $data);
		return $data;
	}
}