<?php
$host = "localhost";
$usuarioBD = "root";
$contrasenaBD = "root";
$nombreBD = "interra_store";

$conexion = new mysqli($host, $usuarioBD, $contrasenaBD, $nombreBD);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

?>
