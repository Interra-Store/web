<?php
include '../conexion/conexion.php';

$responsable = $_POST['responsable'] ?? 'Sin nombre';

// === PRODUCTOS ===
if (isset($_POST['productos']) && isset($_POST['cantidades_productos'])) {
    foreach ($_POST['productos'] as $index => $id_producto) {
        $cantidad = floatval($_POST['cantidades_productos'][$index]);

        // Validar stock disponible
        $consulta = $conexion->query("SELECT stock, id_unidad FROM productos WHERE id = $id_producto");
        if ($consulta && $consulta->num_rows > 0) {
            $producto = $consulta->fetch_assoc();
            if ($producto['stock'] >= $cantidad) {
                // Actualizar stock
                $conexion->query("UPDATE productos SET stock = stock - $cantidad WHERE id = $id_producto");

                // Obtener unidad
                $unidad = $conexion->query("SELECT nombre FROM unidad_de_medida WHERE id_unidad = {$producto['id_unidad']}")->fetch_assoc()['nombre'];

                // Registrar actividad
                $conexion->query("
                    INSERT INTO actividad_salidas (tipo, id_item, cantidad, unidad, responsable)
                    VALUES ('producto', $id_producto, $cantidad, '$unidad', '$responsable')
                ");
            }
        }
    }
}

// === MATERIAS PRIMAS ===
if (isset($_POST['materias']) && isset($_POST['cantidades_materias'])) {
    foreach ($_POST['materias'] as $index => $id_materia) {
        $cantidad = floatval($_POST['cantidades_materias'][$index]);

        // Validar stock disponible
        $consulta = $conexion->query("SELECT cantidad_disponible, id_unidad FROM materiasprimas WHERE id = $id_materia");
        if ($consulta && $consulta->num_rows > 0) {
            $materia = $consulta->fetch_assoc();
            if ($materia['cantidad_disponible'] >= $cantidad) {
                // Actualizar stock
                $conexion->query("UPDATE materiasprimas SET cantidad_disponible = cantidad_disponible - $cantidad WHERE id = $id_materia");

                // Obtener unidad
                $unidad = $conexion->query("SELECT nombre FROM unidad_de_medida WHERE id_unidad = {$materia['id_unidad']}")->fetch_assoc()['nombre'];

                // Registrar actividad
                $conexion->query("
                    INSERT INTO actividad_salidas (tipo, id_item, cantidad, unidad, responsable)
                    VALUES ('materia_prima', $id_materia, $cantidad, '$unidad', '$responsable')
                ");
            }
        }
    }
}

echo "<script>alert('Salida registrada con exito'); window.location.href='../vistas/salidas.php';</script>";
exit;
?>
