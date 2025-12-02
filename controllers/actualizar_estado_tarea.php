<?php
require __DIR__ . '/../models/conexion.php';

if (!isset($_POST['id'], $_POST['estado'])) {
    header('Location: ../controllers/dashboard.php');
    exit;
}

$id = (int) $_POST['id'];
$nuevoEstado = $_POST['estado'];

// Validar que el estado sea uno de los permitidos
$estadosValidos = ['pendiente', 'en_curso', 'terminada'];

if (!in_array($nuevoEstado, $estadosValidos, true)) {
    header('Location: ../controllers/dashboard.php?error=estado_invalido');
    exit;
}

$stmt = $conn->prepare("UPDATE tareas SET estado = ? WHERE id = ?");
$stmt->bind_param("si", $nuevoEstado, $id);
$stmt->execute();
$stmt->close();

header('Location: ../controllers/dashboard.php');
exit;
