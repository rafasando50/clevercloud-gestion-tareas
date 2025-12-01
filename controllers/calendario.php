<?php include __DIR__ . "/../includes/header.php"; ?>

<h2>Calendario de tareas</h2>
<p>Aquí podrás visualizar las tareas por fecha o mes.</p>

<section class="calendar-container">
  <iframe 
    src="https://calendar.google.com/calendar/embed?src=es.mexican%23holiday%40group.v.calendar.google.com&ctz=America%2FMexico_City"
    style="border: 0" width="100%" height="600" frameborder="0" scrolling="no">
  </iframe>
</section>

<?php 
  include __DIR__ . "/../includes/footer.php"
?>
