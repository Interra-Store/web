<?php
include '../conexion/conexion.php';

$id_producto = intval($_GET['id']);
$receta = $conexion->query("
  SELECT r.materia_prima_id, r.cantidad_utilizada, m.cantidad_disponible
  FROM recetas r
  JOIN materiasprimas m ON r.materia_prima_id = m.id
  WHERE r.producto_id = $id_producto
");

$max_unidades = PHP_INT_MAX;

while ($row = $receta->fetch_assoc()) {
  $unidades = floor($row['cantidad_disponible'] / $row['cantidad_utilizada']);
  if ($unidades < $max_unidades) {
    $max_unidades = $unidades;
  }
}

echo json_encode(['maximo' => $max_unidades]);
