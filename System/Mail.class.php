<?php
class Mail
{
	function SendMail($toAddress,$toName,$subject,$messageBody,$bcc=NULL,$mailList=FALSE)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		if($mailList)
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/MailService2.php");
		else
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/MailService.php");

		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/PHPMailer/class.phpmailer.php");
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/PHPMailer/class.pop3.php");
		
		if($MailServiceAuthPOP3)
		{
			$pop = new POP3();
			$pop->Authorise($MailServicePOP3Addr, $MailServicePOP3Port, 30, $MailServiceSMTPUser, $MailServiceSMTPPass, $MailServicePOPDebug);
		}
		
		$mail = new PHPMailer();
		
		if($MailServiceMailerLang != "en")
			$mail->SetLanguage($MailServiceMailerLang, $_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/PHPMailer/language/");
			
		$mail->IsSMTP();
		$mail->SMTPDebug= $MailServiceSMTPDebug;
		$mail->Port		= $MailServiceSMTPPort;
		$mail->SMTPSecure= $MailServiceEncrypt;
		$mail->Host		= $MailServiceSMTPAddr;
		$mail->SMTPAuth = $MailServiceAuthSMTP;
		$mail->Username = $MailServiceSMTPUser;
		$mail->Password = $MailServiceSMTPPass;
		$mail->From		= $MailServiceFromMail;
		$mail->FromName = $MailServiceFromName;
		$mail->AddAddress($toAddress,$toName);
		
		if($bcc != NULL)
			if(is_array($bcc))
				foreach($bcc as $key=>$value)
					$mail->AddBCC($value);
		
		$mail->WordWrap = 50;
		$mail->CharSet = $MailServiceMsgCharset;
		$mail->IsHTML(true);
		
		$mail->Subject = $subject;
		$mail->Body    = $messageBody;
		
		if($mail->Send())
			return true;
		else
			return $mail->ErrorInfo;
	}
}
?>