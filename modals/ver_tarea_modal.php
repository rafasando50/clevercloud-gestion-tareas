<?php
require __DIR__ . "/../models/conexion.php";

// Permitir que $id llegue por GET o por include (subtareas_agregar)
if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];
} elseif (isset($id)) { // viene de subtareas_agregar.php
    $id = (int) $id;
} else {
    $id = 0;
}

if ($id <= 0) {
    exit("Tarea no válida");
}

// ====== CARGAR TAREA ======
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
$stmt->close();

// ====== CARGAR SUBTAREAS ======
$subtareas = [];
$stmt_sub = $conn->prepare("
    SELECT id, titulo, completada, creado_en
    FROM subtareas
    WHERE tarea_id = ?
    ORDER BY id ASC
");
$stmt_sub->bind_param("i", $id);
$stmt_sub->execute();
$res_sub = $stmt_sub->get_result();
while ($row = $res_sub->fetch_assoc()) {
    $subtareas[] = $row;
}
$stmt_sub->close();
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

<hr>

<h3>Subtareas</h3>

<ul class="subtask-list" data-tarea-id="<?php echo $tarea['id']; ?>">
  <?php if (empty($subtareas)): ?>
    <li class="subtask-empty">No hay subtareas</li>
  <?php else: ?>
    <?php foreach ($subtareas as $s): ?>
      <li class="subtask-item <?php echo $s['completada'] ? 'subtask-completada' : ''; ?>"
          data-id="<?php echo $s['id']; ?>">
        <label>
          <input
            type="checkbox"
            class="subtask-toggle"
            data-id="<?php echo $s['id']; ?>"
            <?php echo $s['completada'] ? 'checked' : ''; ?>
          >
          <?php echo htmlspecialchars($s['titulo']); ?>
        </label>

        <!-- Botón para borrar la subtarea -->
        <button type="button"
                class="subtask-delete-btn"
                title="Eliminar subtarea">
          Borrar
        </button>
      </li>
    <?php endforeach; ?>
  <?php endif; ?>
</ul>


<form class="subtask-form" style="margin-top: 0.75rem;">
  <input type="hidden" name="tarea_id" value="<?php echo $tarea['id']; ?>">
  <input
    type="text"
    name="titulo"
    placeholder="Nueva subtarea..."
    required
  >
  <button type="submit" class="subtask-btn-primary">Agregar</button>
</form>

<hr>

<div class="btn-group" style="margin-top: 1rem;">
  <a href="editar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn-primary-editar">Editar</a>
</div>
