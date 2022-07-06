<?php
// $clave = "melli123";
// $usuario = "pi";
// $nombreBaseDeDatos = "ticket";
// $rutaServidor = "localhost";
// $puerto = "5432";
$clave = "5d85e10117864c5c36c4ae02493f2d167dd3a1271ce130637c999119355161e6";
$usuario = "ryyhmkbdyklkyf";
$nombreBaseDeDatos = "d28ppmvrq8aa89";
$rutaServidor = "ec2-44-206-89-185.compute-1.amazonaws.com";
$puerto = "5432";
try {
    $base_de_datos = new PDO("pgsql:host=$rutaServidor;port=$puerto;dbname=$nombreBaseDeDatos", $usuario, $clave);
    $base_de_datos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Ocurrió un error con la base de datos: " . $e->getMessage();
}
