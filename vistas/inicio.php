<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

$permisoUsuario = $_SESSION['permiso'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/inicio.css" rel="stylesheet">
</head>

<body class="inicio">
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar">
            <img src="../IMG/log1.png" class="logo" alt="Logo">
            <h3><?= $_SESSION['usuario'] ?></h3>
            <h3><?= $_SESSION['permiso'] ?></h3>
            <div class="linea-divisoria"></div>
            <nav class="nav flex-column mt-3 w-100">
                <?php if ($_SESSION['permiso'] === 'Administrador'): ?>
                    <a href="entradas.php" class="btn btn-light">Entradas</a>
                    <a href="salidas.php" class="btn btn-light">Salidas</a>
                    <a href="productos.php" class="btn btn-light">Productos</a>
                    <a href="usuarios.php" class="btn btn-light">Usuarios</a>
                    <a href="recetas.php" class="btn btn-light">Recetas</a>
                    <a href="unidades.php" class="btn btn-light">Unidades de Medida</a>
                    <a href="actividad.php" class="btn btn-light">Actividad</a>
                <?php elseif ($_SESSION['permiso'] === 'Operador'): ?>
                    <a href="entradas.php" class="btn btn-light">Entradas</a>
                    <a href="salidas.php" class="btn btn-light">Salidas</a>
                <?php endif; ?>

                <a href="../login/logout.php" class="btn btn-danger mt-auto">Cerrar sesión</a>
            </nav>
        </aside>

        <!-- Contenido principal -->

        <main class="main-content">
            <div class="bloque-bienvenida">
                <header class="text-center py-3 bg-light">
                    <h1>Hola!! <?= $_SESSION['usuario'] ?>, Bienvenido al Panel de Control</h1>
                </header>

                <p class="permiso-text">
                    <?php
                    if ($permisoUsuario === 'Administrador') {
                        echo 'Actualmente, usted cuenta con el rango de Administrador esto le da un acceso total a todo el sistema de inventario Interra-Store.';
                    } elseif ($permisoUsuario === 'Operador') {
                        echo 'Actualmente, usted cuenta con el rango de Operador, esto le da acceso al almacén de materias primas y salidas. En caso de un error, comuníquese con el administrador.';
                    } else {
                        echo 'Si usted no cuenta con un permiso, así que le será imposible interactuar con el sistema de inventario.';
                    }
                    ?>
                </p>
            </div>

            <!-- Iconos flotantes -->
            <div class="iconos-flotantes">
                <a href="https://www.facebook.com/share/1XNprcq3jr/?mibextid=qi2Omg">
                    <img src="../IMG/facebook.png" alt="Facebook">
                </a>
                <a href="https://www.instagram.com/agrointerra?igsh=MWMwdWhneXFpbmh1bg==">
                    <img src="../IMG/instagram.png" alt="Instagram">
                </a>
                <a href="https://www.tiktok.com/@agrointerra?is_from_webapp=1&sender_device=pc">
                    <img src="../IMG/tik-tok.png" alt="TikTok">
                </a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalAyuda">
                    <img src="../IMG/pregunta.png" alt="Ayuda">
                </a>
            </div>
        </main>



    </div>

    <!-- Modal de ayuda -->
    <div class="modal fade" id="modalAyuda" tabindex="-1" aria-labelledby="modalAyudaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <header class="modal-header">
                    <h5 class="modal-title" id="modalAyudaLabel">¿Necesitas ayuda?</h5>
                </header>
                <section class="modal-body">
                    <p>Hola <?= $_SESSION['usuario']; ?>, te ofrecemos un breve resumen de las acciones posibles:</p>
                    <ul class="lista-ayuda">
                        <li><img src="../IMG/log1.png" alt="Logo"> Icono superior izquierdo que te regresa a esta
                            pantalla de inicio.</li>
                        <li><img src="../IMG/flecha.png" alt="Flecha"> Justo debajo del logo verás tu nombre de usuario
                            y permiso.</li>
                        <li>La lista de botones son los módulos del sistema: puedes editar, agregar o eliminar
                            registros.</li>
                        <li>El botón de cerrar sesión termina tu sesión cuando hayas terminado tu labor.</li>
                    </ul>
                </section>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </footer>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<footer style="text-align: center; padding: 1rem; background-color: #f4f4f4; font-size: 0.9rem; color: #555;">
  &copy; 2025 Cooperativa AgroInterra. Todos los derechos reservados.
</footer>


</html>