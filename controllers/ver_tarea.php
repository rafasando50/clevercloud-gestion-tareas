<?php
require __DIR__ . "/../models/conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT id, titulo, descripcion, fecha_inicio, fecha_fin, estado, creado_en, actualizado_en
    FROM tareas
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$tarea = $result->fetch_assoc();

if (!$tarea) {
    header("Location: dashboard.php");
    exit;
}

include __DIR__ . "/../includes/header.php";
?>

<div class="form-container">
  <div class="form-box">
    <div class="form-header">
      <h2>Detalle de tarea</h2>
      <a href="dashboard.php" class="close-btn">&times;</a>
    </div>

    <p><strong>ID:</strong> <?php echo $tarea["id"]; ?></p>

    <p><strong>Título:</strong><br>
      <?php echo htmlspecialchars($tarea["titulo"]); ?>
    </p>

    <p><strong>Descripción:</strong><br>
      <?php echo nl2br(htmlspecialchars($tarea["descripcion"] ?? "Sin descripción")); ?>
    </p>

    <p><strong>Fecha de inicio:</strong>
      <?php echo $tarea["fecha_inicio"] ?: "No definida"; ?>
    </p>

    <p><strong>Fecha de fin:</strong>
      <?php echo $tarea["fecha_fin"] ?: "No definida"; ?>
    </p>

    <p><strong>Estado:</strong>
      <?php echo ucfirst(str_replace("_", " ", $tarea["estado"])); ?>
    </p>

    <p><strong>Creado en:</strong>
      <?php echo $tarea["creado_en"]; ?>
    </p>

    <p><strong>Última actualización:</strong>
      <?php echo $tarea["actualizado_en"]; ?>
    </p>

    <div class="btn-group" style="margin-top: 1rem;">
      <a href="editar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn-primary">Editar</a>
      <a href="dashboard.php" class="btn-primary">Volver</a>
    </div>
  </div>
</div>

<?php 
  include __DIR__ . "/../includes/footer.php"
?>
