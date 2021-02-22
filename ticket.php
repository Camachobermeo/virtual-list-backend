<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


$json = file_get_contents('php://input');

$params = json_decode($json);

$PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

//html PNG location prefix
$PNG_WEB_DIR = 'temp/';

include "utiles/phpqrcode.php";

class Result
{
}

try {

  include_once "utiles/base_de_datos.php";

  if (!file_exists($PNG_TEMP_DIR))
    mkdir($PNG_TEMP_DIR);

  $filename = $PNG_TEMP_DIR . 'test.png';

  // user data
  $filename = $PNG_TEMP_DIR . 'test' . md5('MARIO' . '|H|10') . '.png';
  // QRcode::png('MARIO', $filename, 'H', '10', 2);

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


  $to = $params->email;
  $subject = "Ticket Generado";
  $message = "Presentar el siguiente ticket al ingresar.";

  $response->correo = $to;

  mail('joelbermeo452@gmail.com', $subject, $message);

  header('Content-Type: application/json');
  echo json_encode($response);
} catch (Exception $th) {
  $response = new Result();
  $response->mensaje = $th->getMessage();
  $response->hola = $params->recordatorio;

  header('Content-Type: application/json');
  echo json_encode($response);
}
