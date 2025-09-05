<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include "../conexion/conexion.php";
include('../frecetas/agregar_receta.php');

// Traer materias primas con su unidad
$materias = $conexion->query("
    SELECT mp.id, mp.nombre, um.nombre AS unidad
    FROM materiasprimas mp
    LEFT JOIN unidad_de_medida um ON mp.id_unidad = um.id_unidad
    ORDER BY mp.nombre ASC
");

$opcionesMaterias = "";
while ($m = $materias->fetch_assoc()) {
    $unidad = $m['unidad'] ?? '—';
    $opcionesMaterias .= "<option value='{$m['id']}' data-unidad='{$unidad}'>{$m['nombre']}</option>";
}

function obtenerIngredientes($producto_id, $pdo)
{
    $stmt = $pdo->prepare("SELECT materia_prima_id, cantidad_utilizada FROM recetas WHERE producto_id = ?");
    $stmt->execute([$producto_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$consulta_unidades = $conexion->query("SELECT id_unidad, nombre FROM unidad_de_medida");
$consulta_unidades_editar = $conexion->query("SELECT id_unidad, nombre FROM unidad_de_medida");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recetas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/inicio.css" rel="stylesheet">
</head>

<body class="inicio">
    <div class="d-flex">
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
                    <a href="usuarios.php" class="btn btn-light">Usuarios</a>
                    <a style="background-color: #ffeb3b;" class="btn btn-light">Recetas</a>
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
            <h1 class="text-center">Bienvenido al Panel de Recetas</h1>

            <div class="container-fluid row">
                <div div class="text-center p-4">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#modalAgregarReceta">Registra Una Receta</button>
                </div>

                <h3 class="text-center text-secondary">Recetas</h3>
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Producto</th>
                            <th scope="col">Materias Primas</th>
                            <th scope="col">Cantidades</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $registros_por_pagina = 10;
                        $pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                        $offset = ($pagina_actual - 1) * $registros_por_pagina;

                        $sql = $conexion->query("
                    SELECT p.id, p.nombre AS nombre_producto, p.descripcion, p.id_unidad,
                           GROUP_CONCAT(mp.nombre ORDER BY mp.id SEPARATOR '\n') AS nombre_materias_primas,
                           GROUP_CONCAT(CONCAT(r.cantidad_utilizada, ' ', IFNULL(um.nombre, '—')) ORDER BY mp.id SEPARATOR '\n') AS cantidad_con_unidad
                    FROM productos p
                    JOIN recetas r ON p.id = r.producto_id
                    JOIN materiasprimas mp ON r.materia_prima_id = mp.id
                    LEFT JOIN unidad_de_medida um ON mp.id_unidad = um.id_unidad
                    GROUP BY p.id
                    ORDER BY p.id ASC
                    LIMIT $registros_por_pagina OFFSET $offset
                ");

                        while ($datos = $sql->fetch_object()) { ?>
                            <tr>
                                <td><?= $datos->nombre_producto ?></td>
                                <td><?= nl2br($datos->nombre_materias_primas) ?></td>
                                <td><?= nl2br($datos->cantidad_con_unidad) ?></td>
                                <td>
                                    <?php $ingredientes = obtenerIngredientes($datos->id, $pdo); ?>
                                    <button class="btn btn-warning" data-producto='<?= htmlspecialchars(json_encode([
                                        "id" => $datos->id,
                                        "nombre" => $datos->nombre_producto,
                                        "descripcion" => $datos->descripcion,
                                        "id_unidad" => $datos->id_unidad,
                                        "ingredientes" => $ingredientes
                                    ]), ENT_QUOTES, 'UTF-8') ?>' onclick="abrirModalEditarDesdeData(this)">
                                        Editar
                                    </button>
                                    <a href="../frecetas/eliminar_receta.php?producto_id=<?= $datos->id ?>"
                                        class="btn btn-small btn-danger" onclick="return eliminar()">Eliminar</a>
                                </td>
                            </tr>
                        <?php }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal agregar receta -->
    <div class="modal fade" id="modalAgregarReceta" tabindex="-1" aria-hidden="true"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formAgregarReceta" method="POST" action="../frecetas/agregar_receta.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar nueva receta</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del producto</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unidad de medida</label>
                            <select name="id_unidad" class="form-select" required>
                                <option value="">Seleccione una unidad</option>
                                <?php while ($unidad = $consulta_unidades->fetch_object()) { ?>
                                    <option value="<?= $unidad->id_unidad ?>"><?= $unidad->nombre ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <h6>Ingredientes</h6>
                        <template id="templateOpcionesMateria"><?= $opcionesMaterias ?></template>
                        <div id="contenedorIngredientes"></div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                            onclick="agregarIngrediente()">+ Agregar ingrediente</button>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar receta</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal editar (simplificado y funcional) -->
    <div class="modal fade" id="modalEditarReceta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formEditarReceta" method="POST" action="../frecetas/editar_receta.php">
                <input type="hidden" name="producto_id" id="editarProductoId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar producto con receta</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del producto</label>
                            <input type="text" class="form-control" id="editarNombreProducto" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="editarDescripcionProducto" name="descripcion"
                                rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unidad de medida</label>
                            <select id="editarUnidad" name="id_unidad" class="form-select" required>
                                <option value="">Seleccione una unidad</option>
                                <?php while ($unidad = $consulta_unidades_editar->fetch_object()) { ?>
                                    <option value="<?= $unidad->id_unidad ?>"><?= $unidad->nombre ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <h6>Ingredientes</h6>
                        <template id="templateOpcionesMateriaEditar"><?= $opcionesMaterias ?></template>
                        <div id="contenedorIngredientesEditar"></div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                            onclick="agregarIngredienteEditar()">+ Agregar ingrediente
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Actualizar receta</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function abrirModalEditarDesdeData(btn) {
            const producto = JSON.parse(btn.getAttribute('data-producto'));
            abrirModalEditar(producto);
        }

        function abrirModalEditar(producto) {
            document.getElementById('editarProductoId').value = producto.id;
            document.getElementById('editarNombreProducto').value = producto.nombre;
            document.getElementById('editarDescripcionProducto').value = producto.descripcion;
            document.getElementById('editarUnidad').value = producto.id_unidad;

            const contenedor = document.getElementById('contenedorIngredientesEditar');
            contenedor.innerHTML = '';
            producto.ingredientes.forEach(ingrediente => agregarIngredienteEditar(ingrediente));

            new bootstrap.Modal(document.getElementById('modalEditarReceta')).show();
        }

        function agregarIngredienteEditar(data = {}) {
            const contenedor = document.getElementById('contenedorIngredientesEditar');
            const templateOpciones = document.getElementById('templateOpcionesMateriaEditar').innerHTML;

            const fila = document.createElement('div');
            fila.classList.add('row', 'mb-2', 'ingrediente');

            const colMateria = document.createElement('div');
            colMateria.classList.add('col-md-6');
            const select = document.createElement('select');
            select.name = 'materia_prima_id[]';
            select.classList.add('form-select');
            select.required = true;
            select.innerHTML = templateOpciones;
            if (data.materia_prima_id) select.value = data.materia_prima_id;
            colMateria.appendChild(select);

            const colCantidad = document.createElement('div');
            colCantidad.classList.add('col-md-4', 'd-flex', 'align-items-center', 'gap-2');
            const inputCantidad = document.createElement('input');
            inputCantidad.type = 'number';
            inputCantidad.name = 'cantidad_utilizada[]';
            inputCantidad.classList.add('form-control');
            inputCantidad.step = '0.01';
            inputCantidad.placeholder = 'Cantidad';
            inputCantidad.required = true;
            inputCantidad.value = data.cantidad_utilizada || '';

            const unidadSpan = document.createElement('span');
            unidadSpan.classList.add('text-muted');
            unidadSpan.textContent = select.selectedOptions[0].dataset.unidad || '—';

            select.addEventListener('change', () => {
                unidadSpan.textContent = select.selectedOptions[0].dataset.unidad || '—';
            });

            colCantidad.appendChild(inputCantidad);
            colCantidad.appendChild(unidadSpan);

            const colEliminar = document.createElement('div');
            colEliminar.classList.add('col-md-2');
            const btnEliminar = document.createElement('button');
            btnEliminar.type = 'button';
            btnEliminar.classList.add('btn', 'btn-danger');
            btnEliminar.innerText = '✖';
            btnEliminar.onclick = () => fila.remove();
            colEliminar.appendChild(btnEliminar);

            fila.appendChild(colMateria);
            fila.appendChild(colCantidad);
            fila.appendChild(colEliminar);
            contenedor.appendChild(fila);
        }


        function agregarIngrediente(data = {}) {
            const contenedor = document.getElementById('contenedorIngredientes');
            const templateOpciones = document.getElementById('templateOpcionesMateria').innerHTML;

            const fila = document.createElement('div');
            fila.classList.add('row', 'mb-2', 'ingrediente');

            const colMateria = document.createElement('div');
            colMateria.classList.add('col-md-6');
            const select = document.createElement('select');
            select.name = 'materia_prima_id[]';
            select.classList.add('form-select');
            select.required = true;
            select.innerHTML = templateOpciones;
            if (data.materia_prima_id) select.value = data.materia_prima_id;
            colMateria.appendChild(select);

            const colCantidad = document.createElement('div');
            colCantidad.classList.add('col-md-4', 'd-flex', 'align-items-center', 'gap-2');
            const inputCantidad = document.createElement('input');
            inputCantidad.type = 'number';
            inputCantidad.name = 'cantidad_utilizada[]';
            inputCantidad.classList.add('form-control');
            inputCantidad.step = '0.01';
            inputCantidad.placeholder = 'Cantidad';
            inputCantidad.required = true;
            inputCantidad.value = data.cantidad_utilizada || '';

            const unidadSpan = document.createElement('span');
            unidadSpan.classList.add('text-muted');
            unidadSpan.textContent = select.selectedOptions[0].dataset.unidad || '—';

            select.addEventListener('change', () => {
                unidadSpan.textContent = select.selectedOptions[0].dataset.unidad || '—';
            });

            colCantidad.appendChild(inputCantidad);
            colCantidad.appendChild(unidadSpan);

            const colEliminar = document.createElement('div');
            colEliminar.classList.add('col-md-2');
            const btnEliminar = document.createElement('button');
            btnEliminar.type = 'button';
            btnEliminar.classList.add('btn', 'btn-danger');
            btnEliminar.innerText = '✖';
            btnEliminar.onclick = () => fila.remove();
            colEliminar.appendChild(btnEliminar);

            fila.appendChild(colMateria);
            fila.appendChild(colCantidad);
            fila.appendChild(colEliminar);
            contenedor.appendChild(fila);
        }

        function eliminar() {
            return confirm("¿Seguro que quieres eliminar esta receta?");
        }
    </script>
</body>



</html>
<footer style="text-align: center; padding: 1rem; background-color: #f4f4f4; font-size: 0.9rem; color: #555;">
    &copy; 2025 Cooperativa AgroInterra. Todos los derechos reservados.
</footer>