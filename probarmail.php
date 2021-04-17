<?php
use PHPMailer\PHPMailer\PHPMailer;  //estas son las funciones
use PHPMailer\PHPMailer\Exception;   

require 'PHPMailer/Exception.php';   //aqui las librerias
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$mail = new PHPMailer(true);    //se crea el objeto

try {
    //Server settings
    $mail->SMTPDebug = 2;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'kottoland@gmail.com';                     //SMTP username
    $mail->Password   = 'Megustaelvin0';                               //SMTP password
    $mail->SMTPSecure = 'tls';         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       =  587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('kottoland@gmail.com', 'Checkseguro');
    $mail->addAddress('daniel@almodobar.cl');     //destinatario...


    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'este asunto es muy importante';
    $mail->Body    = 'Este es el cuerpo del correo en HTML <b>en negrita!</b>';
    $mail->AltBody = 'y este es el cuerpo del correo en texto plano para clientes que no aceptan html';

    $mail->send();
    echo 'Mensaje enviado correctamente...';
} catch (Exception $e) {
    echo 'Mensaje no pudo ser enviado...', $mail->ErrorInfo;
}
