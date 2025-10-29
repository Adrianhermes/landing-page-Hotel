<?php
require_once __DIR__ . '/../../config/auth.php';
$usuarioAtual = usuarioLogado();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/hotel/assets/css/style.css">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet" />
    <title>Document</title>
</head>
<header>
<div class="navbar-custom">
    <div class="bannxer">
      <div class="container-navbar">
        <img src="/hotel/img/sharai.png" alt="sharai" class="logo-navbar" />
        <ul class="nav-links">
          <li><a href="/hotel/index.php">Home</a></li>
          <li><a href="/hotel/views/gerenciarReserva.php">Reservas</a></li>
          <li><a href="/hotel/views/gerenciarQuarto.php">Quartos</a></li>
          <li><a href="/hotel/views/telaGerenciamento.php">Painel</a></li>
          <?php if ($usuarioAtual): ?>
            <li><a href="/hotel/controllers/logout.php">Sair</a></li>
          <?php else: ?>
            <li><a href="/hotel/views/login.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>

</header>


</html>
