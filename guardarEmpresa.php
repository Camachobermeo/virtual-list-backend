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

  $persona = $params->obligar_persona == false ? 'no' : 'yes';
  $correo = $params->obligar_correo == false ? 'no' : 'yes';
  $celular = $params->obligar_celular == false ? 'no' : 'yes';
  $rut = $params->obligar_rut == false ? 'no' : 'yes';
  $nombre = $params->obligar_nombre == false ? 'no' : 'yes';

  $sentencia = $base_de_datos->prepare("UPDATE empresa SET (obligar_persona, obligar_correo, obligar_celular, obligar_rut, obligar_nombre, logo, cabecera, menu) = (?, ?, ?, ?, ?, ?) WHERE rut = '$params->empresa'");
  $resultado = $sentencia->execute([$persona, $correo, $celular, $rut, $nombre, $params->logo, $params->cabecera, $params->menu]);

  $response = new Result();

  if ($resultado == true) {
    $response->mensaje = 'Datos guardados correctamente.';
  } else {
    $response->mensaje = 'Ocurrió un error al modificar la Empresa.';
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
