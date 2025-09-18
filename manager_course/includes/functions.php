<?php
if(!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}

function layout($layoutName, $data = []){
    if(file_exists(_PATH_URL_TEMPLATES . '/layouts/'.$layoutName. '.php')){
        require_once _PATH_URL_TEMPLATES . '/layouts/'.$layoutName. '.php';
    }
}

// Hàm gửi mail (php mailer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function senMail($emailTo, $subject, $body){

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'redcream2004@gmail.com';                     //SMTP username
    $mail->Password   = 'fycgpbkdsivhlvob';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('redcream2004@gmail.com', 'Ahryxx Course');
    $mail->addAddress($emailTo);     //Add a recipient

    //Content
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = '$subject';
    $mail->Body    = '$body';

    $mail->SMTPOptions = array(
    'ssl' => [
        'verify_peer' => true,
        'verify_depth' => 3,
        'allow_self_signed' => true,
    ],
);

    return $mail->send();
} catch (Exception $e) {
    echo "Gửi thất bại: {$mail->ErrorInfo}";
}
}