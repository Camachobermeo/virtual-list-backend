<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


$json = file_get_contents('php://input');

$params = json_decode($json);

$PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

//html PNG location prefix
$PNG_WEB_DIR = 'temp/';

class Result
{
}

try {

  include_once "utiles/base_de_datos.php";
  include_once "utiles/phpqrcode.php";
  // echo '<img src="' . $PNG_WEB_DIR . basename($filename) . '" />';

  $fecha = date("Y-m-d H:i:s");
  $recordar = $params->recordatorio == false ? 'no' : 'yes';
  $query =
    "INSERT INTO ticket(codigo_tipo_operacion, email, telefono, recordatorio, fecha_sacado, rut, nombres, numeracion)
     VALUES ('$params->codigo_tipo_operacion', '$params->email', '$params->telefono', '$recordar', '$fecha', '$params->rut', '$params->nombres', 0) RETURNING numeracion;
     ";

  $conexion = pg_connect("host=" . $rutaServidor . " port=" . $puerto . " dbname=" . $nombreBaseDeDatos . " user=" . $usuario . " password=" . $clave . "") or die('Error al conectar con la base de datos: ' . pg_last_error());
  $resource = pg_Exec($conexion, $query);
  $resultado = pg_fetch_object($resource);

  $response = new Result();

  if ($resultado) {
    $response->mensaje = 'Usted reservó un ticket para ser atendido. Revise su correo electrónico.';

    if (!file_exists($PNG_TEMP_DIR))
      mkdir($PNG_TEMP_DIR);

    $textoCodigo = $params->codigo_tipo_operacion . "-" . $resultado->numeracion;
    $textoQR = $textoCodigo . " --> " . $fecha;

    $filename = $PNG_TEMP_DIR . 'test.png';
    $filename = $PNG_TEMP_DIR . 'test' . md5($textoQR . '|H|10') . '.png';
    QRcode::png($textoQR, $filename, 'H', '10', 2);
    $imagen = "";

    if ($filename) {
      $imgbinary = fread(fopen($filename, "r"), filesize($filename));
      $imagen = 'data:image/png;base64,' . base64_encode($imgbinary);
    }

    $subject = "Ticket Generado " . $textoCodigo;

    $message = '<h3>Hola: ' . $params->nombres . ' <br> </h3> Usted reservó el siguiente ticket: 
              <img src="' . $imagen . '" />
              <img src="https://drive.google.com/file/d/1EjngHyBSjDtrd0SubkFlcE2FTlYSSICz/view?usp=sharing">          
              <br> 
              <img src="' . $imagen . '" />
              <img src="https://k60.kn3.net/E/7/F/2/4/8/472.gif">
              <br> 
              <img src="' . $imagen . '" />
              <img src="https://drive.google.com/file/d/1j4YJqotD7-VfAEbqXTW4t7QyF6f8v_Dx/view">
              Presentar el siguiente ticket al ingresar.';

    $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
    $cabeceras .= 'Content-type:  text/html; charset=iso-8859-1' . "\r\n";

    $enviado = mail($params->email, $subject, $message, $cabeceras);
  } else {
    $response->mensaje = 'Ocurrió un error al reservar un ticket.';
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
