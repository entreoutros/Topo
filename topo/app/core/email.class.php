<?php

require_once(lib_path.'PHPMailer_5.2.1/class.phpmailer.php');

class Email{

	/**
	 * sendMsg
	 *
	 * Envia um e-mail utilizando a bibliteca PHPMailer.
	 *
	 * @version 1.0
	 * @param 	string 	$to 					E-mail do destinatário.
	 * @param 	string 	$subject 				Assunto do e-mail.
	 * @param 	string 	$msg 					Mensagem do e-mail (html).
	 * @param 	string 	$configs 				Array com as configurações do envio.
	 *											Índice 'SMTPAuth' - Informa se a conexão com o SMTP será autenticada.
	 *											Índice 'SMTPSecure' - Define o padrão de segurança (SSL, TLS, STARTTLS).
	 *											Índice 'Host' - Host smtp para envio do e-mail.
	 *											Índice 'SMTPAuth' - Informa se a conexão com o SMTP será autenticada.
	 *											Índice 'Port' - Porta do SMTP para envio do e-mail.
	 *											Índice 'Username' - Usuário para autenticação do SMTP.
	 *											Índice 'Password' - Senha para autenticação do SMTP.
	 *											Índice 'AddReplyTo' - E-mail e nome para resposta do e-mail.
	 *													array( 0 => e-mail, 1 => nome)
	 *											Índice 'SetFrom' - E-mail e nome do remetente do e-mail.
	 *													array( 0 => e-mail, 1 => nome)
	 * @param 	string 	$msg 					Mensagem do e-mail (html).
	 * @param 	string 	$msg 					Mensagem do e-mail (html).
	 * @param 	string 	$msg 					Mensagem do e-mail (html).
	 * @return 	boolean / mensagens de erros 	Retorna true caso o e-mail tenha sido enviado ou as mensagens de erro caso não tenha sido enviado.
	 */
	public static function sendMsg($to,$subject,$msg,$configs = '',$debug = 1){
	
		$configs = ($configs == '' || $configs == null)?Config::$email['default']: $configs;
		$configs = (is_string($configs))?Config::$email[$configs]: $configs;
		
		$mail = new PHPMailer(true); // True para a função enviar exceções de erros

		$mail->IsSMTP(); // Definindo a classe para utilizar SMTP.
		
		try {
		  
		  $mail->SMTPDebug  = $debug;                // Ativar informações de debug
		  $mail->SMTPAuth   = $configs['SMTPAuth'];  // Informa se a conexão com o SMTP será autenticada.
		  $mail->SMTPSecure = $configs['SMTPSecure'];// Define o padrão de segurança (SSL, TLS, STARTTLS).
		  $mail->Host       = $configs['Host'];		 // Host smtp para envio do e-mail.
		  $mail->Port       = $configs['Port'];;     // Porta do SMTP para envio do e-mail.
		  $mail->Username   = $configs['Username'];  // Usuário para autenticação do SMTP.
		  $mail->Password   = $configs['Password'];  // Senha para autenticação do SMTP.
		  $mail->AddAddress($to, "");
		  $mail->AddReplyTo($configs['AddReplyTo'][0], $configs['AddReplyTo'][1]);
		  $mail->SetFrom($configs['SetFrom'][0], $configs['SetFrom'][1]);
		  $mail->Subject = $subject;
		  $mail->AltBody = 'Para ver essa mensagem utilize um programa compatível com e-mails em formato HTML!'; // optional - MsgHTML will create an alternate automatically
		  $mail->MsgHTML($msg);
		  $mail->Send();
		  return true;
		} catch (phpmailerException $e) {
		  return $e->errorMessage(); // Mensagens de erro das exceções geradas pelo phpmailer.
		} catch (Exception $e) {
		  return $e->getMessage(); // Mensagens de exceções geradas durante a execução da função.
		}
	}
}

?>