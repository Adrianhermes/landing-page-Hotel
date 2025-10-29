<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../config/conexao.php';

$flash = $_GET['m'] ?? null;

$conexao = new Conexao();
$pdo = $conexao->getPdo();

$stmt = $pdo->query("
  SELECT id, numero, tipo, preco, descricao, ativo AS status
  FROM quartos
  ORDER BY id ASC
");
$quartos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$tiposDisponiveis = [];

if (!empty($quartos)) {
    $tiposDisponiveis = array_map(function (array $quarto) {
        return isset($quarto['tipo']) ? trim((string) $quarto['tipo']) : '';
    }, $quartos);
    $tiposDisponiveis = array_values(array_filter(array_unique($tiposDisponiveis)));
    if (!empty($tiposDisponiveis)) {
        sort($tiposDisponiveis, SORT_NATURAL | SORT_FLAG_CASE);
    }
}
?>
<head>
  <link rel="stylesheet" href="/hotel/assets/css/style.css">
</head>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Gerenciar Quartos</h2>
    <div class="d-flex gap-2">
      <a href="/hotel/views/quarto.php" class="btn btn-success">Novo Quarto</a>
    </div>
  </div>

  <?php if ($flash): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <section class="quartos-filtros-card" aria-label="Filtros de quartos">
    <div class="quartos-filtros-grid">
      <div class="quartos-filtro-campo quartos-filtro-busca">
        <label for="filtro-quarto-busca" class="quartos-filtro-label">Buscar</label>
        <div class="quartos-filtro-input">
          <input
            type="text"
            id="filtro-quarto-busca"
            class="quartos-input"
            placeholder="Numero, tipo ou descricao"
            autocomplete="off">
        </div>
      </div>
      <div class="quartos-filtro-campo">
        <label for="filtro-quarto-status" class="quartos-filtro-label">Status</label>
        <div class="quartos-filtro-input">
          <select id="filtro-quarto-status" class="quartos-input">
            <option value="">Todos</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
          </select>
        </div>
      </div>
      <div class="quartos-filtro-campo">
        <label for="filtro-quarto-tipo" class="quartos-filtro-label">Tipo</label>
        <div class="quartos-filtro-input">
          <select id="filtro-quarto-tipo" class="quartos-input">
            <option value="">Todos</option>
            <?php foreach ($tiposDisponiveis as $tipoDisponivel): ?>
              <option value="<?= htmlspecialchars($tipoDisponivel) ?>"><?= htmlspecialchars($tipoDisponivel) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="quartos-filtro-campo">
        <label for="filtro-quarto-preco-min" class="quartos-filtro-label">Preco minimo</label>
        <div class="quartos-filtro-input">
          <input type="number" id="filtro-quarto-preco-min" class="quartos-input" min="0" step="0.01" placeholder="0,00">
        </div>
      </div>
      <div class="quartos-filtro-campo">
        <label for="filtro-quarto-preco-max" class="quartos-filtro-label">Preco maximo</label>
        <div class="quartos-filtro-input">
          <input type="number" id="filtro-quarto-preco-max" class="quartos-input" min="0" step="0.01" placeholder="0,00">
        </div>
      </div>
      <div class="quartos-filtro-campo quartos-filtro-acoes">
        <button type="button" class="btn btn-link quartos-filtro-limpar" id="filtro-quarto-limpar">
          Limpar filtros
        </button>
      </div>
    </div>
  </section>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0 align-middle quartos-tabela" data-quartos-table>
          <thead class="table-light">
            <tr>
              <th style="width:80px;">ID</th>
              <th>Numero</th>
              <th>Tipo</th>
              <th>Preco</th>
              <th>Status</th>
              <th style="width:160px;">Acoes</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($quartos)): ?>
            <tr>
              <td colspan="6" class="text-center py-4">Nenhum quarto cadastrado.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($quartos as $q): ?>
              <?php
                $ativo = is_numeric($q['status']) ? ((int) $q['status'] === 1) : (mb_strtolower((string) $q['status']) === 'ativo');
                $statusSlug = $ativo ? 'ativo' : 'inativo';
                $precoNumerico = isset($q['preco']) ? (float) $q['preco'] : 0.0;
                $termosBusca = mb_strtolower(
                  implode(' ', [
                    $q['numero'] ?? '',
                    $q['tipo'] ?? '',
                    $q['descricao'] ?? '',
                  ]),
                  'UTF-8'
                );
                $tipoSlug = mb_strtolower(trim((string) ($q['tipo'] ?? '')), 'UTF-8');
              ?>
              <tr
                class="quarto-linha"
                data-quarto-linha
                data-status="<?= htmlspecialchars($statusSlug) ?>"
                data-tipo="<?= htmlspecialchars($tipoSlug) ?>"
                data-preco="<?= htmlspecialchars(number_format($precoNumerico, 2, '.', '')) ?>"
                data-termos="<?= htmlspecialchars($termosBusca) ?>"
              >
                <td><?= (int) $q['id'] ?></td>
                <td><?= htmlspecialchars($q['numero']) ?></td>
                <td><?= htmlspecialchars($q['tipo']) ?></td>
                <td><?= 'R$ ' . number_format((float) $q['preco'], 2, ',', '.') ?></td>
                <td>
                  <span class="badge <?= $ativo ? 'bg-success' : 'bg-secondary' ?>">
                    <?= $ativo ? 'Ativo' : 'Inativo' ?>
                  </span>
                </td>
                <td>
                  <div class="quartos-acoes">
                    <a href="/hotel/views/updateQuarto.php?id=<?= (int) $q['id'] ?>"
                       class="btn quartos-btn quartos-btn-editar">Editar</a>

                    <form action="/hotel/controllers/excluirQuarto.php"
                          method="POST"
                          class="quartos-form-excluir"
                          onsubmit="return confirm('Tem certeza que deseja excluir este quarto?');">
                      <input type="hidden" name="id" value="<?= (int) $q['id'] ?>">
                      <button type="submit" class="btn quartos-btn quartos-btn-excluir">Excluir</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <tr class="quarto-vazio" data-quartos-vazio style="display: none;">
              <td colspan="6" class="text-center py-4">Nenhum quarto corresponde aos filtros aplicados.</td>
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
  var tabela = document.querySelector('[data-quartos-table]');
  if (!tabela) {
    return;
  }

  var campoBusca = document.getElementById('filtro-quarto-busca');
  var campoStatus = document.getElementById('filtro-quarto-status');
  var campoTipo = document.getElementById('filtro-quarto-tipo');
  var campoPrecoMin = document.getElementById('filtro-quarto-preco-min');
  var campoPrecoMax = document.getElementById('filtro-quarto-preco-max');
  var botaoLimpar = document.getElementById('filtro-quarto-limpar');
  var linhas = Array.prototype.slice.call(tabela.querySelectorAll('[data-quarto-linha]'));
  var linhaVazia = tabela.querySelector('[data-quartos-vazio]');

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
    var precoTexto = linha.getAttribute('data-preco') || '0';
    var precoNumero = parseFloat(precoTexto);
    if (typeof Number.isNaN === 'function') {
      precoNumero = Number.isNaN(precoNumero) ? 0 : precoNumero;
    } else if (isNaN(precoNumero)) {
      precoNumero = 0;
    }
    return {
      linha: linha,
      termos: normalizar(linha.getAttribute('data-termos') || ''),
      status: (linha.getAttribute('data-status') || '').toLowerCase(),
      tipo: normalizar(linha.getAttribute('data-tipo') || ''),
      preco: precoNumero
    };
  });

  function filtrar() {
    var termoBusca = normalizar(campoBusca && campoBusca.value);
    var statusDesejado = (campoStatus && campoStatus.value) ? campoStatus.value.toLowerCase() : '';
    var tipoDesejado = normalizar(campoTipo && campoTipo.value);
    var precoMin = campoPrecoMin && campoPrecoMin.value ? parseFloat(String(campoPrecoMin.value).replace(',', '.')) : null;
    var precoMax = campoPrecoMax && campoPrecoMax.value ? parseFloat(String(campoPrecoMax.value).replace(',', '.')) : null;

    if (typeof Number.isNaN === 'function') {
      if (Number.isNaN(precoMin)) precoMin = null;
      if (Number.isNaN(precoMax)) precoMax = null;
    } else {
      if (isNaN(precoMin)) precoMin = null;
      if (isNaN(precoMax)) precoMax = null;
    }

    var linhasVisiveis = 0;

    dadosLinhas.forEach(function(item) {
      var visivel = true;

      if (termoBusca && item.termos.indexOf(termoBusca) === -1) {
        visivel = false;
      }

      if (visivel && statusDesejado && item.status !== statusDesejado) {
        visivel = false;
      }

      if (visivel && tipoDesejado && item.tipo.indexOf(tipoDesejado) === -1) {
        visivel = false;
      }

      if (visivel && precoMin !== null && item.preco < precoMin) {
        visivel = false;
      }

      if (visivel && precoMax !== null && item.preco > precoMax) {
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
    if (campoTipo) campoTipo.value = '';
    if (campoPrecoMin) campoPrecoMin.value = '';
    if (campoPrecoMax) campoPrecoMax.value = '';
    filtrar();
  }

  if (campoBusca) {
    campoBusca.addEventListener('input', filtrar);
  }
  if (campoStatus) {
    campoStatus.addEventListener('change', filtrar);
  }
  if (campoTipo) {
    campoTipo.addEventListener('change', filtrar);
  }
  if (campoPrecoMin) {
    campoPrecoMin.addEventListener('input', filtrar);
  }
  if (campoPrecoMax) {
    campoPrecoMax.addEventListener('input', filtrar);
  }
  if (botaoLimpar) {
    botaoLimpar.addEventListener('click', limpar);
  }
})();
</script>
