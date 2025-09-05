<?php
include "conexion/conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/stylelogin.css" rel="stylesheet">
</head>
<body class="login d-flex justify-content-center align-items-center min-vh-100 bg-light">
  <div class="card shadow p-4 rounded-4" style="width: 22rem;">
    <h2 class="text-center mb-4">Iniciar Sesión</h2>
    <form action="login/login.php" method="POST">
      <div class="mb-3">
        <label for="username" class="form-label">Usuario</label>
        <input type="text" class="form-control" name="usuario" required autofocus>

      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" name="contrasena" required>
      </div>
      <button type="submit" class="btn btn-primary w-100 rounded-3">Entrar</button>
    </form>
  </div>
</body>
</html>