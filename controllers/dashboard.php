<?php
require __DIR__ . "/../models/conexion.php";

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

include __DIR__ . "/../includes/header.php";
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
            <a href="#"
              class="task-link"
              data-id="<?php echo $t['id']; ?>">
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
            <a href="#"
              class="task-link"
              data-id="<?php echo $t['id']; ?>">
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
              <a href="#"
                class="task-link"
                data-id="<?php echo $t['id']; ?>">
                <?php echo htmlspecialchars($t['titulo']); ?>
              </a>
            </li>
          <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>
</section>

<div class="btn-container">
  <a href="agregar_tarea.php" class="btn-primary">Nueva tarea</a>
</div>

<div id="task-modal-overlay" class="modal-overlay" style="display:none;">
  <div class="modal-box">
    <button id="modal-close" class="modal-close-btn">&times;</button>
    <div id="task-modal-content">
      <!-- Aquí se cargará la info de la tarea -->
    </div>
  </div>
</div>

<script>
  const modalOverlay = document.getElementById('task-modal-overlay');
  const modalContent = document.getElementById('task-modal-content');
  const modalClose   = document.getElementById('modal-close');

  // Abrir modal al hacer clic en una tarea
  document.querySelectorAll('.task-link').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const id = this.dataset.id;

      // Cargar contenido vía AJAX
      fetch('../modals/ver_tarea_modal.php?id=' + id)
        .then(response => response.text())
        .then(html => {
          modalContent.innerHTML = html;
          modalOverlay.style.display = 'flex';
        })
        .catch(err => {
          modalContent.innerHTML = '<p>Error al cargar la tarea.</p>';
          modalOverlay.style.display = 'flex';
        });
    });
  });

  // Cerrar modal al hacer clic en la X
  modalClose.addEventListener('click', () => {
    modalOverlay.style.display = 'none';
  });

  // Cerrar modal al hacer clic fuera de la caja
  modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) {
      modalOverlay.style.display = 'none';
    }
  });
</script>

<?php 
  include __DIR__ . "/../includes/footer.php"
?>
