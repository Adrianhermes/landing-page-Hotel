<?php
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
  <link rel="stylesheet" href="/hotel/assets/css/style.css">
</head>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Gerenciar Reservas</h2>
    <a href="/hotel/views/reservas.php" class="btn btn-success">Nova Reserva</a>
  </div>

  <?php if (!empty($flash)): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0 align-middle">
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
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td>
                  Nº <?= htmlspecialchars($r['quarto_numero']) ?><br>
                  <small><?= htmlspecialchars($r['quarto_tipo']) ?></small>
                </td>
                <td><?= htmlspecialchars($r['nome_cliente']) ?></td>
                <td><?= htmlspecialchars($r['cpf']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td><?= htmlspecialchars($r['telefone']) ?></td>
                <td><?= date('d/m/Y', strtotime($r['data_checkin'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['data_checkout'])) ?></td>
                <td>
                  <?php
                    $status = strtolower($r['status']);
                    $badge = 'secondary';
                    if ($status === 'confirmada') $badge = 'success';
                    elseif ($status === 'pendente') $badge = 'warning';
                    elseif ($status === 'cancelada') $badge = 'danger';
                  ?>
                  <span class="badge bg-<?= $badge ?>"><?= ucfirst($status) ?></span>
                </td>
                <td>
                  <a href="/hotel/views/updateReserva.php?id=<?= (int)$r['id'] ?>" class="btn btn-primary btn-sm">Editar</a>

                  <form action="/hotel/controllers/excluirReserva.php" method="POST" class="d-inline"
                        onsubmit="return confirm('Tem certeza que deseja excluir esta reserva?');">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
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
