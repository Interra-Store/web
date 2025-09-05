<?php

if (!empty($_GET["id"])) {
    $id = $_GET["id"];
    $sql=$conexion->query("DELETE FROM usuarios WHERE id=$id");
    if ($sql==1) {
         
        
        

    } else {
        echo'<div class="alert alert-succsess">Error Al Eliminar El Usuario</div>';
    }
    
    echo "<script>alert('Usuario eliminado con éxito'); window.location.href='../vistas/usuarios.php';</script>";
}
?>