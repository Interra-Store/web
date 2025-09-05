<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

$permisoUsuario = $_SESSION['permiso'];
include "../conexion/conexion.php";

// --- FILTRO POR FECHA ---
$where = "";
if (isset($_GET['filtro'])) {
    $filtro = $_GET['filtro'];
    if ($filtro == "dia") {
        $where = "WHERE DATE(a.fecha) = CURDATE()";
    } elseif ($filtro == "semana") {
        $where = "WHERE YEARWEEK(a.fecha, 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($filtro == "mes") {
        $where = "WHERE YEAR(a.fecha) = YEAR(CURDATE()) AND MONTH(a.fecha) = MONTH(CURDATE())";
    }
}

$sql_historial = $conexion->query("
    SELECT a.*, 
        CASE a.tipo 
            WHEN 'producto' THEN p.nombre 
            WHEN 'materia_prima' THEN m.nombre 
        END AS nombre_item
    FROM actividad_salidas a
    LEFT JOIN productos p ON a.tipo = 'producto' AND a.id_item = p.id
    LEFT JOIN materiasprimas m ON a.tipo = 'materia_prima' AND a.id_item = m.id
    $where
    ORDER BY a.fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividad</title>
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
                    <a href="recetas.php" class="btn btn-light">Recetas</a>
                    <a href="unidades.php" class="btn btn-light">Unidades de Medida</a>
                    <a style="background-color: #ffeb3b;" class="btn btn-light">Actividad</a>
                <?php elseif ($_SESSION['permiso'] === 'Operador'): ?>
                    <a href="entradas.php" class="btn btn-light">Entradas</a>
                    <a href="salidas.php" class="btn btn-light">Salidas</a>
                <?php endif; ?>

                <a href="../login/logout.php" class="btn btn-danger mt-auto">Cerrar sesión</a>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <div class="flex-grow-1 p-4">
            <h1 class="text-center mb-4">Historial de Actividad</h1>
            <div class="container-fluid row">

                <!-- Filtro -->
                <form method="GET" class="mb-3 d-flex gap-2 justify-content-center">
                    <select name="filtro" class="form-select" style="width:200px;">
                        <option value="">-- Ver todo --</option>
                        <option value="dia" <?= (isset($_GET['filtro']) && $_GET['filtro'] == 'dia') ? 'selected' : '' ?>>
                            Hoy</option>
                        <option value="semana" <?= (isset($_GET['filtro']) && $_GET['filtro'] == 'semana') ? 'selected' : '' ?>>
                            Esta semana</option>
                        <option value="mes" <?= (isset($_GET['filtro']) && $_GET['filtro'] == 'mes') ? 'selected' : '' ?>>
                            Este mes</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>

                <!-- Tabla -->
                <div class="table-responsive d-flex justify-content-center">
                    <table class="table table-bordered table-hover align-middle text-center table-sm"
                        style="max-width: 1300px;">
                        <thead class="table-dark">
                            <tr>
                                <th>Tipo</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Unidad</th>
                                <th>Responsable</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $sql_historial->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= ucfirst($row['tipo']) ?></td>
                                    <td><?= $row['nombre_item'] ?></td>
                                    <td><?= $row['cantidad'] ?></td>
                                    <td><?= $row['unidad'] ?></td>
                                    <td><?= $row['responsable'] ?></td>
                                    <td><?= $row['fecha'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
        </div>
    </div>

    <!-- Modal de ayuda -->
    <div class="modal fade" id="modalAyuda" tabindex="-1" aria-labelledby="modalAyudaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalAyudaLabel">¿Necesitas ayuda?</h5>
                </div>

                <div class="modal-body">
                    <p>Hola <?php echo $_SESSION['usuario']; ?>, aquí tienes un resumen:</p>
                    <ul>
                        <li>El logo te regresa al inicio.</li>
                        <li>Tu nombre y permiso determinan qué módulos ves.</li>
                        <li>Los botones son accesos a módulos donde puedes editar, agregar o eliminar.</li>
                        <li>El botón de cerrar sesión termina tu sesión.</li>
                        <li>El filtro te permite ver actividad por día, semana o mes.</li>
                    </ul>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<footer style="text-align: center; padding: 1rem; background-color: #f4f4f4; font-size: 0.9rem; color: #555;">
    &copy; 2025 Cooperativa AgroInterra. Todos los derechos reservados.
</footer>


</html>