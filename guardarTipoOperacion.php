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

  $sentencia = $base_de_datos->prepare("INSERT INTO tipo_operacion(codigo, codigo_totem, descripcion) VALUES (?, ?, ?);");
  $resultado = $sentencia->execute([strtoupper($params->codigo), strtoupper($params->codigo_totem), strtoupper($params->descripcion)]);

  $response = new Result();

  if ($resultado == true) {
    $response->mensaje = 'Tipo operacion guardado correctamente.';
  } else {
    $response->mensaje = 'Ocurrió un error al guardar el Tipo operacion.';
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