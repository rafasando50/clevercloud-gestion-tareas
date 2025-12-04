<?php
require __DIR__ . '/../models/conexion.php';

if (!isset($_POST['tarea_id'], $_POST['titulo'])) {
    http_response_code(400);
    echo "Faltan datos";
    exit;
}

$tarea_id = (int) $_POST['tarea_id'];
$titulo   = trim($_POST['titulo']);

if ($tarea_id <= 0 || $titulo === '') {
    http_response_code(400);
    echo "Datos inválidos";
    exit;
}

// Usamos otro nombre de variable para NO chocar con $stmt del modal
$stmt_insert = $conn->prepare("INSERT INTO subtareas (tarea_id, titulo) VALUES (?, ?)");
if (!$stmt_insert) {
    http_response_code(500);
    echo "Error al preparar: " . $conn->error;
    exit;
}

$stmt_insert->bind_param("is", $tarea_id, $titulo);

if ($stmt_insert->execute()) {
    // Cerramos el statement del INSERT
    $stmt_insert->close();

    // Volver a renderizar el modal con la tarea y sus subtareas actualizadas
    $id = $tarea_id; // ver_tarea_modal.php usa $id
    require __DIR__ . '/../modals/ver_tarea_modal.php';
} else {
    $error = $stmt_insert->error;
    $stmt_insert->close();

    http_response_code(500);
    echo "Error al ejecutar: " . $error;
}
