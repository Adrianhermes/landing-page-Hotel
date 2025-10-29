<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../config/conexao.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

// ID da reserva
$id = $_GET['id'] ?? null;
if (!$id) {
  die("ID da reserva não informado.");
}

// Busca a reserva
$stmt = $pdo->prepare("
  SELECT id, quarto_id, nome_cliente, email, cpf, telefone, data_checkin, data_checkout, status 
  FROM reservas 
  WHERE id = ?
");
$stmt->execute([$id]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$reserva) {
  die("Reserva não encontrada.");
}

// Busca quartos ativos
$quartos = $pdo->query("SELECT id, numero, tipo FROM quartos WHERE ativo = 1 ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);

// Lista de status válidos
$STATUS = ['confirmada', 'cancelada'];

// Função para escapar HTML
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<head>
  <link rel="stylesheet" href="/hotel/assets/css/style.css">
</head>

<div class="container">
  <h2>Editar Reserva #<?= (int)$reserva['id'] ?></h2>

  <form action="/hotel/controllers/atualizarReserva.php" method="POST" class="form-quarto">
    <input type="hidden" name="id" value="<?= (int)$reserva['id'] ?>">

    <!-- Quarto -->
    <div class="mb-3">
      <label for="quarto_id" class="form-label">Quarto:</label>
      <select class="form-control" id="quarto_id" name="quarto_id" required>
        <?php foreach ($quartos as $q): ?>
          <option value="<?= (int)$q['id'] ?>" <?= $q['id'] == $reserva['quarto_id'] ? 'selected' : '' ?>>
            Nº <?= h($q['numero']) ?> (<?= h($q['tipo']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Nome -->
    <div class="mb-3">
      <label for="nome_cliente" class="form-label">Nome Completo:</label>
      <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" value="<?= h($reserva['nome_cliente']) ?>" required>
    </div>

    <!-- E-mail -->
    <div class="mb-3">
      <label for="email" class="form-label">E-mail:</label>
      <input type="email" class="form-control" id="email" name="email" value="<?= h($reserva['email']) ?>" required>
    </div>

    <!-- CPF -->
    <div class="mb-3">
      <label for="cpf" class="form-label">CPF:</label>
      <input type="text" class="form-control" id="cpf" name="cpf" value="<?= h($reserva['cpf']) ?>" required
             pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" placeholder="000.000.000-00">
    </div>

    <!-- Telefone -->
    <div class="mb-3">
      <label for="telefone" class="form-label">Telefone:</label>
      <input type="text" class="form-control" id="telefone" name="telefone" value="<?= h($reserva['telefone']) ?>" required
             pattern="\(\d{2}\)\s?\d{4,5}-\d{4}" placeholder="(99) 99999-9999">
    </div>

    <!-- Datas -->
    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="data_checkin" class="form-label">Check-in:</label>
        <input type="date" class="form-control" id="data_checkin" name="data_checkin"
               value="<?= h($reserva['data_checkin']) ?>" required>
      </div>
      <div class="col-md-6 mb-3">
        <label for="data_checkout" class="form-label">Check-out:</label>
        <input type="date" class="form-control" id="data_checkout" name="data_checkout"
               value="<?= h($reserva['data_checkout']) ?>" required>
      </div>
    </div>

    <!-- Status -->
    <div class="mb-3">
      <label for="status" class="form-label">Status:</label>
      <select class="form-control" id="status" name="status" required>
        <?php foreach ($STATUS as $s): ?>
          <option value="<?= h($s) ?>" <?= $reserva['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Botões -->
    <div class="mb-3 d-flex gap-2">
      <button type="submit" class="btn btn-success">Salvar Alterações</button>
      <a href="/hotel/views/gerenciarReserva.php" class="btn btn-secondary">Voltar</a>
    </div>
  </form>
</div>

<script>
// Máscara CPF e telefone (igual na tela de cadastro)
document.addEventListener('DOMContentLoaded', function() {
  const tel = document.getElementById('telefone');
  const cpf = document.getElementById('cpf');

  tel.addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    if (v.length > 6) {
      e.target.value = `(${v.slice(0,2)}) ${v.slice(2,7)}-${v.slice(7)}`;
    } else if (v.length > 2) {
      e.target.value = `(${v.slice(0,2)}) ${v.slice(2)}`;
    } else if (v.length > 0) {
      e.target.value = `(${v}`;
    }
  });

  cpf.addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    if (v.length > 9) {
      e.target.value = `${v.slice(0,3)}.${v.slice(3,6)}.${v.slice(6,9)}-${v.slice(9,11)}`;
    } else if (v.length > 6) {
      e.target.value = `${v.slice(0,3)}.${v.slice(3,6)}.${v.slice(6)}`;
    } else if (v.length > 3) {
      e.target.value = `${v.slice(0,3)}.${v.slice(3)}`;
    } else {
      e.target.value = v;
    }
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
