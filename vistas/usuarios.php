<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include "../conexion/conexion.php";
include('../fusuarios/eliminar_usuario.php');
include('../fusuarios/agregar_usuario.php');
include('../fusuarios/editar_usuario.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/inicio.css" rel="stylesheet">
</head>

<body class="inicio d-flex flex-column min-vh-100">

    <div class="d-flex flex-grow-1">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="inicio.php">
                <img src="../IMG/log1.png" class="logo" alt="Logo">
            </a>
            <h3><?= $_SESSION['usuario'] ?></h3>
            <h3><?= $_SESSION['permiso'] ?></h3>
            <div class="linea-divisoria"></div>
            <nav class="nav flex-column mt-3 w-100">
                <?php if ($_SESSION['permiso'] === 'Administrador'): ?>
                    <a href="entradas.php" class="btn btn-light">Entradas</a>
                    <a href="salidas.php" class="btn btn-light">Salidas</a>
                    <a href="productos.php" class="btn btn-light">Productos</a>
                    <a style="background-color: #ffeb3b;" class="btn btn-light">Usuarios</a>
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
        <div class="flex-grow-1 p-4">
            <h1 class="text-center">Bienvenido al Panel de Usuarios</h1>

            <div class="container-fluid row">
                <div class="text-center p-4">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#modalAgregar">Registrar un Usuario</button>
                </div>

                <h3 class="text-center text-secondary">Usuarios</h3>
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th class="oculto">ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Permiso</th>
                            <th>Usuario</th>
                            <th>Contraseña</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $registros_por_pagina = 10;
                        $pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                        $offset = ($pagina_actual - 1) * $registros_por_pagina;

                        $sql = $conexion->query("SELECT * FROM usuarios ORDER BY id ASC LIMIT $registros_por_pagina OFFSET $offset");

                        while ($datos = $sql->fetch_object()) { ?>
                            <tr>
                                <td class="oculto"><?= $datos->id ?></td>
                                <td><?= $datos->nombre ?></td>
                                <td><?= $datos->apellido ?></td>
                                <td><?= $datos->permiso ?></td>
                                <td><?= $datos->usuario ?></td>
                                <td><?= $datos->contrasena ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning" data-id="<?= $datos->id ?>"
                                        data-nombre="<?= $datos->nombre ?>" data-apellido="<?= $datos->apellido ?>"
                                        data-usuario="<?= $datos->usuario ?>" data-permiso="<?= $datos->permiso ?>"
                                        data-contrasena="<?= $datos->contrasena ?>" data-bs-toggle="modal"
                                        data-bs-target="#modalEditar" onclick="cargarDatos(this)">Editar</button>

                                    <a href="usuarios.php?id=<?= $datos->id ?>" class="btn btn-danger"
                                        onclick="return eliminar()">Eliminar</a>
                                </td>
                            </tr>
                        <?php }

                        $resultado_total = $conexion->query("SELECT COUNT(*) as total FROM usuarios");
                        $row_total = $resultado_total->fetch_assoc();
                        $total_paginas = ceil($row_total['total'] / $registros_por_pagina);
                        ?>
                    </tbody>
                </table>

                <div class="text-center mt-3">
                    <nav aria-label="Paginación de usuarios">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                                <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                                    <a class="page-link" href="usuarios.php?pagina=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div> <!-- fin flex-grow-1 -->
    </div> <!-- fin d-flex flex-grow-1 -->

    <!-- Modal Agregar Usuario -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarLabel">Datos del Usuario</h5>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" id="nombre" class="form-control" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" id="apellido" class="form-control" name="apellido" required>
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" id="usuario" class="form-control" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="permiso" class="form-label">Permiso</label>
                            <select class="form-select" name="permiso" id="permiso" required>
                                <option>Administrador</option>
                                <option>Operador</option>
                                <option>No Permiso</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" id="contrasena" class="form-control" name="contrasena"
                                placeholder="Ingrese la contraseña" onfocus="this.type='text'" onblur="this.type='password'" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success" name="btnregistrar">Guardar</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../fusuarios/editar_usuario.php">
                        <input type="hidden" id="editar_id" name="id">
                        <div class="mb-3">
                            <label for="editar_nombre" class="form-label">Nombre</label>
                            <input type="text" id="editar_nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editar_apellido" class="form-label">Apellido</label>
                            <input type="text" id="editar_apellido" name="apellido" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editar_usuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" id="editar_usuario" name="usuario" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editar_permiso" class="form-label">Permiso</label>
                            <select id="editar_permiso" name="permiso" class="form-select" required>
                                <option>Administrador</option>
                                <option>Operador</option>
                                <option>No Permiso</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editar_contrasena" class="form-label">Contraseña</label>
                            <input type="password" id="editar_contrasena" name="contrasena" class="form-control"
                                onfocus="this.type='text'" onblur="this.type='password'" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success" name="btnEditar">Actualizar</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function eliminar() {
            return confirm("¿Estás seguro de eliminar este usuario?");
        }

        function cargarDatos(boton) {
            document.getElementById('editar_id').value = boton.dataset.id;
            document.getElementById('editar_nombre').value = boton.dataset.nombre;
            document.getElementById('editar_apellido').value = boton.dataset.apellido;
            document.getElementById('editar_usuario').value = boton.dataset.usuario;
            document.getElementById('editar_permiso').value = boton.dataset.permiso;
            document.getElementById('editar_contrasena').value = boton.dataset.contrasena;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Footer fijo al final -->
    <footer class="mt-auto text-center p-3 bg-light text-muted w-100">
        &copy; 2025 Cooperativa AgroInterra. Todos los derechos reservados.
    </footer>

</body>
</html>
