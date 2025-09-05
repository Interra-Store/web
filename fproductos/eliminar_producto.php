<?php
include '../conexion/conexion.php';

$id_producto = intval($_GET['id']);

// se eliminan las recetas con el id del producto
$conexion->query("DELETE FROM recetas WHERE producto_id = $id_producto");

// se elimina el producto para no dejar recetas huérfanas o sin producto
$conexion->query("DELETE FROM productos WHERE id = $id_producto");


echo "<script>alert('Producto eliminado correctamente.'); window.location.href='../vistas/productos.php';</script>";
?>
