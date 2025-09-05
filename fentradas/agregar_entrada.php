<?php 
include '../conexion/conexion.php';


if (isset($_POST["btnAgregar"])) {

    // Sanitizamos los datos (mínimo protección básica)
    $nombre = trim($_POST["nombre"]);
    $cantidad_disponible = trim($_POST["cantidad_disponible"]);
    $id_unidad = intval($_POST['id_unidad']); // Convertir a entero

    // Validación: asegurarse que los campos no estén vacíos
    if ($nombre === '' || $cantidad_disponible === '' || $id_unidad === 0) {
        die("Error: Datos incompletos o inválidos");
    }

    // Modo edición (si se está actualizando)
    if (isset($_POST['modoEdicion']) && $_POST['modoEdicion'] == 'true' && isset($_POST['idEditar'])) {
        $idEditar = intval($_POST['idEditar']);

        $sql = $conexion->query("UPDATE materiasprimas SET nombre='$nombre', cantidad_disponible='$cantidad_disponible', id_unidad='$id_unidad' WHERE id=$idEditar");
    } else {
        // Modo agregar nuevo
        $sql = $conexion->query("INSERT INTO materiasprimas (nombre, cantidad_disponible, id_unidad) VALUES ('$nombre', '$cantidad_disponible', '$id_unidad')");
    }

    // Resultado y redirección
    if ($sql) {
        echo "<script>alert('Materia prima registrada correctamente.'); window.location.href='../vistas/entradas.php';</script>";
        exit;
    } else {
        die("Error en la consulta: " . $conexion->error);
    }
}


?>