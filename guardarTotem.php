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

if($params->esEdicion){
  $sentencia = $base_de_datos->prepare("UPDATE  totem
                                        SET (codigo, codigo_tienda, ubicacion) =
                                        ('$params->codigo', upper('$params->codigo_tienda'), upper('$params->ubicacion')) WHERE codigo = '$params->codigo'");
  $resultado = $sentencia->execute();  
}  else{
    $sentencia = $base_de_datos->prepare("INSERT INTO totem(codigo, codigo_tienda, ubicacion) VALUES (?, ?, ?);");
    $resultado = $sentencia->execute([strtoupper($params->codigo), strtoupper($params->codigo_tienta), strtoupper($params->ubicacion)]);
    
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