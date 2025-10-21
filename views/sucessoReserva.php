<?php 

require_once __DIR__ . '/includes/header.php';

?>
<head>
  <link rel="stylesheet" href="/hotel/assets/css/style.css">
</head>


<div class="container-home" style="max-width: 500px; margin: 60px auto;">
  <div class="card" style="padding: 32px; text-align: center;">
    <h3 style="color: #2e7d32; margin-bottom: 24px;">Reserva cadastrado com sucesso!</h3>
    <div>
      <a href="/hotel/views/gerenciarReserva.php" class="btn btn-primary" style="margin-right: 8px;">Ver Reservas</a>
      <a href="/hotel/views/reservas.php" class="btn btn-success">Cadastrar Novo</a>
    </div>
  </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

