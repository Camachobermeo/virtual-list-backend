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
    $sentencia = $base_de_datos->prepare("UPDATE  tienda
                                          SET (codigo, rut, direccion, telefono, l_inicio_atencion, l_fin_atencion, m_inicio_atencion, m_fin_atencion, mm_inicio_atencion, mm_fin_atencion, j_inicio_atencion, j_fin_atencion, v_inicio_atencion, v_fin_atencion, s_inicio_atencion, s_fin_atencion, d_inicio_atencion, d_fin_atencion) =
                                          ( '$params->codigo', upper('$params->rut'), upper('$params->direccion'), upper('$params->telefono'), '$params->l_inicio_atencion', '$params->l_fin_atencion', '$params->m_inicio_atencion', '$params->m_fin_atencion', '$params->mm_inicio_atencion', '$params->mm_fin_atencion', '$params->j_inicio_atencion', '$params->j_fin_atencion', '$params->v_inicio_atencion', '$params->v_fin_atencion', '$params->s_inicio_atencion', '$params->s_fin_atencion', '$params->d_inicio_atencion', '$params->d_fin_atencion') 
                                          WHERE codigo = '$params->codigo'");
    $resultado = $sentencia->execute();  
  }  else{
    $sentencia = $base_de_datos->prepare("INSERT INTO tienda(codigo, rut, direccion, telefono, l_inicio_atencion, l_fin_atencion, m_inicio_atencion, m_fin_atencion, mm_inicio_atencion, mm_fin_atencion, j_inicio_atencion, j_fin_atencion, v_inicio_atencion, v_fin_atencion, s_inicio_atencion, s_fin_atencion, d_inicio_atencion, d_fin_atencion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $resultado = $sentencia->execute([strtoupper($params->codigo), strtoupper($params->rut), strtoupper($params->direccion), strtoupper($params->telefono), $params->l_inicio_atencion, $params->l_fin_atencion, $params->m_inicio_atencion, $params->m_fin_atencion, $params->mm_inicio_atencion, $params->mm_fin_atencion, $params->j_inicio_atencion, $params->j_fin_atencion, $params->v_inicio_atencion, $params->v_fin_atencion, $params->s_inicio_atencion, $params->s_fin_atencion, $params->d_inicio_atencion, $params->d_fin_atencion]);
  
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