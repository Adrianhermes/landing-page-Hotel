<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/../config/conexao.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

$stmt = $pdo->query('SELECT id, nome, email, criado_em FROM usuarios_admin ORDER BY criado_em DESC');
$usuarios = $stmt->fetchAll();
$emailsDisponiveis = $usuarios ? array_unique(array_filter(array_column($usuarios, 'email'))) : [];
sort($emailsDisponiveis);

$totalUsuarios = count($usuarios);
$ultimoCadastro = null;
if (!empty($usuarios) && !empty($usuarios[0]['criado_em'])) {
    $timestampUltimo = strtotime($usuarios[0]['criado_em']);
    if ($timestampUltimo) {
        $ultimoCadastro = date('d/m/Y H:i', $timestampUltimo);
    }
}
$usuariosRecentes = array_slice($usuarios, 0, 3);

$usuarioLogado = usuarioLogado();

require_once __DIR__ . '/includes/header.php';

$flashSuccess = $_GET['m'] ?? null;
$flashError = $_GET['e'] ?? null;
?>

<head>
  <link rel="stylesheet" href="/landing-page-Hotel-principal/assets/css/style.css">
</head>

<div class="container admin-dashboard">
  <section class="admin-header">
    <div class="admin-header-text">
      <h2 class="admin-title">Painel Administrativo</h2>
      <p class="admin-subtitle">
        Gerencie os usuarios administradores do sistema, crie novos acessos e mantenha tudo organizado em um unico lugar.
      </p>
    </div>

    <div class="admin-summary">
      <div class="admin-summary-card">
        <span class="admin-summary-label">Usuarios ativos</span>
        <span class="admin-summary-value"><?= (int) $totalUsuarios ?></span>
      </div>

      <div class="admin-summary-card admin-summary-recent">
        <span class="admin-summary-label">Ultimos cadastros</span>
        <?php if (empty($usuariosRecentes)): ?>
          <span class="admin-summary-placeholder">Nenhum cadastro registrado</span>
        <?php else: ?>
          <ul class="admin-summary-list">
            <?php foreach ($usuariosRecentes as $recent): ?>
              <li>
                <span><?= htmlspecialchars($recent['nome']) ?></span>
                <small><?= htmlspecialchars($recent['email']) ?></small>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="admin-summary-card">
        <span class="admin-summary-label">Ultima atualizacao</span>
        <span class="admin-summary-value admin-summary-small">
          <?= $ultimoCadastro ? htmlspecialchars($ultimoCadastro) : '---' ?>
        </span>
      </div>
    </div>
  </section>

  <?php if (!empty($flashSuccess)): ?>
    <div class="admin-alert admin-alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
  <?php endif; ?>

  <?php if (!empty($flashError)): ?>
    <div class="admin-alert admin-alert-error"><?= htmlspecialchars($flashError) ?></div>
  <?php endif; ?>

  <section class="admin-grid">
    <div class="admin-card">
      <header class="admin-card-header">
        <h3>Novo usuario administrador</h3>
        <p>Defina o nome completo, escolha ou informe um email e crie uma senha inicial.</p>
      </header>
      <div class="admin-card-body">
        <form action="/landing-page-Hotel-principal/controllers/cadastrarUsuario.php" method="POST" class="admin-form">
          <div class="admin-form-field">
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome" placeholder="Ex.: Maria Pereira" required>
          </div>

          <div class="admin-form-field">
            <label for="email_select">Email do login</label>
            <select id="email_select" name="email_select" required>
              <option value="" disabled selected>Escolha um email padrao ja utilizado</option>
              <?php foreach ($emailsDisponiveis as $emailDisponivel): ?>
                <option value="<?= htmlspecialchars($emailDisponivel) ?>"><?= htmlspecialchars($emailDisponivel) ?></option>
              <?php endforeach; ?>
              <option value="__custom">Cadastrar outro email...</option>
            </select>
            <small class="admin-field-help">
              Use um email existente ou selecione "Cadastrar outro email..." para informar um novo endereco.
            </small>
            <input type="email"
                   class="admin-custom-email <?= empty($emailsDisponiveis) ? '' : 'is-hidden' ?>"
                   id="email_custom"
                   name="email_custom"
                   placeholder="Digite o email completo (ex.: nome@empresa.com)"
                   <?= empty($emailsDisponiveis) ? 'required' : '' ?>>
          </div>

          <div class="admin-form-group">
            <div class="admin-form-field">
              <label for="senha">Senha temporaria</label>
              <input type="password" id="senha" name="senha" placeholder="Minimo de 6 caracteres" required minlength="6">
            </div>
            <div class="admin-form-field">
              <label for="confirmar_senha">Confirmar senha</label>
              <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a senha" required minlength="6">
            </div>
          </div>

          <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn-primary">Cadastrar usuario</button>
          </div>
        </form>
      </div>
    </div>

    <div class="admin-card">
      <header class="admin-card-header">
        <h3>Usuarios cadastrados</h3>
        <p>Acompanhe quem ja possui acesso e edite ou remova entradas rapidamente.</p>
      </header>
      <div class="admin-card-body">
        <?php if (empty($usuarios)): ?>
          <div class="admin-empty-state">
            <h4>Nenhum usuario cadastrado</h4>
            <p>Cadastre o primeiro administrador usando o formulario ao lado.</p>
          </div>
        <?php else: ?>
          <div class="admin-table-wrapper">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Email</th>
                  <th>Criado em</th>
                  <th>Acoes</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                  <?php
                    $usuarioId = (int) $usuario['id'];
                    $proprioUsuario = $usuarioLogado && isset($usuarioLogado['id']) && (int) $usuarioLogado['id'] === $usuarioId;
                    $criadoEm = $usuario['criado_em'] ?? '';
                    $criadoFormatado = $criadoEm && strtotime($criadoEm) ? date('d/m/Y H:i', strtotime($criadoEm)) : '--';
                  ?>
                  <tr>
                    <td>
                      <strong><?= htmlspecialchars($usuario['nome']) ?></strong>
                      <?php if ($proprioUsuario): ?>
                        <span class="admin-badge">Voce</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($criadoFormatado) ?></td>
                    <td>
                      <div class="admin-table-actions">
                        <a href="/landing-page-Hotel-principal/views/updateUsuario.php?id=<?= $usuarioId ?>" class="admin-btn admin-btn-ghost">Editar</a>
                        <form action="/landing-page-Hotel-principal/controllers/excluirUsuario.php" method="POST" class="form-excluir-usuario">
                          <input type="hidden" name="id" value="<?= $usuarioId ?>">
                          <button type="submit" class="admin-btn admin-btn-danger" <?= $proprioUsuario ? 'disabled' : '' ?>>
                            Excluir
                          </button>
                        </form>
                      </div>
                      <?php if ($proprioUsuario): ?>
                        <small class="admin-hint">Seu usuario nao pode ser removido.</small>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
(function() {
  const select = document.getElementById('email_select');
  const customInput = document.getElementById('email_custom');

  if (!select || !customInput) {
    return;
  }

  if (!customInput.classList.contains('is-hidden')) {
    select.value = '__custom';
    customInput.required = true;
  }

  select.addEventListener('change', function() {
    if (select.value === '__custom') {
      customInput.classList.remove('is-hidden');
      customInput.required = true;
      customInput.focus();
    } else {
      customInput.classList.add('is-hidden');
      customInput.required = false;
      customInput.value = '';
    }
  });
})();
</script>
<script>
  document.querySelectorAll('.form-excluir-usuario').forEach(function(form) {
    form.addEventListener('submit', function(event) {
      const botao = form.querySelector('button[type="submit"]');
      if (botao && botao.disabled) {
        event.preventDefault();
        return false;
      }

      const confirmado = confirm('Tem certeza de que deseja excluir este usuario? Esta acao nao pode ser desfeita.');
      if (!confirmado) {
        event.preventDefault();
      }
    });
  });
</script>
