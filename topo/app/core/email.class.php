<?php

require_once(lib_path.'PHPMailer_5.2.1/class.phpmailer.php');

class Email{

	public static function sendMsg($to,$subject,$msg,$configs = '',$debug = 1){
	
		$configs = ($configs == '' || $configs == null)?Config::$email['default']: $configs;
		$configs = (is_string($configs))?Config::$email[$configs]: $configs;
		
		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

		$mail->IsSMTP(); // telling the class to use SMTP
		
		try {
		  
 		  $mail->SMTPDebug  = $debug;                // enables SMTP debug information (for testing)
		  $mail->SMTPAuth   = $configs['SMTPAuth'];  // enable SMTP authentication
		  $mail->SMTPSecure = $configs['SMTPSecure'];// sets the prefix to the servier
		  $mail->Host       = $configs['Host'];		 // sets GMAIL as the SMTP server
		  $mail->Port       = $configs['Port'];;     // set the SMTP port for the GMAIL server
		  $mail->Username   = $configs['Username'];  // GMAIL username
		  $mail->Password   = $configs['Password'];  // GMAIL password
		  $mail->AddAddress($to, "");
		  $mail->AddReplyTo($configs['AddReplyTo'][0], $configs['AddReplyTo'][1]);
		  $mail->SetFrom($configs['SetFrom'][0], $configs['SetFrom'][1]);
		  $mail->Subject = $subject;
		  $mail->AltBody = 'Para ver essa mensagem utilize um programa compatível com e-mails em formato HTML!'; // optional - MsgHTML will create an alternate automatically
		  $mail->MsgHTML($msg);
		  $mail->Send();
		  return true;
		} catch (phpmailerException $e) {
		  return $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  return $e->getMessage(); //Boring error messages from anything else!
		}
	}
}

?>