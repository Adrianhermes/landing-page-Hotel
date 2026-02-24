<?php
require_once __DIR__ . '/../config/auth.php';

if (usuarioLogado()) {
    header('Location: /landing-page-Hotel-principal/views/gerenciarReserva.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';

$flashSuccess = $_GET['m'] ?? null;
$flashError = $_GET['e'] ?? null;
?>

<head>
  <link rel="stylesheet" href="/landing-page-Hotel-principal/assets/css/style.css">
</head>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="text-center mb-4">Acesso Administrativo</h3>

          <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success py-2"><?= htmlspecialchars($flashSuccess) ?></div>
          <?php endif; ?>

          <?php if (!empty($flashError)): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($flashError) ?></div>
          <?php endif; ?>

          <form action="/landing-page-Hotel-principal/controllers/processarLogin.php" method="POST" novalidate>
            <div class="mb-3">
              <label for="email" class="form-label">E-mail</label>
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="admin@hotel.com"
                value="admin@essentia.com"
                required
              >
            </div>
            <div class="mb-3">
              <label for="senha" class="form-label">Senha</label>
              <input
                type="text"
                class="form-control"
                id="senha"
                name="senha"
                placeholder="Sua senha"
                value="admin"
                required
                minlength="5"
              >
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
