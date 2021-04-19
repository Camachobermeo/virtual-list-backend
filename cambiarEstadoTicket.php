<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
$json = file_get_contents('php://input');
$params = json_decode($json);

class Result
{
}

try {

  include_once "utiles/base_de_datos.php";
  if ($params->estado) {
    $sentencia = $base_de_datos->prepare("UPDATE  ticket
    SET (estado, usuario) = (?, ?) WHERE secuencial = '$params->secuencial'");

    $resultado = $sentencia->execute([
      strtoupper($params->estado), strtoupper($params->usuario)
    ]);
  } else {
    $sentencia = $base_de_datos->prepare("UPDATE  ticket
    SET (estado, usuario) = (?, ?) WHERE secuencial = '$params->secuencial'");

    $resultado = $sentencia->execute([
      null, null
    ]);
  }

  $response = new Result();
  if ($resultado == true) {
    $response->mensaje = 'El Ticket cambió de estado: ' . $params->estado;
  } else {
    $response->mensaje = 'Ocurrió un error al modificar estado.';
  }
  $response->resultado = $resultado;
  header('Content-Type: application/json');
  echo json_encode($response);
} catch (Exception $th) {
  $response = new Result();
  $response->mensaje = $th->getMessage();
  header('Content-Type: application/json');
  echo json_encode($response);
}
