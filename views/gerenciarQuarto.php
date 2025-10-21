<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../config/conexao.php';


$flash = $_GET['m'] ?? null; // define default (null) se não vier na URL


$conexao = new Conexao();
$pdo = $conexao->getPdo();

$stmt = $pdo->query("
  SELECT id, numero, tipo, preco, descricao, ativo AS status
  FROM quartos
  ORDER BY id ASC
");
$quartos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<head>
    <link rel="stylesheet" href="/hotel/assets/css/style.css">
</head>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Gerenciar Quartos</h2>
        <a href="/hotel/views/quarto.php" class="btn btn-success">Novo Quarto</a>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-success py-2"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:80px;">ID</th>
                            <th>Número</th>
                            <th>Tipo</th>
                            <th>Preço</th>
                            <th>Status</th>
                            <th style="width:160px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quartos)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">Nenhum quarto cadastrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($quartos as $q): ?>
                                <tr>
                                    <td><?= (int)$q['id'] ?></td>
                                    <td><?= htmlspecialchars($q['numero']) ?></td>
                                    <td><?= htmlspecialchars($q['tipo']) ?></td>
                                    <td><?=
                                        'R$ ' . number_format((float)$q['preco'], 2, ',', '.')
                                        ?></td>
                                    <td>
                                        <?php
                                        // status: 1/0 ou 'Ativo'/'Inativo'
                                        $ativo = is_numeric($q['status']) ? ((int)$q['status'] === 1) : (mb_strtolower($q['status']) === 'ativo');
                                        ?>
                                        <span class="badge <?= $ativo ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $ativo ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td>
                                       <a href="/hotel/views/updateQuarto.php?id=<?= (int)$q['id'] ?>" class="btn btn-primary btn-sm">
  Editar
</a>




                                        <!-- Excluir via POST com confirmação -->
                                        <form action="/hotel/controllers/excluirQuarto.php" method="POST" class="d-inline"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este quarto?');">
                                            <input type="hidden" name="id" value="<?= (int)$q['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>