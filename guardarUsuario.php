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
                                          SET (nombre, apellidos, telefono, clave, estado, codigo_sucursal, ventanilla, tipo_usuario) = 
                                          (?, ?, ?, ?, ?, ?, ?, ?) WHERE username = '$params->username'");
    $resultado = $sentencia->execute([
      strtoupper($params->nombre),
      strtoupper($params->apellidos), strtoupper($params->telefono), $params->clave,  strtoupper($estado), $params->codigo_sucursal,
      strtoupper($params->ventanilla), strtoupper($params->tipo_usuario)
    ]);
  } else {
    $sentencia = $base_de_datos->prepare("INSERT INTO usuario(username, rut_empresa, nombre, apellidos, telefono, clave, estado, codigo_sucursal, ventanilla, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $resultado = $sentencia->execute([
      strtoupper($params->username), strtoupper($params->rut_empresa), strtoupper($params->nombre),
      strtoupper($params->apellidos), strtoupper($params->telefono), $params->clave,  strtoupper($estado), $params->codigo_sucursal,
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
