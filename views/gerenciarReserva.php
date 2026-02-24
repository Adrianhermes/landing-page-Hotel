<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../config/conexao.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

// Consulta reservas com o nome do quarto
$sql = "
  SELECT 
    r.id, 
    r.nome_cliente, 
    r.email, 
    r.cpf, 
    r.telefone, 
    r.data_checkin, 
    r.data_checkout, 
    r.status, 
    q.numero AS quarto_numero,
    q.tipo AS quarto_tipo
  FROM reservas r
  JOIN quartos q ON q.id = r.quarto_id
  ORDER BY r.id DESC
";
$reservas = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Flash (mensagem ?m=)
$flash = $_GET['m'] ?? null;
?>
<head>
  <link rel="stylesheet" href="/landing-page-Hotel-principal/assets/css/style.css">
</head>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Gerenciar Reservas</h2>
    <div class="d-flex gap-2">
      <a href="/landing-page-Hotel-principal/views/reservas.php" class="btn btn-success">Nova Reserva</a>
    </div>
  </div>

  <?php if (!empty($flash)): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <section class="reservas-filtros-card" aria-label="Filtros de reserva">
    <div class="reservas-filtros-grid">
      <div class="reservas-filtro-campo reservas-filtro-busca">
        <label for="filtro-reserva-busca" class="reservas-filtro-label">Buscar</label>
        <div class="reservas-filtro-input">
          <input
            type="text"
            id="filtro-reserva-busca"
            class="reservas-input"
            placeholder="Nome, email, CPF ou quarto"
            autocomplete="off">
        </div>
      </div>
      <div class="reservas-filtro-campo">
        <label for="filtro-reserva-status" class="reservas-filtro-label">Status</label>
        <div class="reservas-filtro-input">
          <select id="filtro-reserva-status" class="reservas-input">
            <option value="">Todos</option>
            <option value="confirmada">Confirmada</option>
            <option value="cancelada">Cancelada</option>
          </select>
        </div>
      </div>
      <div class="reservas-filtro-campo">
        <label for="filtro-reserva-checkin" class="reservas-filtro-label">Check-in a partir de</label>
        <div class="reservas-filtro-input">
          <input type="date" id="filtro-reserva-checkin" class="reservas-input">
        </div>
      </div>
      <div class="reservas-filtro-campo">
        <label for="filtro-reserva-checkout" class="reservas-filtro-label">Check-out até</label>
        <div class="reservas-filtro-input">
          <input type="date" id="filtro-reserva-checkout" class="reservas-input">
        </div>
      </div>
      <div class="reservas-filtro-campo reservas-filtro-acoes">
        <button type="button" class="btn btn-link reservas-filtro-limpar" id="filtro-reserva-limpar">
          Limpar filtros
        </button>
      </div>
    </div>
  </section>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0 align-middle reservas-tabela" data-reservas-table>
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Quarto</th>
              <th>Cliente</th>
              <th>CPF</th>
              <th>E-mail</th>
              <th>Telefone</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($reservas)): ?>
            <tr>
              <td colspan="10" class="text-center py-4">Nenhuma reserva encontrada.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($reservas as $r): ?>
              <?php
                $status = strtolower($r['status']);
                $badge = 'secondary';
                if ($status === 'confirmada') {
                  $badge = 'success';
                } elseif ($status === 'cancelada') {
                  $badge = 'danger';
                }
                $checkinIso = date('Y-m-d', strtotime($r['data_checkin']));
                $checkoutIso = date('Y-m-d', strtotime($r['data_checkout']));
                $termosBusca = strtolower(
                  $r['nome_cliente'] . ' ' .
                  $r['email'] . ' ' .
                  $r['cpf'] . ' ' .
                  $r['telefone'] . ' ' .
                  'quarto ' . $r['quarto_numero'] . ' ' .
                  $r['quarto_tipo']
                );
              ?>
              <tr
                class="reserva-linha"
                data-reserva-linha
                data-status="<?= htmlspecialchars($status) ?>"
                data-checkin="<?= htmlspecialchars($checkinIso) ?>"
                data-checkout="<?= htmlspecialchars($checkoutIso) ?>"
                data-termos="<?= htmlspecialchars($termosBusca) ?>"
              >
                <td><?= (int)$r['id'] ?></td>
                <td>
                  N&ordm; <?= htmlspecialchars($r['quarto_numero']) ?><br>
                  <small><?= htmlspecialchars($r['quarto_tipo']) ?></small>
                </td>
                <td><?= htmlspecialchars($r['nome_cliente']) ?></td>
                <td><?= htmlspecialchars($r['cpf']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td><?= htmlspecialchars($r['telefone']) ?></td>
                <td><?= date('d/m/Y', strtotime($r['data_checkin'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['data_checkout'])) ?></td>
                <td>
                  <span class="badge bg-<?= $badge ?>"><?= ucfirst($status) ?></span>
                </td>
                <td>
                  <a href="/landing-page-Hotel-principal/views/updateReserva.php?id=<?= (int)$r['id'] ?>" class="btn btn-primary btn-sm">Editar</a>

                  <form action="/landing-page-Hotel-principal/controllers/excluirReserva.php" method="POST" class="d-inline"
                        onsubmit="return confirm('Tem certeza que deseja excluir esta reserva?');">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <tr class="reserva-vazia" data-reservas-vazio style="display: none;">
              <td colspan="10" class="text-center py-4">
                Nenhuma reserva corresponde aos filtros aplicados.
              </td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
(function() {
  var tabela = document.querySelector('[data-reservas-table]');
  if (!tabela) {
    return;
  }

  var campoBusca = document.getElementById('filtro-reserva-busca');
  var campoStatus = document.getElementById('filtro-reserva-status');
  var campoCheckin = document.getElementById('filtro-reserva-checkin');
  var campoCheckout = document.getElementById('filtro-reserva-checkout');
  var botaoLimpar = document.getElementById('filtro-reserva-limpar');
  var linhas = Array.prototype.slice.call(tabela.querySelectorAll('[data-reserva-linha]'));
  var linhaVazia = tabela.querySelector('[data-reservas-vazio]');

  function normalizar(valor) {
    if (!valor) {
      return '';
    }
    if (typeof valor.normalize === 'function') {
      valor = valor.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }
    return valor.toLowerCase();
  }

  var dadosLinhas = linhas.map(function(linha) {
    return {
      linha: linha,
      termos: normalizar(linha.getAttribute('data-termos') || ''),
      status: (linha.getAttribute('data-status') || '').toLowerCase(),
      checkin: linha.getAttribute('data-checkin') || '',
      checkout: linha.getAttribute('data-checkout') || ''
    };
  });

  function filtrar() {
    var termoBusca = normalizar(campoBusca && campoBusca.value);
    var statusDesejado = (campoStatus && campoStatus.value) ? campoStatus.value.toLowerCase() : '';
    var checkinMin = campoCheckin && campoCheckin.value ? campoCheckin.value : '';
    var checkoutMax = campoCheckout && campoCheckout.value ? campoCheckout.value : '';
    var linhasVisiveis = 0;

    dadosLinhas.forEach(function(item) {
      var visivel = true;

      if (termoBusca && item.termos.indexOf(termoBusca) === -1) {
        visivel = false;
      }

      if (visivel && statusDesejado && item.status !== statusDesejado) {
        visivel = false;
      }

      if (visivel && checkinMin && item.checkin && item.checkin < checkinMin) {
        visivel = false;
      }

      if (visivel && checkoutMax && item.checkout && item.checkout > checkoutMax) {
        visivel = false;
      }

      if (visivel) {
        item.linha.style.display = '';
        linhasVisiveis++;
      } else {
        item.linha.style.display = 'none';
      }
    });

    if (linhaVazia) {
      linhaVazia.style.display = linhasVisiveis === 0 ? '' : 'none';
    }
  }

  function limpar() {
    if (campoBusca) campoBusca.value = '';
    if (campoStatus) campoStatus.value = '';
    if (campoCheckin) campoCheckin.value = '';
    if (campoCheckout) campoCheckout.value = '';
    filtrar();
  }

  if (campoBusca) {
    campoBusca.addEventListener('input', filtrar);
  }
  if (campoStatus) {
    campoStatus.addEventListener('change', filtrar);
  }
  if (campoCheckin) {
    campoCheckin.addEventListener('change', filtrar);
  }
  if (campoCheckout) {
    campoCheckout.addEventListener('change', filtrar);
  }
  if (botaoLimpar) {
    botaoLimpar.addEventListener('click', limpar);
  }
})();
</script>
