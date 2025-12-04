<?php
require __DIR__ . '/../models/conexion.php';

if (!isset($_POST['id'])) {
    http_response_code(400);
    echo "Faltan datos";
    exit;
}

$id = (int) $_POST['id'];
if ($id <= 0) {
    http_response_code(400);
    echo "ID inválido";
    exit;
}

$stmt_del = $conn->prepare("DELETE FROM subtareas WHERE id = ?");
if (!$stmt_del) {
    http_response_code(500);
    echo "Error al preparar: " . $conn->error;
    exit;
}

$stmt_del->bind_param("i", $id);

if ($stmt_del->execute()) {
    echo "ok";
} else {
    http_response_code(500);
    echo "Error al ejecutar: " . $stmt_del->error;
}

$stmt_del->close();
