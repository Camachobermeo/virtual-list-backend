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
    $response = new Result();
    date_default_timezone_set($zonaHoraria);
    $fecha = date("Y-m-d H:i:s");
    //TICKET NORMAL
    $query = "SELECT ticket.* FROM ticket 
        inner join fila fila on fila.codigo = codigo_fila 
        inner join sucursal su on su.codigo = fila.codigo_sucursal 
        where su.codigo = '" . $params->sucursal . "' and 
        (estado is null or (estado = 'EN ATENCION' and usuario = '" . $params->usuario . "')) 
        and DATE(fecha_sacado) = '" . $fecha . "'";
    if ($params->fila) {
        $query = $query . " and fila.codigo = '" . $params->fila . "'";
    }
    $query = $query . " order by fecha_sacado limit 1";
    $sentencia = $base_de_datos->query($query);
    $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
    $programado = false;

    $response->resultado = $resultado;
    $response->programado = $programado;
    $response->mensaje = 'Datos Listados Correctamente';
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (Exception $th) {
    $response = new Result();
    $response->mensaje = $th->getMessage();
    header('Content-Type: application/json');
    echo json_encode($response);
}
