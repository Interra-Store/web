<?php
include("../conexion/conexion.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conexion->query("DELETE FROM materiasprimas WHERE id = $id");
}

// Redirige de vuelta a la lista SIN id
header("Location: ../vistas/entradas.php");
exit;
?>