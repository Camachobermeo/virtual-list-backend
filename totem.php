<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


$json = file_get_contents('php://input');

$params = json_decode($json);

try {

  include_once "utiles/base_de_datos.php";

  $query = "SELECT * FROM totem WHERE codigo_tienda = '$params->tienda';";
  $sentencia = $base_de_datos->query($query);
  $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);

  class Result
  {
  }

  $response = new Result();
  $response->resultado = $resultado;
  $response->mensaje = 'datos listados';

  header('Content-Type: application/json');
  echo json_encode($response);
} catch (Exception $th) {
  $response = new Result();
  $response->resultado = [];
  $response->mensaje = "error desconocido";
}
