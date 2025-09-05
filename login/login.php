<?php
session_start();
include "../conexion/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = ($_POST['contrasena']);

    $stmt = $conexion->prepare("SELECT id, permiso FROM usuarios WHERE usuario=? AND contrasena=?");
    $stmt->bind_param("ss", $usuario, $contrasena);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $datos = $resultado->fetch_assoc();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['permiso'] = $datos['permiso'];
        echo "<script>alert('Acceso con exito'); window.location.href='../vistas/inicio.php';</script>";
        exit;
    } else {
      echo "<script>alert('Tu usuario o contraseña no son correctos, intenta nuevamente o notifica de tu problema al administrador'); window.location.href='../login.php';</script>";
        
    }
}
?>

