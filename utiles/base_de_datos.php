<?php
$clave = "5d85e10117864c5c36c4ae02493f2d167dd3a1271ce130637c999119355161e6";
$usuario = "ryyhmkbdyklkyf";
$nombreBaseDeDatos = "d28ppmvrq8aa89";
$rutaServidor = "ec2-44-206-89-185.compute-1.amazonaws.com";
$puerto = "5432";
// $clave = "88d308eb5345f7237be7c1d94d979ac5eec7bc863a9b484b47252a737e01366f";
// $usuario = "awubqqjaicjvpm";
// $nombreBaseDeDatos = "d6u69047lqlu4b";
// $rutaServidor = "ec2-34-239-33-57.compute-1.amazonaws.com";
// $puerto = "5432";
try {
    $base_de_datos = new PDO("pgsql:host=$rutaServidor;port=$puerto;dbname=$nombreBaseDeDatos", $usuario, $clave);
    $base_de_datos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Ocurrió un error con la base de datos: " . $e->getMessage();
}
