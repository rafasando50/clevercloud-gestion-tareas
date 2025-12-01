<?php
require "conexion.php";

$pendientes  = [];
$en_curso    = [];
$terminadas  = [];

// Traer todas las tareas y separarlas por estado
$sql = "SELECT id, titulo, estado FROM tareas ORDER BY fecha_inicio IS NULL, fecha_inicio ASC, id ASC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        switch ($row["estado"]) {
            case "en_curso":
                $en_curso[] = $row;
                break;
            case "terminada":
                $terminadas[] = $row;
                break;
            default:
                $pendientes[] = $row;
                break;
        }
    }
}

$total_tareas   = count($pendientes) + count($en_curso) + count($terminadas);
$completadas    = count($terminadas);
$nuevas         = $total_tareas; // puedes cambiar esta lógica si quieres

include("includes/header.php");
?>

<section class="stats">
  <div class="stat-box">
    <h4>Task Completed</h4>
    <p><?php echo str_pad($completadas, 2, "0", STR_PAD_LEFT); ?></p>
    <span>Total terminadas</span>
  </div>
  <div class="stat-box">
    <h4>New Task</h4>
    <p><?php echo str_pad($nuevas, 2, "0", STR_PAD_LEFT); ?></p>
    <span>Registradas en el sistema</span>
  </div>
  <div class="stat-box">
    <h4>Project Done</h4>
    <p><?php echo str_pad($total_tareas > 0 ? 1 : 0, 2, "0", STR_PAD_LEFT); ?></p>
    <span>Proyecto de tareas</span>
  </div>
</section>

<section class="boards">
  <div class="board">
    <h3>Pendientes</h3>
    <ul>
      <?php if (empty($pendientes)): ?>
        <li>No hay tareas pendientes</li>
      <?php else: ?>
        <?php foreach ($pendientes as $t): ?>
          <li>
            <a href="ver_tarea.php?id=<?php echo $t['id']; ?>">
              <?php echo htmlspecialchars($t['titulo']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>

  <div class="board">
    <h3>En curso</h3>
    <ul>
      <?php if (empty($en_curso)): ?>
        <li>No hay tareas en curso</li>
      <?php else: ?>
        <?php foreach ($en_curso as $t): ?>
          <li>
            <a href="ver_tarea.php?id=<?php echo $t['id']; ?>">
              <?php echo htmlspecialchars($t['titulo']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>

  <div class="board">
    <h3>Terminadas</h3>
    <ul>
      <?php if (empty($terminadas)): ?>
        <li>No hay tareas terminadas</li>
      <?php else: ?>
        <?php foreach ($terminadas as $t): ?>
          <li>
            <a href="ver_tarea.php?id=<?php echo $t['id']; ?>">
              <?php echo htmlspecialchars($t['titulo']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>
</section>

<div class="btn-container">
  <a href="agregar_tarea.php" class="btn-primary">+ Nueva tarea</a>
</div>

<?php include("includes/footer.php"); ?>
