<?php
$pdo = new PDO("mysql:host=localhost;dbname=interra_store", "root", "root");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_unidad = $_POST['id_unidad'];
    //  $ingredientes = $_POST['ingredientes']; // Ya es array nativo

    $materias = $_POST['materia_prima_id'];
    $cantidades = $_POST['cantidad_utilizada'];

    $ingredientes = [];
    for ($i = 0; $i < count($materias); $i++) {
        $ingredientes[] = [
            'materia_prima_id' => $materias[$i],
            'cantidad' => $cantidades[$i]
        ];
    }


    // 1. Crear producto con stock = 0
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, stock, id_unidad) VALUES (?, ?, 0, ?)");
    $stmt->execute([$nombre, $descripcion, $id_unidad]);
    $producto_id = $pdo->lastInsertId();
    

    // 2. Insertar receta con ingredientes
    foreach ($ingredientes as $ing) {
        $materia_prima_id = $ing['materia_prima_id'];
        $cantidad = $ing['cantidad'];

        $stmt = $pdo->prepare("INSERT INTO recetas (producto_id, materia_prima_id, cantidad_utilizada) VALUES (?, ?, ?)");
        $stmt->execute([$producto_id, $materia_prima_id, $cantidad]);
        
    }

    // 3. Redirigir
    echo "<script>alert('Receta registrada con exito'); window.location.href='../vistas/recetas.php';</script>";
    exit;
}
?>