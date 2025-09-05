<?php
if (isset($_POST["btnregistrarunidad"])) {

    if (
        !empty($_POST["nombre"]) 

    ) {

        $nombre = $_POST["nombre"];
       

        $sql = $conexion->query("INSERT INTO unidad_de_medida (nombre)
            VALUES ('$nombre')");

        if ($sql) {
            echo "<script>alert('Unidad Registrada con Exito'); window.location.href='../vistas/unidades.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error al registrar usuario');</script>";
        }

    } else {
        echo "<script>alert('Por favor, completa todos los campos');</script>";
    }

}
?>
