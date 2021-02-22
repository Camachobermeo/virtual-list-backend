<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


$json = file_get_contents('php://input');

$params = json_decode($json);

try {

    include_once "utiles/base_de_datos.php";

    $query = "select tipo.codigo, tipo.codigo_totem, tipo.descripcion from tipo_operacion AS tipo 
  INNER JOIN totem t on t.codigo = tipo.codigo_totem
  INNER JOIN tienda ti on ti.codigo = t.codigo_tienda
  where ti.codigo = '$params->tienda'";
    $sentencia = $base_de_datos->query($query);
    $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);

    class Result
    {
    }

    $response = new Result();
    $response->resultado = $resultado;
    $response->mensaje = 'Datos Listados Correctamente';

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (Exception $th) {
    $response = new Result();
    $response->resultado = [];
    $response->mensaje = "error desconocido";
}