<?php
require __DIR__ . "/../models/conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    header("Location: dashboard.php");
    exit;
}

$mensaje = "";

// Si enviaron el formulario (guardar o eliminar)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["guardar"])) {
        $titulo       = trim($_POST["titulo"] ?? "");
        $descripcion  = trim($_POST["descripcion"] ?? "");
        $fecha_inicio = $_POST["fecha_inicio"] ?: null;
        $fecha_fin    = $_POST["fecha_fin"] ?: null;

        if ($titulo === "") {
            $mensaje = "<p class='error-msg'>El título es obligatorio.</p>";
        } else {
            $stmt = $conn->prepare("
                UPDATE tareas
                SET titulo = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ?
                WHERE id = ?
            ");
            $stmt->bind_param("ssssi", $titulo, $descripcion, $fecha_inicio, $fecha_fin, $id);

            if ($stmt->execute()) {
                header("Location: dashboard.php");
                exit;
            } else {
                $mensaje = "<p class='error-msg'>Error al actualizar: " . $conn->error . "</p>";
            }
        }
    } elseif (isset($_POST["eliminar"])) {
        $stmt = $conn->prepare("DELETE FROM tareas WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit;
        } else {
            $mensaje = "<p class='error-msg'>Error al eliminar: " . $conn->error . "</p>";
        }
    }
}

// Obtener datos de la tarea
$stmt = $conn->prepare("
    SELECT titulo, descripcion, fecha_inicio, fecha_fin, estado
    FROM tareas
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$tarea = $result->fetch_assoc();

if (!$tarea) {
    // Si no existe la tarea, regresar al dashboard
    header("Location: dashboard.php");
    exit;
}

include __DIR__ . "/../includes/header.php";
?>

<div class="form-container">
  <div class="form-box">
    <div class="form-header">
      <h2>Editar tarea</h2>
      <a href="dashboard.php" class="close-btn">&times;</a>
    </div>

    <?php
    if (!empty($mensaje)) {
        echo $mensaje;
    }
    ?>

    <form method="POST" action="">
      <label for="titulo">Título</label>
      <input
        type="text"
        id="titulo"
        name="titulo"
        value="<?php echo htmlspecialchars($tarea['titulo']); ?>"
        required
      >

      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="4"><?php
        echo htmlspecialchars($tarea['descripcion'] ?? "");
      ?></textarea>

      <div class="fecha-group">
        <div>
          <label for="fecha_inicio">Fecha de inicio</label>
          <input
            type="date"
            id="fecha_inicio"
            name="fecha_inicio"
            value="<?php echo htmlspecialchars($tarea['fecha_inicio'] ?? ""); ?>"
          >
        </div>
        <div>
          <label for="fecha_fin">Fecha de fin</label>
          <input
            type="date"
            id="fecha_fin"
            name="fecha_fin"
            value="<?php echo htmlspecialchars($tarea['fecha_fin'] ?? ""); ?>"
          >
        </div>
      </div>

      <div class="btn-group">
        <button type="submit" name="guardar" class="btn-primary">Guardar</button>
        <button type="submit" name="eliminar" class="btn-primary">Eliminar</button>
      </div>
    </form>
  </div>
</div>

<script>
  const btnEliminar = document.querySelector('button[name="eliminar"]');
  const form = document.querySelector('form');

  if (btnEliminar && form) {
    btnEliminar.addEventListener('click', () => {
      // Quita "required" de todos los campos antes de enviar
      form.querySelectorAll('[required]').forEach(campo => {
        campo.removeAttribute('required');
      });
    });
  }
</script>

<?php 
  include __DIR__ . "/../includes/footer.php"
?>
