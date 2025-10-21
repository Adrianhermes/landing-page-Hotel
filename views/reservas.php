<?php 
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../config/conexao.php';

// carrega quartos ativos para o <select>
$conexao = new Conexao();
$pdo = $conexao->getPdo();
$quartos = $pdo->query("SELECT id, numero, tipo FROM quartos WHERE ativo = 1 ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);
?>
<head>
  <link rel="stylesheet" href="/hotel/assets/css/style.css">
</head>

<div class="container">
  <h2>Reservas</h2>

  <form action="/hotel/controllers/processarReserva.php" method="POST" class="form-quarto" id="form-reserva">

    <!-- Quarto -->
    <div class="mb-3">
      <label for="quarto_id" class="form-label">Quarto:</label>
      <select class="form-control" id="quarto_id" name="quarto_id" required>
        <option value="">Selecione um quarto...</option>
        <?php foreach ($quartos as $q): ?>
          <option value="<?= (int)$q['id'] ?>">Nº <?= htmlspecialchars($q['numero']) ?> (<?= htmlspecialchars($q['tipo']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Nome Completo -->
    <div class="mb-3">
      <label for="nome_cliente" class="form-label">Nome Completo:</label>
      <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" required>
    </div>

    <!-- E-mail -->
    <div class="mb-3">
      <label for="email" class="form-label">E-mail:</label>
      <input type="email" class="form-control" id="email" name="email" required>
    </div>

    <!-- CPF -->
    <div class="mb-3">
      <label for="cpf" class="form-label">CPF:</label>
      <input type="text" class="form-control" id="cpf" name="cpf" required
             pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" placeholder="000.000.000-00">
    </div>

    <!-- Telefone -->
    <div class="mb-3">
      <label for="telefone" class="form-label">Telefone:</label>
      <input type="text" class="form-control" id="telefone" name="telefone" required
             pattern="\(\d{2}\)\s?\d{4,5}-\d{4}" placeholder="(99) 99999-9999">
    </div>

    <!-- Datas -->
    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="data_checkin" class="form-label">Check-in:</label>
        <input type="date" class="form-control" id="data_checkin" name="data_checkin" required>
      </div>
      <div class="col-md-6 mb-3">
        <label for="data_checkout" class="form-label">Check-out:</label>
        <input type="date" class="form-control" id="data_checkout" name="data_checkout" required>
      </div>
    </div>

    <!-- Status -->
    <div class="mb-3">
      <label for="status" class="form-label">Status:</label>
      <select class="form-control" id="status" name="status" required>
        <option value="confirmada">Confirmada</option>
        <option value="cancelada">Cancelada</option>
      </select>
    </div>

    <!-- Botões -->
    <div class="mb-3" style="display: flex; gap: 10px;">
      <button type="submit" class="btn btn-success">Cadastrar Reserva</button>
      <a href="/hotel/views/gerenciarReserva.php" class="btn btn-primary">Ver Reservas</a>
      <button type="button" class="btn btn-secondary" onclick="history.back()">Voltar</button>
    </div>

  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Máscara telefone
  const tel = document.getElementById('telefone');
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

  // Máscara CPF
  const cpf = document.getElementById('cpf');
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

  // Nome: capitaliza primeira letra de cada palavra
  const nome = document.getElementById('nome_cliente');
  nome.addEventListener('input', function(e) {
    e.target.value = e.target.value.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
  });

  // E-mail: sempre minúsculo
  const email = document.getElementById('email');
  email.addEventListener('input', function(e) {
    e.target.value = e.target.value.toLowerCase();
  });

  // Validação simples de datas: check-in < check-out
  const form = document.getElementById('form-reserva');
  const ckIn = document.getElementById('data_checkin');
  const ckOut = document.getElementById('data_checkout');

  function validaDatas() {
    const d1 = ckIn.value;
    const d2 = ckOut.value;
    ckIn.setCustomValidity('');
    ckOut.setCustomValidity('');
    if (d1 && d2 && d1 >= d2) {
      ckOut.setCustomValidity('O check-out deve ser após o check-in.');
    }
  }
  ckIn.addEventListener('change', validaDatas);
  ckOut.addEventListener('change', validaDatas);

  form.addEventListener('submit', function(e) {
    validaDatas();
    if (!form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
