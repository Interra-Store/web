<?php
if (isset($_POST['btneditarunidad'])) {
    include("../conexion/conexion.php");

    $id_unidad = $_POST['id_unidad'];
    $nombre = $_POST['nombre'];

    if (!empty($id_unidad) && !empty($nombre)) {
        // Usar sentencia preparada para seguridad
        $stmt = $conexion->prepare("UPDATE unidad_de_medida SET nombre = ? WHERE id_unidad = ?");
        $stmt->bind_param("si", $nombre, $id_unidad);

        if ($stmt->execute()) {
            echo "<script>alert('Unidad actualizada correctamente'); window.location='../vistas/unidades.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar la unidad'); window.location='../vistas/unidades.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Por favor completa todos los campos'); window.location='../vistas/unidades.php';</script>";
    }
}