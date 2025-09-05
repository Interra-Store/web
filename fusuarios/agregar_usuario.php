<?php



if (isset($_POST["btnregistrar"])) {

    if (
        !empty($_POST["nombre"]) &&
        !empty($_POST["apellido"]) &&
        !empty($_POST["permiso"]) &&
        !empty($_POST["usuario"]) &&
        !empty($_POST["contrasena"])
    ) {

        $nombre = $_POST["nombre"];
        $apellido = $_POST["apellido"];
        $permiso = $_POST["permiso"];
        $contrasena = $_POST["contrasena"];
        $usuario = $_POST["usuario"];

        $sql = $conexion->query("INSERT INTO usuarios (nombre, apellido, permiso, contrasena, usuario)
            VALUES ('$nombre', '$apellido', '$permiso', '$contrasena', '$usuario')");

        if ($sql) {
            echo "<script>alert('Usuario agregado con éxito'); window.location.href='../vistas/usuarios.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error al registrar usuario'); window.location.href='../vistas/usuarios.php';</script>";
        }

    } else {
        echo "<script>alert('Por favor, completa todos los campos');</script>";
    }
}
?>
