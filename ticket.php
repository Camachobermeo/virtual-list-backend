<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


$json = file_get_contents('php://input');

$params = json_decode($json);

$PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

//html PNG location prefix
$PNG_WEB_DIR = 'temp/';

class Result
{
}

try {

  include_once "utiles/base_de_datos.php";
  include_once "utiles/phpqrcode.php";

  if (!file_exists($PNG_TEMP_DIR))
    mkdir($PNG_TEMP_DIR);

  $filename = $PNG_TEMP_DIR . 'test.png';

  // user data
  $filename = $PNG_TEMP_DIR . 'test' . md5('MARIO' . '|H|10') . '.png';
  QRcode::png('MARIO', $filename, 'H', '10', 2);

  // echo '<img src="' . $PNG_WEB_DIR . basename($filename) . '" />';

  $fecha = date("Y-m-d H:i:s");
  $recordar = $params->recordatorio == false ? 0 : 1;

  $sentencia = $base_de_datos->prepare("INSERT INTO ticket(codigo_tipo_operacion, email, telefono, recordatorio, fecha_sacado) VALUES (?, ?, ?, ?, ?);");
  $resultado = $sentencia->execute([$params->codigo_tipo_operacion, $params->email, $params->telefono, $recordar, $fecha]);

  $response = new Result();

  if ($resultado == true) {
    $response->mensaje = 'Usted reservó un ticket para ser atendido. Revise su correo electrónico.';
  } else {
    $response->mensaje = 'Ocurrió un error al reservar un ticket.';
  }
  $response->resultado = $resultado;

  $num = md5(time());

  //MAIL BODY
  $body = "
    <html>
    <head>
    <title>Monitoreo</title>
    </head>
    <body style='background:#EEE; padding:30px;'>
    <h2 style='color:#767676;'>Monitoreo Grupo Bedoya</h2>";

  $body .= "
    <strong style='color:#0090C6;'>Monitor: </strong>
    <span style='color:#767676;'>Monitor</span>";

  $body .= "
    <strong style='color:#0090C6;'>Email: </strong>
    <span style='color:#767676;'>Email</span>";

  $body .= "
    <strong style='color:#0090C6;'>Nick: </strong>
    <span style='color:#767676;'>Nick</span>";

  $body .= "
    <strong style='color:#0090C6;'>Pagina Monitoreda: </strong>
    <span style='color:#767676;'>Pagina</span></br>";

  $body .= "</body></html>";

  $_name = $filename;
  // $_size = $filename["size"];

  if (strcmp($_name, "")) //FILES EXISTS
  {
    $fp = fopen($filename, "rb");
    $file = fread($fp, filesize($filename));
    $file = chunk_split(base64_encode($file));

    // MULTI-HEADERS Content-Type: multipart/mixed and Boundary is mandatory.
    $headers = "From: Monitoreo Grupo Bedoya <monitoreogrupobedoya@hotmail.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; ";
    $headers .= "boundary=" . $num . "\r\n";
    $headers .= "--" . $num . "\n";

    // HTML HEADERS 
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";
    $headers .= "" . $body . "\n";
    $headers .= "--" . $num . "\n";

    // FILES HEADERS 
    $headers .= "Content-Type:application/octet-stream ";
    $headers .= "name=\"" . $_name . "\"r\n";
    $headers .= "Content-Transfer-Encoding: base64\r\n";
    $headers .= "Content-Disposition: attachment; ";
    $headers .= "filename=\"" . $_name . "\"\r\n\n";
    $headers .= "" . $file . "\r\n";
    $headers .= "--" . $num . "--";
  } else { //FILES NO EXISTS

    // HTML HEADERS
    $headers = "From: Grupo Bedoya \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";
  }
  $to = $params->email;

  // SEND MAIL
  mail($to, "Monitoreo grupo bedoya", $body, $headers);

  echo "<div class='ok'>
    <strong>El formulario se ha enviado correctamente.</strong></div>";


  $cabeceras = 'MIME-Version: 1.0' . "\r\n";
  $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";

  $subject = "Ticket Generado";
  $message = "<h1>Hola: </h1>Presentar el siguiente ticket al ingresar.";

  $response->correo = $to;

  $enviado = mail($to, $subject, $message, $cabeceras);

  header('Content-Type: application/json');
  echo json_encode($response);
} catch (Exception $th) {
  $response = new Result();
  $response->mensaje = $th->getMessage();
  $response->hola = $params->recordatorio;

  header('Content-Type: application/json');
  echo json_encode($response);
}
