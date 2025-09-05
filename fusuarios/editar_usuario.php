<?php
include '../conexion/conexion.php';



if (isset($_POST['btnEditar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario = $_POST['usuario'];
    $permiso = $_POST['permiso'];
    $contrasena = $_POST['contrasena'];

    $consulta = $conexion->prepare("UPDATE usuarios SET nombre=?, apellido=?, usuario=?, permiso=?, contrasena=? WHERE id=?");
    $consulta->bind_param("sssssi", $nombre, $apellido, $usuario, $permiso, $contrasena, $id);
    
    if ($consulta->execute()) {
        echo "<script>alert('Usuario actualizado con éxito'); window.location.href='../vistas/usuarios.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar usuario'); window.location.href='../vistas/usuarios.php';</script>";
    }
}
?>




