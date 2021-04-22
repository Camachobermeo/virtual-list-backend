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
  include_once "utiles/constantes.php";
  date_default_timezone_set($zonaHoraria);
  $fecha = date("Y-m-d H:i:s");
  if ($params->estado != null) {
    $query = "SELECT * FROM ticket WHERE codigo_fila = '$params->codigo' AND DATE(fecha_sacado) = '" . $fecha . "' AND (estado = '$params->estado' OR estado = 'ATENDIDO') order by fecha_sacado  DESC LIMIT 1;";
  } else {
    $query = "SELECT * FROM ticket WHERE codigo_fila = '$params->codigo' AND DATE(fecha_sacado) = '" . $fecha . "' order by fecha_sacado  DESC LIMIT 1;";
  }
  $sentencia = $base_de_datos->query($query);
  $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);



  $response = new Result();
  $response->resultado = $resultado ? $resultado[0] : null;
  $response->estado = 'T';
  $response->mensaje = 'Datos Listados Correctamente';

  header('Content-Type: application/json');
  echo json_encode($response);
} catch (Exception $th) {
  $response = new Result();
  $response->resultado = [];
  $response->mensaje =  $th->getMessage();
  header('Content-Type: application/json');
  echo json_encode($response);
}
