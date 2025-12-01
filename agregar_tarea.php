<?php
require "conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo       = trim($_POST["titulo"] ?? "");
    $descripcion  = trim($_POST["descripcion"] ?? "");
    $fecha_inicio = $_POST["fecha_inicio"] ?: null;
    $fecha_fin    = $_POST["fecha_fin"] ?: null;

    if ($titulo === "") {
        $mensaje = "<p class='error-msg'>El título es obligatorio.</p>";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO tareas (titulo, descripcion, fecha_inicio, fecha_fin, estado)
            VALUES (?, ?, ?, ?, 'pendiente')
        ");

        $stmt->bind_param("ssss", $titulo, $descripcion, $fecha_inicio, $fecha_fin);

        if ($stmt->execute()) {
            // Redirigir al dashboard después de guardar
            header("Location: dashboard.php");
            exit;
        } else {
            $mensaje = "<p class='error-msg'>Error al guardar la tarea: " . $conn->error . "</p>";
        }
    }
}

include("includes/header.php");
?>

<div class="form-container">
  <div class="form-box">
    <div class="form-header">
      <h2>Nueva tarea</h2>
      <a href="dashboard.php" class="close-btn">&times;</a>
    </div>

    <?php
    if (!empty($mensaje)) {
        echo $mensaje;
    }
    ?>

    <form method="POST" action="">
      <label for="titulo">Título</label>
      <input type="text" id="titulo" name="titulo" required>

      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="4"></textarea>

      <div class="fecha-group">
        <div>
          <label for="fecha_inicio">Fecha de inicio</label>
          <input type="date" id="fecha_inicio" name="fecha_inicio">
        </div>
        <div>
          <label for="fecha_fin">Fecha de fin</label>
          <input type="date" id="fecha_fin" name="fecha_fin">
        </div>
      </div>

      <button type="submit" class="btn-primary">Guardar</button>
    </form>
  </div>
</div>

<?php include("includes/footer.php"); ?>
