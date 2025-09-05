<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

$permisoUsuario = $_SESSION['permiso'];
include "../conexion/conexion.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
                    <a style="background-color: #ffeb3b;" class="btn btn-light">Productos</a>
                    <a href="usuarios.php" class="btn btn-light">Usuarios</a>
                    <a href="recetas.php" class="btn btn-light">Recetas</a>
                    <a href="unidades.php" class="btn btn-light">Unidades de Medida</a>
                    <a href="actividad.php" class="btn btn-light">Actividad</a>
                <?php elseif ($_SESSION['permiso'] === 'Operador'): ?>
                    <a href="entradas.php" class="btn btn-light">Entradas</a>
                    <a style="background-color: #ffeb3b;" class="btn btn-light">Salidas</a>
                <?php endif; ?>

                <a href="../login/logout.php" class="btn btn-danger mt-auto">Cerrar sesión</a>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <div class="flex-grow-1 p-4">
            <h1 class="text-center">Bienvenido al Panel de Productos</h1>
            <h3 class="text-center p-4">Aquí podrás agregar el stock de los productos que hayas creado previamente.</h3>
            <h4 class="text-center p-4">Si deseas editar aspectos como el nombre o la descripción, deberás hacerlo desde la página de recetas.</h4>

            <div class="container-fluid row">
                <form method="GET" class="mb-3 busqueda-productos">
                    <div class="input-group">
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar producto..."
                            value="<?= isset($_GET['buscar']) ? $_GET['buscar'] : '' ?>">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </form>

                <script>
                    document.querySelector('input[name="buscar"]').addEventListener('input', function () {
                        if (this.value.trim() === '') {
                            window.location.href = 'productos.php';
                        }
                    });
                </script>

                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre del Producto</th>
                            <th>Descripción</th>
                            <th>Stock</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $registros_por_pagina = 10;
                        $pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                        $offset = ($pagina_actual - 1) * $registros_por_pagina;

                        $buscar = isset($_GET['buscar']) ? trim($conexion->real_escape_string($_GET['buscar'])) : '';
                        $where = ($buscar !== '') ? "WHERE p.nombre LIKE '%$buscar%'" : '';

                        $sql = $conexion->query("
                            SELECT p.id, p.nombre, p.descripcion, p.stock, u.nombre AS unidad 
                            FROM productos p 
                            JOIN unidad_de_medida u ON p.id_unidad = u.id_unidad 
                            $where 
                            ORDER BY p.id ASC 
                            LIMIT $registros_por_pagina OFFSET $offset
                        ");

                        while ($datos = $sql->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $datos['nombre'] ?></td>
                                <td><?= $datos['descripcion'] ?></td>
                                <td><?= $datos['stock'] . ' ' . $datos['unidad'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-fabricar" data-id="<?= $datos['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($datos['nombre']) ?>" data-bs-toggle="modal"
                                        data-bs-target="#modalFabricar">
                                        <i class="bi bi-hammer"></i> Fabricar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-eliminar" data-id="<?= $datos['id'] ?>">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php }

                        $resultado_total = $conexion->query("SELECT COUNT(*) as total FROM productos p $where");
                        $row_total = $resultado_total->fetch_assoc();
                        $total_paginas = ceil($row_total['total'] / $registros_por_pagina);
                        ?>
                    </tbody>
                </table>

                <div class="text-center mt-3">
                    <nav aria-label="Paginación de productos">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                                <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="productos.php?pagina=<?= $i ?>&buscar=<?= urlencode($buscar) ?>"><?= $i ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div> <!-- fin flex-grow-1 -->
    </div> <!-- fin d-flex flex-grow-1 -->

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
                        <li><img src="../IMG/log1.png" alt="Logo"> Icono superior izquierdo que te regresa a esta pantalla de inicio.</li>
                        <li><img src="../IMG/flecha.png" alt="Flecha"> Justo debajo del logo verás tu nombre de usuario y permiso.</li>
                        <li>La lista de botones son los módulos del sistema: puedes editar, agregar o eliminar registros.</li>
                        <li>El botón de cerrar sesión termina tu sesión cuando hayas terminado tu labor.</li>
                    </ul>
                </section>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </footer>
            </div>
        </div>
    </div>

    <!-- Modal Fabricar -->
    <div class="modal fade" id="modalFabricar" tabindex="-1" aria-labelledby="modalFabricarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalFabricarLabel">Fabricar producto</h5>
                </div>

                <form id="formFabricar" method="POST" action="../fproductos/fabricar.php">
                    <div class="modal-body">
                        <input type="hidden" name="id_producto" id="id_producto">
                        <div class="mb-3">
                            <label for="nombre_producto" class="form-label">Producto</label>
                            <input type="text" class="form-control" id="nombre_producto" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="cantidad" class="form-label">Cantidad a fabricar</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
                            <div id="maxInfo" class="form-text text-muted"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Confirmar</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-fabricar').forEach(btn => {
            btn.addEventListener('click', async function () {
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;

                document.getElementById('id_producto').value = id;
                document.getElementById('nombre_producto').value = nombre;

                const res = await fetch(`consultar_maximo.php?id=${id}`);
                const data = await res.json();

                document.getElementById('cantidad').max = data.maximo;
                document.getElementById('maxInfo').textContent = `Máximo posible: ${data.maximo} unidades`;
            });
        });

        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                if (confirm('¿Estás seguro de que deseas eliminar este producto? Si lo haces se eliminará su receta.')) {
                    window.location.href = `../fproductos/eliminar_producto.php?id=${id}`;
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Footer fijo al final -->
    <footer class="mt-auto text-center p-3 bg-light text-muted w-100">
        &copy; 2025 Cooperativa AgroInterra. Todos los derechos reservados.
    </footer>

</body>
</html>
