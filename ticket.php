<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


$json = file_get_contents('php://input');

$params = json_decode($json);

try {

  include_once "utiles/base_de_datos.php";

  $fecha = date("Y-m-d H:i:s");
  $sentencia = $base_de_datos->prepare("INSERT INTO ticket(codigo_tipo_operacion, email, telefono, recordatorio, fecha_sacado) VALUES (?, ?, ?, ?, ?);");
  $resultado = $sentencia->execute([$params->codigo_tipo_operacion, $params->email, $params->telefono, $params->recordatorio, $fecha]);
  if ($resultado == true) {
    $response->mensaje = 'Usted reservó un ticket para ser atendido. Revise su correo electrónico.';
  } else {
    $response->mensaje = 'Ocurrió un error al reservar un ticket.';
  }

  class Result
  {
  }

  $response = new Result();
  $response->resultado = $resultado;


  header('Content-Type: application/json');
  echo json_encode($response);
} catch (Exception $th) {
  $response = new Result();
  $response->resultado = [];
  $response->mensaje = "error desconocido";
}
