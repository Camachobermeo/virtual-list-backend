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

  $filas = $params->filas;

  if ($params->esEdicion) {
    $sentencia = $base_de_datos->prepare("UPDATE  totem
                                        SET (codigo, codigo_sucursal, ubicacion) =
                                        ('$params->codigo', upper('$params->codigo_sucursal'), upper('$params->ubicacion')) WHERE codigo = '$params->codigo'");
    $resultado = $sentencia->execute();
  } else {
    $sentencia = $base_de_datos->prepare("INSERT INTO totem(codigo, codigo_sucursal, ubicacion) VALUES (?, ?, ?);");
    $resultado = $sentencia->execute([strtoupper($params->codigo), strtoupper($params->codigo_sucursal), strtoupper($params->ubicacion)]);
  }

  $query = "DELETE FROM totem_fila WHERE codigo_totem = '$params->codigo';";
  $s = $base_de_datos->query($query);
  $r = $s->execute();

  foreach ($filas as $fila) {
    $sen = $base_de_datos->prepare("INSERT INTO totem_fila(codigo_fila, codigo_totem, estado) VALUES (?, ?, ?);");
    $res = $sen->execute([strtoupper($fila->codigo_fila), strtoupper($fila->codigo_totem), true]);
  }

  $response = new Result();

  if ($resultado == true) {
    $response->mensaje = 'Totem guardado correctamente.';
  } else {
    $response->mensaje = 'Ocurrió un error al guardar el Totem.';
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
