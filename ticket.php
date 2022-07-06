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
  include_once "utiles/credenciales.php";
  date_default_timezone_set($zonaHoraria);
  $fecha = date("Y-m-d H:i:s");
  $recordar = $params->recordatorio == false ? 'no' : 'yes';
  $query =
    "INSERT INTO ticket(codigo_fila, email, telefono, recordatorio, fecha_sacado, rut, nombres, numeracion)
     VALUES ('$params->codigo_fila', '$params->email', '$params->telefono', '$recordar', '$fecha', '$params->rut', '$params->nombres', 0) RETURNING numeracion, fecha_sacado;
     ";

  $conexion = pg_connect("host=" . $rutaServidor . " port=" . $puerto . " dbname=" . $nombreBaseDeDatos . " user=" . $usuario . " password=" . $clave . "") or die('Error al conectar con la base de datos: ' . pg_last_error());
  $resource = pg_Exec($conexion, $query);
  $resultado = pg_fetch_object($resource);

  $response = new Result();

  if ($resultado) {
    $response->mensaje = 'Usted reservó un ticket para ser atendido. Revise su correo electrónico.';

    $textoCodigo = $params->codigo_fila . "-" . $resultado->numeracion;
    $textoQR = $textoCodigo . " --> " . $fecha;

    $imagen = "";


    if ($recordar == 'yes') {
      $fecha_recordatorio = $fecha;
      if ($params->minutos > 0) {
        $horaMas = strtotime('+' . $params->minutos . 'minute', strtotime($fecha_recordatorio));
        $fecha_recordatorio = date('Y-m-d H:i:s', $horaMas);
      }
      $contenido = "Ha reservado el Ticket $textoCodigo en la fila $params->fila, revise el avance en: $params->url.";
      $numeracionn = $resultado->numeracion;
      $query =
        "INSERT INTO recordatorio(tipo_envio, contenido, codigo_fila, numeracion, direccion_envio, fecha_hora_envio)
                          VALUES ('SMS', '$contenido', '$params->codigo_fila', $numeracionn, '$params->telefono', '$fecha_recordatorio'); ";

      $conexion = pg_connect("host=" . $rutaServidor . " port=" . $puerto . " dbname=" . $nombreBaseDeDatos . " user=" . $usuario . " password=" . $clave . "") or die('Error al conectar con la base de datos: ' . pg_last_error());
      $resource = pg_Exec($conexion, $query);
    }

    $subject = "Ticket Generado " . $textoCodigo;
    $message = '<html lang="es" style="font-family: sans-serif; font-size: 12px; font-weight: bold;">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <style>
      *,
      *::before,
      *::after {
        box-sizing: border-box;
      }
    </style>
    
    <body style="margin: 0; line-height: 1.5; text-align: left; margin-left: 10%; margin-right: 10%;">
      <div style="height: 10px; background-color: #c81d89;"></div>
      <div style="height: 4px; background-color: #365963;"></div>
      <div style="height: 100px;">
      </div>
      <div style="text-align: center; font-weight: bold; font-size: 18px; color: #365963;">
        TICKET GENERADO VIRTUALMENTE </div>
      <div style="height: 4px; background-color: #365963;"></div>
      <div style="padding: 3% 3% 0 3%;">
        <b style="font-size: 16px;"> Estimado (a) ' . $params->nombres . '</b>
      </div>
      <div style="padding: 3%;">
        Es un placer saludarte, esperamos que todo est&eacute; saliendo bien, te informamos que se ha generado el siguiente
        ticket electr&oacute;nico:
      </div>
      <div style="background-color: #dedede; padding-top: 5px; padding-bottom: 5px;">
        <div
          style="border-bottom-style: dashed; border-color: #fff; border-top-style: dashed; border-top-width: 2px; border-bottom-width: 2px;">
          <div style="padding: 30px 3% 0 3%; display: flex;">
            <div
              style="color: white; text-align: right; padding: 2% 2% 0 2%; width: 33.333333%; max-width: 33.333333%; background-color: #365b61; border-radius: 20px 0 0 0;">
              <div>Fila</div>
            </div>
            <div
              style="padding: 2% 2% 0 2%; width: 66.666667%; max-width: 66.666667%; font-weight: bold; background-color: #ffff; border-radius: 0 5px 0 0;">
              <div>' . $params->fila . '</div>
            </div>
          </div>
          <div style="padding: 0 3% 0 3%; display: flex;">
            <div
              style="color: white; text-align: right; padding: 0 2% 0 2%; width: 33.333333%; max-width: 33.333333%; background-color: #365b61;">
              <div>N&uacute;mero de Ticket</div>
            </div>
            <div
              style="padding: 0 2% 0 2%; width: 66.666667%; max-width: 66.666667%; font-weight: bold; background-color: #ffff;">
              <div>' . $params->codigo_fila . "-" . $resultado->numeracion . '</div>
            </div>
          </div>
          <div style="padding: 0 3% 0 3%; display: flex;">
            <div
              style="color: white; text-align: right; padding: 0 2% 0 2%; width: 33.333333%; max-width: 33.333333%; background-color: #365b61;">
              <div>Fecha de emisi&oacute;n</div>
            </div>
            <div
              style="padding: 0 2% 0 2%; width: 66.666667%; max-width: 66.666667%; font-weight: bold; background-color: #ffff;">
              <div>' . $fecha . '</div>
            </div>
          </div>
          <div style="padding: 0 3% 0 3%; display: flex;">
            <div
              style="color: white; text-align: right; padding: 0 2% 2% 2%; width: 33.333333%; max-width: 33.333333%; background-color: #365b61; border-radius: 0 0 0 20px;">
              <div>Sucursal</div>
            </div>
            <div
              style="padding: 0 2% 2% 2%; width: 66.666667%; max-width: 66.666667%; font-weight: bold; background-color: #ffff; border-radius: 0 0 5px 0;">
              <div>' . $params->sucursal . '</div>
            </div>
          </div>
          <div style="padding: 10px 3% 10px 3%; display: flex;">
            <div style="color: white; text-align: right; padding: 2%; width: 33.333333%; max-width: 33.333333%;">
            
              <a href=' . $params->url . '> Visualizar Atenciones </a>

            </div>
            <div style="padding: 2%; width: 66.666667%; max-width: 66.666667%; color: #616060;">
              <i>La representaci&oacute;n impresa del comprobante electr&oacute;nico es el archivo PDF adjunto, no posee validez
                tributaria y es necesario que la imprima.</i>
            </div>
          </div>
        </div>
      </div>
      <div style="text-align: center;">
        <br>
        <p>Gracias por preferirnos: <br></p>
        <p>Atentamente,</p><br>
        <div><strong>Consultorio Odontológico Dr. Luis Rubio</strong></div>
        <div><strong>RUC: </strong>0940811227001</div>
        <div><strong>Direcci&oacute;n: </strong>Riobamba - Chimborazo - Ecuador</div>
        <div><strong>Tel&eacute;fono: </strong>0980429165</div>
        <div><strong>Email: </strong>luferupo_0609@hotmail.com</div>
        <br>
        <br>
      </div>
      <div
        style="text-align: center; color: #365b61;">
        <br>
        <br><br>
        <div>QVirtual. Software electr&oacute;nico para la emisi&oacute;n de tickets, cont&aacute;ctenos al </div>
        <div> (51)72966371 / (593)968802318 / (593)962853503,</div>
        <div>estaremos gustosos en atender sus requerimientos.</div>
        <br><br>
      </div>

      <div style="height: 4px; background-color: #365963;"></div>
      <div style="height: 10px; background-color: #c81d89;"></div>
    </body>
    
    </html>';

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
  $response->mensaje =  $th->getMessage();
  header('Content-Type: application/json');
  echo json_encode($response);
}
