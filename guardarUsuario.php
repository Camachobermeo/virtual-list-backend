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
  $estado = $params->estado == false ? 0 : 1;
  if ($params->esEdicion) {
    $sentencia = $base_de_datos->prepare("UPDATE usuario 
                                          SET (codigo, rut, nombre, apellidos, telefono, clave, estado, codigo_tienda, ventanilla, tipo_usuario) = 
                                          (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) WHERE codigo = '$params->codigo'");
    $resultado = $sentencia->execute([
      strtoupper($params->codigo), strtoupper($params->rut), strtoupper($params->nombre),
      strtoupper($params->apellidos), strtoupper($params->telefono), $params->clave,  strtoupper($estado), $params->codigo_tienda,
      strtoupper($params->ventanilla), strtoupper($params->tipo_usuario)
    ]);
  } else {
    $sentencia = $base_de_datos->prepare("INSERT INTO usuario(codigo, rut, nombre, apellidos, telefono, clave, estado, codigo_tienda, ventanilla, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $resultado = $sentencia->execute([
      strtoupper($params->codigo), strtoupper($params->rut), strtoupper($params->nombre),
      strtoupper($params->apellidos), strtoupper($params->telefono), $params->clave,  strtoupper($estado), $params->codigo_tienda,
      strtoupper($params->ventanilla), strtoupper($params->tipo_usuario)
    ]);
  }


  $response = new Result();

  if ($resultado == true) {
    $response->mensaje = 'Usuario guardado correctamente.';
  } else {
    $response->mensaje = 'Ocurrió un error al guardar el usuario.';
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
