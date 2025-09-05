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
    <title>Salidas</title>
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
                    <a style="background-color: #ffeb3b;" class="btn btn-light">Salidas</a>
                    <a href="productos.php" class="btn btn-light">Productos</a>
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
            <h1 class="text-center">Bienvenido al Panel de Salidas</h1>
            <h3 class="text-center p-4">Aquí podrás realizar la salida de los productos del almacén.</h3>

            <div class="container-fluid row">

                <div class="text-center p-4">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#modalRegistrarSalida">
                        Registrar nueva salida
                    </button>
                </div>

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
                            window.location.href = 'salidas.php';
                        }
                    });
                </script>

                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // PRODUCTOS
                        $buscar = isset($_GET['buscar']) ? trim($conexion->real_escape_string($_GET['buscar'])) : '';
                        $sql_productos = $conexion->query("
                            SELECT p.id, p.nombre, p.descripcion, p.stock, u.nombre AS unidad
                            FROM productos p
                            JOIN unidad_de_medida u ON p.id_unidad = u.id_unidad
                            " . ($buscar !== '' ? "WHERE p.nombre LIKE '%$buscar%' OR p.descripcion LIKE '%$buscar%'" : "") . "
                            ORDER BY p.id ASC
                        ");
                        while ($datos = $sql_productos->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $datos['nombre'] ?></td>
                                <td><?= $datos['descripcion'] ?></td>
                                <td><?= $datos['stock'] . ' ' . $datos['unidad'] ?></td>
                            </tr>
                        <?php } ?>

                        <?php
                        // MATERIAS PRIMAS
                        $sql_materias = $conexion->query("
                            SELECT m.id, m.nombre, m.cantidad_disponible, u.nombre AS unidad
                            FROM materiasprimas m
                            LEFT JOIN unidad_de_medida u ON m.id_unidad = u.id_unidad
                            " . ($buscar !== '' ? "WHERE m.nombre LIKE '%$buscar%'" : "") . "
                            ORDER BY m.id ASC
                        ");
                        while ($mp = $sql_materias->fetch_assoc()) { ?>
                            <tr class="table-secondary">
                                <td><?= $mp['nombre'] ?></td>
                                <td><em>Materia prima</em></td>
                                <td><?= $mp['cantidad_disponible'] . ' ' . $mp['unidad'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div> <!-- fin flex-grow-1 -->
    </div> <!-- fin d-flex flex-grow-1 -->

    <!-- Modal Ayuda -->
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

    <!-- Modal Registrar Salida -->
    <div class="modal fade" id="modalRegistrarSalida" tabindex="-1" aria-labelledby="tituloModalSalida" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formRegistrarSalida" method="POST" action="../fsalidas/registrar_salida.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModalSalida">Registrar salida de productos y materias primas</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="responsable" name="responsable" value="<?= $_SESSION['usuario'] ?>" required>

                        <!-- Productos -->
                        <h6>Productos</h6>
                        <template id="templateProducto">
                            <div class="input-group mb-2">
                                <select name="productos[]" class="form-select" required>
                                    <option value="">Seleccione un producto</option>
                                    <?php
                                    $productos = $conexion->query("SELECT id, nombre FROM productos ORDER BY nombre ASC");
                                    while ($p = $productos->fetch_assoc()) {
                                        echo "<option value='{$p['id']}'>{$p['nombre']}</option>";
                                    }
                                    ?>
                                </select>
                                <input type="number" name="cantidades_productos[]" class="form-control" placeholder="Cantidad" min="0.01" step="0.01" required>
                                <button type="button" class="btn btn-outline-danger" onclick="this.parentNode.remove()">✖</button>
                            </div>
                        </template>
                        <div id="contenedorProductos"></div>
                        <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="agregarProducto()">+ Agregar producto</button>

                        <!-- Materias primas -->
                        <h6 class="mt-4">Materias primas</h6>
                        <template id="templateMateria">
                            <div class="input-group mb-2">
                                <select name="materias[]" class="form-select" required>
                                    <option value="">Seleccione una materia prima</option>
                                    <?php
                                    $materias = $conexion->query("SELECT id, nombre FROM materiasprimas ORDER BY nombre ASC");
                                    while ($m = $materias->fetch_assoc()) {
                                        echo "<option value='{$m['id']}'>{$m['nombre']}</option>";
                                    }
                                    ?>
                                </select>
                                <input type="number" name="cantidades_materias[]" class="form-control" placeholder="Cantidad" min="0.01" step="0.01" required>
                                <button type="button" class="btn btn-outline-danger" onclick="this.parentNode.remove()">✖</button>
                            </div>
                        </template>
                        <div id="contenedorMaterias"></div>
                        <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="agregarMateria()">+ Agregar materia prima</button>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Registrar salida</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function agregarProducto() {
            const template = document.getElementById('templateProducto').innerHTML;
            const contenedor = document.getElementById('contenedorProductos');
            const div = document.createElement('div');
            div.innerHTML = template;
            contenedor.appendChild(div);
        }

        function agregarMateria() {
            const template = document.getElementById('templateMateria').innerHTML;
            const contenedor = document.getElementById('contenedorMaterias');
            const div = document.createElement('div');
            div.innerHTML = template;
            contenedor.appendChild(div);
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Footer fijo al final -->
    <footer class="mt-auto text-center p-3 bg-light text-muted w-100">
        &copy; 2025 Cooperativa AgroInterra. Todos los derechos reservados.
    </footer>
</body>

</html>
