<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


$json = file_get_contents('php://input');

$params = json_decode($json);

try {

  include_once "utiles/base_de_datos.php";

  $query = "SELECT rut, razon_social, 'data:image/png;base64,' || encode(logo, 'escape') AS logo, tema, obligar_persona, obligar_correo, obligar_celular, cabecera, menu FROM empresa WHERE rut = '$params->empresa';";

  $sentencia = $base_de_datos->query($query);
  $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);

  class Result
  {
  }

  $response = new Result();
  $response->resultado = $resultado[0];
  $response->mensaje = 'Datos Listados Correctamente';

  header('Content-Type: application/json');
  echo json_encode($response);
} catch (Exception $th) {
  $response = new Result();
  $response->resultado = [];
  $response->mensaje = "error desconocido";
  header('Content-Type: application/json');
  echo json_encode($response);
}
