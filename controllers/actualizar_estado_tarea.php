<?php
require __DIR__ . '/../models/conexion.php';

// Verificar que llegaron los datos
if (!isset($_POST['id'], $_POST['estado'])) {
    http_response_code(400);
    echo "Faltan datos";
    exit;
}

$id = (int) $_POST['id'];
$nuevoEstado = $_POST['estado'];

// Validar que el estado sea uno de los permitidos
$estadosValidos = ['pendiente', 'en_curso', 'terminada'];

if (!in_array($nuevoEstado, $estadosValidos, true)) {
    http_response_code(400);
    echo "Estado inválido";
    exit;
}

$stmt = $conn->prepare("UPDATE tareas SET estado = ? WHERE id = ?");
$stmt->bind_param("si", $nuevoEstado, $id);

if ($stmt->execute()) {
    echo "ok"; // respuesta para el fetch()
} else {
    http_response_code(500);
    echo "Error al actualizar";
}

$stmt->close();
