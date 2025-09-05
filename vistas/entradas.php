<?php
session_start();
include "../conexion/conexion.php"; // ajusta ruta si hace falta

// --- ELIMINAR ---
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    if ($id > 0) {
        $sql = "DELETE FROM materiasprimas WHERE id = $id";
        if ($conexion->query($sql) === TRUE) {
            // Redirige después de eliminar para limpiar la URL
            header("Location: entradas.php?msg=eliminado");
            exit;
        } else {
            echo "Error al eliminar: " . $conexion->error;
        }
    }
}

include('../fentradas/agregar_entrada.php');
include('../fentradas/editar_entradas.php');


?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materias Primas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/inicio.css" rel="stylesheet">
</head>

<!-- body ahora es un flex-column de mínimo 100vh -->

<body class="inicio d-flex flex-column min-vh-100">
    <!-- contenedor principal que crece -->
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
                    <a class="btn btn-light" style="background-color: #ffeb3b;">Entradas</a>
                    <a href="salidas.php" class="btn btn-light">Salidas</a>
                    <a href="productos.php" class="btn btn-light">Productos</a>
                    <a href="usuarios.php" class="btn btn-light">Usuarios</a>
                    <a href="recetas.php" class="btn btn-light">Recetas</a>
                    <a href="unidades.php" class="btn btn-light">Unidades de Medida</a>
                    <a href="actividad.php" class="btn btn-light">Actividad</a>
                <?php elseif ($_SESSION['permiso'] === 'Operador'): ?>
                    <a style="background-color: #ffeb3b;" class="btn btn-light">Entradas</a>
                    <a href="salidas.php" class="btn btn-light">Salidas</a>
                <?php endif; ?>

                <a href="../login/logout.php" class="btn btn-danger mt-auto">Cerrar sesión</a>
            </nav>
        </aside>


        <!-- Contenido principal -->
        <div class="flex-grow-1 p-4">
            <h1 style="text-align: center;">Bienvenido al Panel de Materias Primas</h1>


            <div class="container-fluid row">
                <div class="text-center p-4">
                    <button type="button" class="btn btn-success" href="#" data-bs-toggle="modal"
                        data-bs-target="#modalAgregar">Registra Una Materia Prima</button>
                </div>
                <h3 class="text-center text-secondary">Materias Primas</h3>
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th class="oculto" scope="col">ID</th>
                            <?php if ($_SESSION['permiso'] === 'Administrador'): ?>
                                <th scope="col">Nombre</th>
                                <th scope="col">Cantidad Disponible</th>
                                <th scope="col">Acciones</th>
                            <?php elseif ($_SESSION['permiso'] === 'Operador'): ?>
                                <th scope="col">Nombre</th>
                                <th scope="col">Cantidad Disponible</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $registros_por_pagina = 10;
                        $pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                        $offset = ($pagina_actual - 1) * $registros_por_pagina;

                        $sql = $conexion->query("
            SELECT mp.id, mp.nombre AS nombre_materia_prima, mp.cantidad_disponible, 
                   u.nombre AS unidad_medida, mp.id_unidad
            FROM materiasprimas mp 
            JOIN unidad_de_medida u ON mp.id_unidad = u.id_unidad 
            ORDER BY mp.id ASC 
            LIMIT $registros_por_pagina OFFSET $offset
        ");

                        while ($datos = $sql->fetch_object()) { ?>
                            <tr>
                                <td class="oculto"><?= $datos->id ?></td>
                                <td><?= $datos->nombre_materia_prima ?></td>
                                <td><?= $datos->cantidad_disponible . " " . $datos->unidad_medida ?></td>

                                <?php if ($_SESSION['permiso'] === 'Administrador'): ?>
                                    <td>
                                        <!-- Botón Editar -->
                                        <button type="button" class="btn btn-warning" data-id="<?= $datos->id ?>"
                                            data-nombre="<?= $datos->nombre_materia_prima ?>"
                                            data-cantidad_disponible="<?= $datos->cantidad_disponible ?>"
                                            data-id_unidad="<?= $datos->id_unidad ?>" data-bs-toggle="modal"
                                            data-bs-target="#modalEditar" onclick="cargarDatos(this)">
                                            Editar
                                        </button>

                                        <!-- Botón Eliminar -->
                                        <a href="entradas.php?eliminar=<?= $datos->id ?>" class="btn btn-danger btn-small"
                                            onclick="return confirm('¿Seguro que quieres eliminar este registro?');">
                                            Eliminar
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php }

                        // Calcular total de páginas
                        $resultado_total = $conexion->query("SELECT COUNT(*) as total FROM materiasprimas");
                        $row_total = $resultado_total->fetch_assoc();
                        $total_paginas = ceil($row_total['total'] / $registros_por_pagina);
                        ?>
                    </tbody>
                </table>

                <div class="text-center mt-3 w-100">
                    <nav aria-label="Paginación de usuarios">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                                <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                                    <a class="page-link" href="entradas.php?pagina=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div> <!-- fin del d-flex flex-grow-1 -->



    <?php
    $consulta_unidades = $conexion->query("SELECT id_unidad, nombre FROM unidad_de_medida");
    ?>

    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Registrar Materia Prima</h5>
                </div>

                <div class="modal-body">
                    <form method="POST" action="../fentradas/agregar_entrada.php">
                        <input type="hidden" name="id" id="id">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="apellido" class="form-label">Cantidad Disponible</label>
                            <input type="number" id="cantidad_disponible" name="cantidad_disponible"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="id_unidad" class="form-label">Unidad</label>
                            <select id="id_unidad" name="id_unidad" class="form-select" required>
                                <option value="">Seleccione una unidad</option>
                                <?php while ($unidad = $consulta_unidades->fetch_object()) { ?>
                                    <option value="<?= $unidad->id_unidad ?>"><?= $unidad->nombre ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success" name="btnAgregar">Agregar</button>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>

    </div>

    <?php
    $consulta_unidades_edicion = $conexion->query("SELECT id_unidad, nombre FROM unidad_de_medida");
    ?>


    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Editar Materia Prima</h5>
                </div>

                <div class="modal-body">
                    <form method="POST" action="../fentradas/editar_entradas.php">
                        <input type="hidden" name="id" id="editar_id">


                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" id="editar_nombre" name="nombre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="apellido" class="form-label">Cantidad Disponible</label>
                            <input type="number" id="editar_cantidad" name="cantidad_disponible" class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="id_unidad" class="form-label">Unidad</label>
                            <select class="form-select" id="editar_unidad" name="id_unidad">
                                <?php
                                $unidades = $conexion->query("SELECT id_unidad, nombre FROM unidad_de_medida");
                                while ($u = $unidades->fetch_object()) {
                                    echo "<option value='$u->id_unidad'>$u->nombre</option>";
                                }
                                ?>
                            </select>
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
            var respuesta = confirm("Estas Seguro De Eliminar?");
            return respuesta;
        }

        function cargarDatos(boton) {
            const id = boton.getAttribute('data-id');
            const nombre = boton.getAttribute('data-nombre');
            const cantidad = boton.getAttribute('data-cantidad_disponible');
            const unidad = boton.getAttribute('data-id_unidad');

            document.getElementById('editar_id').value = id;
            document.getElementById('editar_nombre').value = nombre;
            document.getElementById('editar_cantidad').value = cantidad;

            const selectUnidad = document.getElementById('editar_unidad');
            for (let i = 0; i < selectUnidad.options.length; i++) {
                if (selectUnidad.options[i].value == unidad) {
                    selectUnidad.selectedIndex = i;
                    break;
                }
            }
        }
    </script>

    <!-- Bootstrap JS (solo una vez) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
<!-- Footer dentro del body, empujado al fondo por mt-auto y layout flex -->
<footer class="mt-auto text-center p-3 bg-light text-muted w-100">
    &copy; 2025 Cooperativa AgroInterra. Todos los derechos reservados.
</footer>

</html>