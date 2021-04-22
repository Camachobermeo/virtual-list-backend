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

  $query = "DELETE FROM ticket_programado WHERE secuencial = $params->secuencial;";

  $sentencia = $base_de_datos->query($query);
  $resultado = $sentencia->execute();

  $response = new Result();

  if ($resultado == true) {
    $response->mensaje = 'Datos eliminados correctamente.';
  } else {
    $response->mensaje = 'Ocurrió un error al eliminar.';
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
