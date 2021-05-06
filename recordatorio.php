<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
$json = file_get_contents('php://input');
$params = json_decode($json);

use Twilio\Rest\Client;

require_once "Twilio/autoload.php";
class Result
{
}

try {
  include_once "utiles/base_de_datos.php";
  include_once "utiles/constantes.php";
  include_once "utiles/credenciales.php";
  $response = new Result();

  date_default_timezone_set($zonaHoraria);
  $fecha = date("Y-m-d H:i:s");
  $horaMas = strtotime('+5 minute', strtotime($fecha));
  $horaMas = date('Y-m-d H:i:s', $horaMas);
  $horaMenos = strtotime('-5 minute', strtotime($fecha));
  $horaMenos = date('Y-m-d H:i:s', $horaMenos);

  $query = "SELECT * FROM recordatorio WHERE (fecha_hora_envio between '$horaMenos' and '$horaMas') and not estado;";
  $sentencia = $base_de_datos->query($query);
  $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);

  if ($resultado) {
    foreach ($resultado as $fila) {
      try {
        if ($fila->direccion_envio) {
          $twilio = new Client($sid, $token);
          $message = $twilio->messages
            ->create(
              $fila->direccion_envio, // to 
              array(
                "messagingServiceSid" => "MG0610d38b1751e630c70bcca7064dee10",
                "body" => $fila->contenido
              )
            );
          $sentencia = $base_de_datos->prepare("
                                  UPDATE recordatorio SET (estado) = (?) WHERE secuencial = '$fila->secuencial'");
          $resultado = $sentencia->execute([
            true
          ]);
        }
      } catch (\Throwable $th) {
        $response->errorCelular = $th->getMessage();
      }
    }
  }

  $response->resultado = $resultado;
  $response->mensaje = 'Datos Listados Correctamente';
  header('Content-Type: application/json');
  echo json_encode($response);
} catch (Exception $th) {
  $response = new Result();
  $response->resultado = [];
  $response->mensaje = "error desconocido";
}
