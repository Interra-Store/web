<?php
require_once '../conexion/conexion.php';

if (!isset($_GET['producto_id'])) {
    header('Location: ../vistas/recetas.php?error=sin_id');
    exit;
}

$producto_id = intval($_GET['producto_id']);

try {
    // Elimina todos los ingredientes asociados al producto
    $conexion->query("DELETE FROM recetas WHERE producto_id = $producto_id");

    // Opcional: también puedes eliminar el producto si ya no lo necesitas
    // $conexion->query("DELETE FROM productos WHERE id = $producto_id");

    header('Location: ../vistas/recetas.php?exito=eliminado');
    exit;
} catch (Exception $e) {
    error_log("Error al eliminar receta: " . $e->getMessage());
    echo "<script>alert('Receta eliminada con exito'); window.location.href='../vistas/recetas.php';</script>";
    exit;
}