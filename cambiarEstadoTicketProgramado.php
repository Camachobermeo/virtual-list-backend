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
  $resultado = false;
  $response = new Result();
  if ($params->estado) {
    date_default_timezone_set($zonaHoraria);
    $fecha = date("Y-m-d H:i:s");
    if ($params->estado == 'EN ATENCION') {
      $query = "SELECT * FROM ticket_programado where secuencial = '" . $params->secuencial . "'";
      $sentencia = $base_de_datos->query($query);
      $objeto = $sentencia->fetchObject();
      if ($objeto && $objeto->estado && $objeto->estado == 'EN ATENCION') {
        $response->objeto = $objeto;
      } else {
        $sentencia = $base_de_datos->prepare("UPDATE ticket_programado
        SET (estado, usuario, inicio_atencion) = (?, ?, ?) WHERE secuencial = '$params->secuencial'");
        $resultado = $sentencia->execute([
          strtoupper($params->estado), strtoupper($params->usuario), $fecha
        ]);
      }
    }
    if ($params->estado == 'ATENDIDO') {
      $sentencia = $base_de_datos->prepare("UPDATE ticket_programado
      SET (estado, usuario, fin_atencion) = (?, ?, ?) WHERE secuencial = '$params->secuencial'");
      $resultado = $sentencia->execute([
        strtoupper($params->estado), strtoupper($params->usuario), $fecha
      ]);
    }
  } else {
    $sentencia = $base_de_datos->prepare("UPDATE ticket_programado
    SET (estado, usuario) = (?, ?) WHERE secuencial = '$params->secuencial'");
    $resultado = $sentencia->execute([
      null, null
    ]);
  }

  if ($resultado == true) {
    $response->mensaje = 'El Ticket Programado cambió de estado: ' . $params->estado;
  } else {
    $response->mensaje = 'Ocurrió un error al modificar estado.';
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
