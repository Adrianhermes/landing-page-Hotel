<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  die('ID do usuario nao informado.');
}

$conexao = new Conexao();
$pdo = $conexao->getPdo();

$stmt = $pdo->prepare('SELECT id, nome, email, criado_em FROM usuarios_admin WHERE id = :id');
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
  die('Usuario nao encontrado.');
}

function h_usuario($valor) {
  return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$flashErro = $_GET['e'] ?? null;
?>
<head>
  <link rel="stylesheet" href="/landing-page-Hotel-principal/assets/css/style.css">
</head>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Editar usuario</h2>
    <a href="/landing-page-Hotel-principal/views/telaGerenciamento.php" class="btn btn-secondary">Voltar</a>
  </div>

  <?php if (!empty($flashErro)): ?>
    <div class="alert alert-danger py-2"><?= htmlspecialchars($flashErro) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form action="/landing-page-Hotel-principal/controllers/atualizarUsuario.php" method="POST" class="row g-3">
        <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">

        <div class="col-md-6">
          <label for="nome" class="form-label">Nome completo</label>
          <input type="text" class="form-control" id="nome" name="nome" value="<?= h_usuario($usuario['nome']) ?>" required>
        </div>

        <div class="col-md-6">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="<?= h_usuario($usuario['email']) ?>" required>
        </div>

        <div class="col-md-6">
          <label for="senha" class="form-label">Nova senha</label>
          <input type="password" class="form-control" id="senha" name="senha" placeholder="Deixe em branco para manter a senha atual" minlength="6">
        </div>

        <div class="col-md-6">
          <label for="confirmar_senha" class="form-label">Confirmar nova senha</label>
          <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" minlength="6" placeholder="Repita a nova senha">
        </div>

        <div class="col-12">
          <p class="text-muted mb-2">Criado em: <?= h_usuario($usuario['criado_em'] ?? '-') ?></p>
        </div>

        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Salvar alteracoes</button>
          <a href="/landing-page-Hotel-principal/views/telaGerenciamento.php" class="btn btn-outline-secondary">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
