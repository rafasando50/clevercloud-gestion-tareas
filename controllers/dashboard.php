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
    <h3>
      Pendientes
        <span class="column-count" data-estado="pendiente">
      (<?php echo count($pendientes); ?>)
  </span>
    </h3>
    <ul class="task-list" data-estado="pendiente">
      <?php if (empty($pendientes)): ?>
        <li class="empty-msg">No hay tareas pendientes</li>
      <?php else: ?>
        <?php foreach ($pendientes as $t): ?>
          <li class="task-item" draggable="true" data-id="<?php echo $t['id']; ?>">
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
    <h3>
      En curso
        <span class="column-count" data-estado="en_curso">
      (<?php echo count($en_curso); ?>)
  </span>
    </h3>
    <ul class="task-list" data-estado="en_curso">
      <?php if (empty($en_curso)): ?>
        <li class="empty-msg">No hay tareas en curso</li>
      <?php else: ?>
        <?php foreach ($en_curso as $t): ?>
          <li class="task-item" draggable="true" data-id="<?php echo $t['id']; ?>">
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
    <h3>
      Terminadas
        <span class="column-count" data-estado="terminada">
      (<?php echo count($terminadas); ?>)
  </span>
    </h3>
    <ul class="task-list" data-estado="terminada">
      <?php if (empty($terminadas)): ?>
        <li class="empty-msg">No hay tareas terminadas</li>
      <?php else: ?>
        <?php foreach ($terminadas as $t): ?>
          <li class="task-item" draggable="true" data-id="<?php echo $t['id']; ?>">
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

  // ========================
  //      MODAL EXISTENTE
  // ========================

  document.querySelectorAll('.task-link').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const id = this.dataset.id;

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

  modalClose.addEventListener('click', () => {
    modalOverlay.style.display = 'none';
  });

  modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) {
      modalOverlay.style.display = 'none';
    }
  });

  // ========================
  //        DRAG & DROP
  // ========================

  let draggedItem = null;

  function getEmptyText(estado) {
    switch (estado) {
      case 'pendiente': return 'No hay tareas pendientes';
      case 'en_curso':  return 'No hay tareas en curso';
      case 'terminada': return 'No hay tareas terminadas';
      default:          return 'Sin tareas';
    }
  }

  function actualizarMensajeVacio(lista) {
    const tieneTareas = lista.querySelector('.task-item') !== null;
    let empty = lista.querySelector('.empty-msg');

    if (tieneTareas) {
      if (empty) empty.remove();
    } else {
      if (!empty) {
        empty = document.createElement('li');
        empty.classList.add('empty-msg');
        empty.textContent = getEmptyText(lista.dataset.estado);
        lista.appendChild(empty);
      }
    }
  }

  // Hacer drag a cada tarea
  document.querySelectorAll('.task-item').forEach(item => {
    item.addEventListener('dragstart', e => {
      draggedItem = item;
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', item.dataset.id);
    });

    item.addEventListener('dragend', () => {
      draggedItem = null;
    });
  });

  // Hacer drop en las listas (aunque estén vacías)
  document.querySelectorAll('.task-list').forEach(lista => {

    // Aseguramos que el mensaje vacío esté correcto al inicio
    actualizarMensajeVacio(lista);

    lista.addEventListener('dragover', e => {
      e.preventDefault();
    });

    lista.addEventListener('drop', e => {
      e.preventDefault();
      if (!draggedItem) return;

      const listaOrigen  = draggedItem.closest('.task-list');
      const listaDestino = lista;

      // Mover visualmente
      listaDestino.appendChild(draggedItem);

      const id          = draggedItem.dataset.id;
      const nuevoEstado = listaDestino.dataset.estado;

      // Actualizar mensajes vacíos
      actualizarMensajeVacio(listaOrigen);
      actualizarMensajeVacio(listaDestino);

      // Llamar al backend
const estadoAnterior = listaOrigen.dataset.estado;

fetch('../controllers/actualizar_estado_tarea.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded'
  },
  body: 'id=' + encodeURIComponent(id) +
        '&estado=' + encodeURIComponent(nuevoEstado)
})
.then(r => r.text().then(text => ({ ok: r.ok, status: r.status, text })))
.then(res => {
  console.log('Respuesta actualizar_estado:', res);

  if (!res.ok || res.text.trim() !== 'ok') {
    // Algo falló -> revertimos el cambio visual
    alert('No se pudo actualizar la tarea: ' + res.text);

    listaOrigen.appendChild(draggedItem);
    actualizarMensajeVacio(listaOrigen);
    actualizarMensajeVacio(listaDestino);
  }
})
.catch(err => {
  console.error('Error en fetch:', err);
  alert('Error de red al actualizar la tarea');

  // Revertir en caso de error de red
  listaOrigen.appendChild(draggedItem);
  actualizarMensajeVacio(listaOrigen);
  actualizarMensajeVacio(listaDestino);
});

    });
  });

    // ========================
  //     SUBTAREAS - TOGGLE
  // ========================

  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('subtask-toggle')) {
      const checkbox = e.target;
      const id = checkbox.dataset.id;
      const completada = checkbox.checked ? '1' : '0';

      fetch('../controllers/subtareas_toggle.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + encodeURIComponent(id) +
              '&completada=' + encodeURIComponent(completada)
      })
      .then(r => r.text().then(text => ({ ok: r.ok, status: r.status, text })))
      .then(res => {
        console.log('Toggle subtarea:', res);
        if (!res.ok || res.text.trim() !== 'ok') {
          alert('No se pudo actualizar la subtarea: ' + res.text);
          // Revertir visualmente
          checkbox.checked = !checkbox.checked;
        } else {
          const li = checkbox.closest('.subtask-item');
          if (li) {
            li.classList.toggle('subtask-completada', checkbox.checked);
          }
        }
      })
      .catch(err => {
        console.error('Error en toggle subtarea:', err);
        alert('Error de red al actualizar la subtarea');
        checkbox.checked = !checkbox.checked;
      });
    }
  });

  // ========================
  //   SUBTAREAS - AGREGAR
  // ========================

  document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('subtask-form')) {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);

      fetch('../controllers/subtareas_agregar.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.text())
      .then(html => {
        // Volvemos a pintar el contenido del modal completo
        modalContent.innerHTML = html;
      })
      .catch(err => {
        console.error('Error al agregar subtarea:', err);
        alert('No se pudo agregar la subtarea');
      });
    }
  });

