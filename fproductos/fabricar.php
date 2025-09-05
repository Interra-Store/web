<?php
include '../conexion/conexion.php';

$id_producto = intval($_POST['id_producto']);
$cantidad = intval($_POST['cantidad']);

// Validar receta y disponibilidad
$receta = $conexion->query("
  SELECT r.materia_prima_id, r.cantidad_utilizada, m.cantidad_disponible
  FROM recetas r
  JOIN materiasprimas m ON r.materia_prima_id = m.id
  WHERE r.producto_id = $id_producto
");

foreach ($receta as $row) {
  $total_necesario = $row['cantidad_utilizada'] * $cantidad;
  if ($row['cantidad_disponible'] < $total_necesario) {
    echo "<script>alert('No hay suficientes materias primas.'); window.location.href='../vistas/productos.php';</script>";
    exit;
  }
}

// se actualiza stock del producto
$conexion->query("UPDATE productos SET stock = stock + $cantidad WHERE id = $id_producto");

// se descuentan las cantidades utilizadas de materias primas
foreach ($receta as $row) {
  $id_materia = $row['materia_prima_id'];
  $total = $row['cantidad_utilizada'] * $cantidad;
  $conexion->query("UPDATE materiasprimas SET cantidad_disponible = cantidad_disponible - $total WHERE id = $id_materia");
}

echo "<script>alert('Se fabricaron $cantidad unidades.'); window.location.href='../vistas/productos.php';</script>";
