<?php
$host = "localhost";
$user = "root";      // cámbialo si usas otro usuario
$pass = "";          // pon tu contraseña si tienes
$db   = "gestion_tareas";

$conn = new mysqli("localhost:3307", "root", "", "gestion_tareas");

if ($conn->connect_error) {
    die("<p style='color:red;'>Error de conexión a la base de datos: " . $conn->connect_error . "</p>");
}

$conn->set_charset("utf8mb4");
?>
