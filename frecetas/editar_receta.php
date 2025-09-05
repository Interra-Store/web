<?php
require_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Acceso no permitido.";
    exit;
}

// Corregido $_POST
if (!isset($_POST['producto_id'], $_POST['nombre'], $_POST['materia_prima_id'], $_POST['cantidad_utilizada'], $_POST['id_unidad'])) {
    die('Datos incompletos');
}

$producto_id = intval($_POST['producto_id']);
$nombre = trim($_POST['nombre']);
$descripcion = trim($_POST['descripcion'] ?? '');
$id_unidad = $_POST['id_unidad'];
$materias = $_POST['materia_prima_id'];
$cantidades = $_POST['cantidad_utilizada'];

try {
    // UPDATE producto
    $conexion->query("UPDATE productos SET nombre = '$nombre', descripcion = '$descripcion', id_unidad = '$id_unidad' WHERE id = $producto_id");

    // Borrar ingredientes antiguos
    $conexion->query("DELETE FROM recetas WHERE producto_id = $producto_id");

    // Insertar nuevos ingredientes
    foreach ($materias as $i => $materia_id) {
        $cantidad = floatval($cantidades[$i]);
        $conexion->query("INSERT INTO recetas (producto_id, materia_prima_id, cantidad_utilizada) VALUES ($producto_id, $materia_id, $cantidad)");
    }

    echo "<script>alert('Receta editada con exito'); window.location.href='../vistas/recetas.php';</script>";
    exit;

} catch (Exception $e) {
    echo "<script>alert('Error al editar la receta'); window.location.href='../vistas/recetas.php';</script>";
    exit;
}