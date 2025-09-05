<?php
include("../conexion/conexion.php");

if (isset($_GET['id_unidad'])) {
    $id = intval($_GET['id_unidad']);

    if ($id > 0) {
        $stmt = $conexion->prepare("DELETE FROM unidad_de_medida WHERE id_unidad = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>alert('Unidad eliminada correctamente'); window.location='../vistas/unidades.php';</script>";
        } else {
            echo "<script>alert('Error al eliminar la unidad'); window.location='../vistas/unidades.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('ID inválido'); window.location='../vistas/unidades.php';</script>";
    }
} else {
    echo "<script>alert('No se recibió el ID'); window.location='../vistas/unidades.php';</script>";
}