// ========================
//   SUBTAREAS - ELIMINAR
// ========================

document.addEventListener('click', function(e) {
    const deleteBtn = e.target.closest('.subtask-delete-btn');
    if (!deleteBtn) return;

    const li = deleteBtn.closest('.subtask-item');
    if (!li) return;

    const id = li.dataset.id;
    if (!id) return;

    // Enviar directamente sin confirmar
    fetch('../controllers/subtareas_eliminar.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'id=' + encodeURIComponent(id)
    })
    .then(r => r.text().then(text => ({ ok: r.ok, status: r.status, text })))
    .then(res => {
      console.log('Eliminar subtarea:', res);

      if (!res.ok || res.text.trim() !== 'ok') {
        alert('No se pudo eliminar la subtarea: ' + res.text);
      } else {
        const ul = li.closest('.subtask-list');
        li.remove();

        // Si ya no hay subtareas, mostramos "No hay subtareas"
        if (ul && ul.querySelectorAll('.subtask-item').length === 0) {
          let empty = ul.querySelector('.subtask-empty');
          if (!empty) {
            empty = document.createElement('li');
            empty.classList.add('subtask-empty');
            empty.textContent = 'No hay subtareas';
            ul.appendChild(empty);
          }
        }
      }
    })
    .catch(err => {
      console.error('Error al eliminar subtarea:', err);
      alert('Error de red al eliminar la subtarea');
    });
});



</script>


<?php 
  include __DIR__ . "/../includes/footer.php"
?>
