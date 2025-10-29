<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../config/conexao.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

$id = $_GET['id'] ?? null;
if (!$id) {
  die('ID do quarto não informado.');
}

// Busca o quarto
$stmt = $pdo->prepare("SELECT id, numero, tipo, preco, descricao, ativo FROM quartos WHERE id = ?");
$stmt->execute([$id]);
$quarto = $stmt->fetch();

if (!$quarto) {
  die('Quarto não encontrado.');
}

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<head>
  <link rel="stylesheet" href="/hotel/assets/css/style.css">
</head>

<div class="container">
  <h2>Editar Quarto #<?= (int)$quarto['id'] ?></h2>

  <form action="/hotel/controllers/atualizarQuarto.php" method="POST" class="form-quarto">

    <input type="hidden" name="id" value="<?= (int)$quarto['id'] ?>">

    <!-- Número do quarto -->
    <div class="mb-3">
      <label for="numero" class="form-label">Número do Quarto:</label>
      <input type="number" class="form-control" id="numero" name="numero" value="<?= h($quarto['numero']) ?>" required>
    </div>

    <!-- Tipo -->
    <div class="mb-3">
      <label for="tipo" class="form-label">Tipo de Quarto:</label>
      <select class="form-control" id="tipo" name="tipo" required>
        <?php
          $tipos = ['Standard','Luxo','Suite','Suíte'];
          $tipoAtual = (string)$quarto['tipo'];
          foreach ($tipos as $t) {
            $sel = ($t === $tipoAtual) ? 'selected' : '';
            echo "<option value=\"".h($t)."\" $sel>".h($t)."</option>";
          }
        ?>
      </select>
    </div>

    <!-- Preço -->
    <div class="mb-3">
      <label for="preco" class="form-label">Preço por Noite (R$):</label>
      <input type="number" step="0.01" class="form-control" id="preco" name="preco" value="<?= h($quarto['preco']) ?>" required>
    </div>

    <!-- Descrição -->
    <div class="mb-3">
      <label for="descricao" class="form-label">Descrição:</label>
      <textarea class="form-control" id="descricao" name="descricao" required><?= h($quarto['descricao']) ?></textarea>
    </div>

    <!-- Ativo -->
    <div class="mb-3 form-check">
      <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1" <?= ((int)$quarto['ativo'] === 1 ? 'checked' : '') ?>>
      <label class="form-check-label" for="ativo">Ativo</label>
    </div>

    <!-- Botões -->
    <div class="mb-3 d-flex gap-2">
      <button type="submit" class="btn btn-success">Salvar Alterações</button>
      <a href="/hotel/views/gerenciarQuarto.php" class="btn btn-secondary">Cancelar</a>
    </div>

  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
