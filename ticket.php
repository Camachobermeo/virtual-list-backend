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
  // echo '<img src="' . $PNG_WEB_DIR . basename($filename) . '" />';

  $fecha = date("Y-m-d H:i:s");
  $recordar = $params->recordatorio == false ? 0 : 1;
  $sentencia = $base_de_datos->prepare("INSERT INTO ticket(codigo_tipo_operacion, email, telefono, recordatorio, fecha_sacado, rut, nombres) VALUES (?, ?, ?, ?, ?, ?, ?);");
  $resultado = $sentencia->execute([$params->codigo_tipo_operacion, $params->email, $params->telefono, $recordar, $fecha, $params->rut, $params->nombres]);

  $response = new Result();
  if ($resultado == true) {
    $response->mensaje = 'Usted reservó un ticket para ser atendido. Revise su correo electrónico.';

    if (!file_exists($PNG_TEMP_DIR))
      mkdir($PNG_TEMP_DIR);

    $filename = $PNG_TEMP_DIR . 'test.png';
    // user data
    $filename = $PNG_TEMP_DIR . 'test' . md5('MARIO' . '|H|10') . '.png';
    QRcode::png('MARIO', $filename, 'H', '10', 2);

    $cabeceras = 'MIME-Version: 1.0' . "\r\n";
    $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";

    $subject = "Ticket Generado " . $params->codigo_tipo_operacion;
    $message = "<h3>Hola: " . $params->nombres . " <br> </h3> Usted reservó el siguiente ticket: 
              <br> 
              <img src=' . $PNG_WEB_DIR . basename($filename) . ' />
              <br> 
              Presentar el siguiente ticket al ingresar.";

    $enviado = mail($params->email, $subject, $message, $cabeceras);
  } else {
    $response->mensaje = 'Ocurrió un error al reservar un ticket.';
  }
  $response->resultado = $resultado;

  header('Content-Type: application/json');
  echo json_encode($response);
} catch (Exception $th) {
  $response = new Result();
  $response->mensaje = $th->getMessage();
  $response->hola = $params->recordatorio;

  header('Content-Type: application/json');
  echo json_encode($response);
}
