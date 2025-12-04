<?php
require __DIR__ . '/../models/conexion.php';

if (!isset($_POST['id'], $_POST['completada'])) {
    http_response_code(400);
    echo "Faltan datos";
    exit;
}

$id = (int) $_POST['id'];
$completada = $_POST['completada'] == '1' ? 1 : 0;

$stmt = $conn->prepare("UPDATE subtareas SET completada = ? WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo "Error al preparar: " . $conn->error;
    exit;
}

$stmt->bind_param("ii", $completada, $id);

if ($stmt->execute()) {
    echo "ok";
} else {
    http_response_code(500);
    echo "Error al ejecutar: " . $stmt->error;
}

$stmt->close();
