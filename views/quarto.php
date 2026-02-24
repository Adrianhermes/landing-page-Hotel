<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/includes/header.php';
?>

<head>
  <link rel="stylesheet" href="/landing-page-Hotel-principal/assets/css/style.css">
</head>

<div class="container">
  <h2>Cadastro de Quarto</h2>

  <!-- novalidate para controlar mensagens customizadas via JS -->
  <form action="/landing-page-Hotel-principal/controllers/processarQuarto.php" method="POST" class="form-quarto needs-validation" novalidate>

    <!-- Número do quarto -->
    <div class="mb-3">
      <label for="numero" class="form-label">Número do Quarto:</label>
      <input
        type="number"
        class="form-control"
        id="numero"
        name="numero"
        inputmode="numeric"
        min="1"
        step="1"
        required
        aria-describedby="numeroHelp">
      <div id="numeroHelp" class="form-text">Use apenas números inteiros e positivos (ex.: 101).</div>
      <div class="invalid-feedback">Informe um número de quarto válido (inteiro ≥ 1).</div>
    </div>

    <!-- Tipo -->
    <div class="mb-3">
      <label for="tipo" class="form-label">Tipo de Quarto:</label>
      <select class="form-control" id="tipo" name="tipo" required>
        <option value="">Selecione...</option>
        <option value="Standard">Standard</option>
        <option value="Luxo">Luxo</option>
        <option value="Suite">Suíte</option>
      </select>
      <div class="invalid-feedback">Selecione um tipo de quarto.</div>
    </div>

    <!-- Preço -->
    <div class="mb-3">
      <label for="preco" class="form-label">Preço por Noite (R$):</label>
      <input
        type="number"
        class="form-control"
        id="preco"
        name="preco"
        min="0.01"
        step="0.01"
        required
        aria-describedby="precoHelp">
      <div id="precoHelp" class="form-text">Valor maior que 0, com até 2 casas decimais (ex.: 199.90).</div>
      <div class="invalid-feedback">Informe um preço válido (maior que 0, com até 2 casas decimais).</div>
    </div>

    <!-- Descrição -->
    <div class="mb-3">
      <label for="descricao" class="form-label">Descrição:</label>
      <textarea
        class="form-control"
        id="descricao"
        name="descricao"
        minlength="10"
        maxlength="500"
        required
        aria-describedby="descricaoHelp"></textarea>
      <div id="descricaoHelp" class="form-text">Entre 10 e 500 caracteres.</div>
      <div class="invalid-feedback">A descrição deve ter entre 10 e 500 caracteres.</div>
    </div>

    <!-- Botões -->
    <div class="mb-3 d-flex gap-2">
      <button type="submit" class="btn btn-success">Cadastrar Quarto</button>
      <button type="button" class="btn btn-secondary" onclick="history.back()">Voltar</button>
    </div>

  </form>
</div>

<script>
  (function() {
    // util: trim seguro
    const trim = (v) => (v ?? '').toString().trim();

    // Mapa de tipos válidos (previne manipulação do DOM)
    const TIPOS_VALIDOS = new Set(['Standard', 'Luxo', 'Suite', 'Suíte']);

    const form = document.querySelector('.form-quarto');
    const numero = document.getElementById('numero');
    const tipo = document.getElementById('tipo');
    const preco = document.getElementById('preco');
    const descricao = document.getElementById('descricao');

    // Normalizações e máscaras leves
    numero.addEventListener('input', () => {
      // força inteiro >= 1
      let val = numero.value.replace(/[^\d]/g, '');
      if (val !== '') val = String(parseInt(val, 10));
      numero.value = val;
      if (val === '' || parseInt(val, 10) < 1) {
        numero.setCustomValidity('Número inválido.');
      } else {
        numero.setCustomValidity('');
      }
    });

    // valida tipo
    tipo.addEventListener('change', () => {
      const v = trim(tipo.value);
      if (!TIPOS_VALIDOS.has(v)) {
        tipo.setCustomValidity('Tipo inválido.');
      } else {
        tipo.setCustomValidity('');
      }
    });

    // formata preço em 2 casas ao sair do campo
    preco.addEventListener('blur', () => {
      const raw = trim(preco.value).replace(',', '.');
      const num = Number(raw);
      if (!isFinite(num) || num <= 0) {
        preco.setCustomValidity('Preço inválido.');
        return;
      }
      // Mantém até 2 casas
      preco.value = num.toFixed(2);
      preco.setCustomValidity('');
    });

    preco.addEventListener('input', () => {
      // enquanto digita, apenas garante que é número > 0
      const raw = trim(preco.value).replace(',', '.');
      const num = Number(raw);
      if (!isFinite(num) || num <= 0) {
        preco.setCustomValidity('Preço inválido.');
      } else {
        preco.setCustomValidity('');
      }
    });

    // valida descrição tamanho
    const validaDescricao = () => {
      const v = trim(descricao.value);
      if (v.length < 10 || v.length > 500) {
        descricao.setCustomValidity('Tamanho inválido.');
      } else {
        descricao.setCustomValidity('');
      }
    };
    descricao.addEventListener('input', validaDescricao);
    descricao.addEventListener('blur', validaDescricao);

    // validação final no submit (Bootstrap + custom)
    form.addEventListener('submit', function(event) {
      // roda validações específicas
      numero.dispatchEvent(new Event('input'));
      tipo.dispatchEvent(new Event('change'));
      preco.dispatchEvent(new Event('input'));
      descricao.dispatchEvent(new Event('input'));

      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  })();
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
