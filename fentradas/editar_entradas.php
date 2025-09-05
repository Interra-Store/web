<?php

include '../conexion/conexion.php';

if (isset($_POST["btnEditar"])) {
    $nombre = $_POST["nombre"];
    $cantidad_disponible = $_POST["cantidad_disponible"];
    $id_unidad = $_POST["id_unidad"];
    $id = $_POST["id"]; // ← el ID viene desde el formulario como campo oculto

    $sql = $conexion->query("
        UPDATE materiasprimas 
        SET nombre='$nombre', cantidad_disponible='$cantidad_disponible', id_unidad='$id_unidad' 
        WHERE id=$id
    ");

    if ($sql) {
        echo "<script>alert('Materia prima editada con exito.'); window.location.href='../vistas/entradas.php';</script>";
        exit;
    } else {
        echo "<script>alert('error al guardar los datos.'); window.location.href='../vistas/entradas.php';</script>";
        
    }
}

?>
