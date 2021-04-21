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

  if ($params->esEdicion) {
    $sentencia = $base_de_datos->prepare("UPDATE fila 
                                          SET (codigo, codigo_sucursal, descripcion, tiempo_estimado_minutos, costo_estimado) = 
                                          (upper('$params->codigo'), '$params->codigo_sucursal', upper('$params->descripcion'), $params->tiempo_estimado_minutos, $params->costo_estimado) WHERE codigo = '$params->codigo'");
$resultado = $sentencia->execute();  
}  else {
  $sentencia = $base_de_datos->prepare("INSERT INTO fila(codigo, codigo_sucursal, descripcion, tiempo_estimado_minutos, costo_estimado) VALUES (?, ?, ?, ?, ?);");
  $resultado = $sentencia->execute([strtoupper($params->codigo), strtoupper($params->codigo_sucursal), strtoupper($params->descripcion), $params->tiempo_estimado_minutos, $params->costo_estimado]);
}



  $response = new Result();

  if ($resultado == true) {
    $response->mensaje = 'Fila guardada correctamente.';
  } else {
    $response->mensaje = 'Ocurrió un error al guardar la Fila.';
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