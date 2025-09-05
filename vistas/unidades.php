<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include "../conexion/conexion.php";
include('../funidades/agregar_unidad.php');
include('../funidades/editar_unidad.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidades de Medida</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/inicio.css" rel="stylesheet">
</head>

<body class="inicio d-flex flex-column min-vh-100">

    <div class="d-flex flex-grow-1">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="inicio.php"><img src="../IMG/log1.png" class="logo" alt="Logo"></a>
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
                    <a style="background-color: #ffeb3b;" class="btn btn-light">Unidades de Medida</a>
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
            <h1 class="text-center p-4">Panel de Unidades de Medida</h1>

            <div class="text-center p-4">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalUnidad">
                    Registrar nueva unidad
                </button>
            </div>

            <h3 class="text-center text-secondary">Unidades de Medida</h3>
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th class="oculto">ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $registros_por_pagina = 10;
                    $pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                    $pagina_actual = max($pagina_actual, 1);
                    $offset = ($pagina_actual - 1) * $registros_por_pagina;

                    $sql = $conexion->query("SELECT * FROM unidad_de_medida ORDER BY id_unidad ASC LIMIT $registros_por_pagina OFFSET $offset");
                    while ($datos = $sql->fetch_object()) { ?>
                        <tr>
                            <td class="oculto"><?= $datos->id_unidad ?></td>
                            <td><?= $datos->nombre ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-id="<?= $datos->id_unidad ?>"
                                    data-nombre="<?= $datos->nombre ?>" onclick="abrirModalEditar(this)">
                                    Editar
                                </button>
                                <a href="../funidades/eliminar_unidad.php?id_unidad=<?= $datos->id_unidad ?>"
                                    class="btn btn-danger btn-sm" onclick="return eliminar()">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php }

                    $resultado_total = $conexion->query("SELECT COUNT(*) as total FROM unidad_de_medida");
                    $row_total = $resultado_total->fetch_assoc();
                    $total_paginas = ceil($row_total['total'] / $registros_por_pagina);
                    ?>
                </tbody>
            </table>

            <div class="text-center mt-3">
                <nav aria-label="Paginación de unidades">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                            <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                                <a class="page-link" href="unidades.php?pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div> <!-- fin flex-grow-1 -->
    </div> <!-- fin d-flex flex-grow-1 -->

    <!-- Modal Nueva Unidad -->
    <div class="modal fade" id="modalUnidad" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Unidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" placeholder="Ingrese el nombre" name="nombre"
                                required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success" name="btnregistrarunidad">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Unidad -->
    <div class="modal fade" id="modalEditarUnidad" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Unidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" id="editarIdUnidad" name="id_unidad">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" id="editarNombreUnidad" class="form-control" name="nombre" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success" name="btneditarunidad">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function eliminar() {
            return confirm("¿Estás seguro de eliminar esta unidad?");
        }

        function abrirModalEditar(button) {
            document.getElementById("editarIdUnidad").value = button.dataset.id;
            document.getElementById("editarNombreUnidad").value = button.dataset.nombre;
            new bootstrap.Modal(document.getElementById('modalEditarUnidad')).show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Footer fijo al final -->
    <footer class="mt-auto text-center p-3 bg-light text-muted w-100">
        &copy; 2025 Cooperativa AgroInterra. Todos los derechos reservados.
    </footer>
</body>

</html>