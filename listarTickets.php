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
    $query = "SELECT * FROM ticket where DATE(fecha_sacado) = '" . $params->fecha_sacado . "'";
    $sentencia = $base_de_datos->query($query);
    $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);

    $response = new Result();
    $response->resultado = $resultado;
    $response->mensaje = 'Datos Listados Correctamente';

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (Exception $th) {
    $response = new Result();
    $response->resultado = [];
    $response->mensaje = $th->getMessage();

    header('Content-Type: application/json');
    echo json_encode($response);
}
