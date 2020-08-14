<?php
require_once("includes/config.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
	function mailOtp($email,$otp) {
		$messageBody = "One Time Password for admin authentication to upload video is:<br/><br/>" . $otp;
		$mail = new PHPMailer(true);
		try {
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->SMTPAuth = TRUE;
		$mail->SMTPSecure = 'tls';
		$mail->Port     = 587;
		$mail->Username = '2senthil2018@gmail.com';
		$mail->Password = 'muthu2006';
		$mail->Host     = "smtp.gmail.com";
		$mail->SetFrom('2senthil2018@gmail.com', 'E-Classroom');
		$mail->AddAddress($email);
		$mail->Subject = "OTP to Upload Video";
		$mail->Body=$messageBody;
		$mail->isHTML(true);		
		$mailStatus = $mail->send();
	} catch (Exception $e) {
		$mailStatus="Mailer Error: {$mail->ErrorInfo}";
	}
	return $mailStatus;
}
?>