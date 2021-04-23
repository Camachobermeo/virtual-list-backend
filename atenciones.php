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
    $fecha = date("Y-m-d");
    $query = "SELECT ticket.* FROM ticket 
    where estado = 'EN ATENCION'
    and DATE(fecha_sacado) = '" . $fecha . "'";
    $query = $query . " order by fecha_sacado limit 4";

    $response = new Result();
    $sentencia = $base_de_datos->query($query);
    $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);

    $response->resultado = $resultado;
    $response->programados = $resultado;
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
