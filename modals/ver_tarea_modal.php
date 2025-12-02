<?php
require __DIR__ . "/../models/conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    exit("Tarea no válida");
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
    exit("Tarea no encontrada");
}
?>

<h2><?php echo htmlspecialchars($tarea["titulo"]); ?></h2>

<p><strong>ID:</strong> <?php echo $tarea["id"]; ?></p>

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
  <a href="editar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn-primary-editar">Editar</a>
</div>

<form method="POST" action="../controllers/actualizar_estado_tarea.php" class="btn-group">
  <input type="hidden" name="id" value="<?php echo $tarea['id']; ?>">

  <?php if ($tarea['estado'] === 'pendiente'): ?>
    <button type="submit" name="estado" value="en_curso" class="btn-primary">
      Mover a "En curso"
    </button>
    <button type="submit" name="estado" value="terminada" class="btn-primary">
      Marcar como "Terminada"
    </button>

  <?php elseif ($tarea['estado'] === 'en_curso'): ?>
    <button type="submit" name="estado" value="pendiente" class="btn-primary">
      Mover a "Pendientes"
    </button>
    <button type="submit" name="estado" value="terminada" class="btn-primary">
      Marcar como "Terminada"
    </button>

  <?php elseif ($tarea['estado'] === 'terminada'): ?>
    <button type="submit" name="estado" value="en_curso" class="btn-primary">
      Mover a "En curso"
    </button>
    <button type="submit" name="estado" value="pendiente" class="btn-primary">
      Mover a "Pendientes"
    </button>
  <?php endif; ?>
</form>

