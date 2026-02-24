<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /landing-page-Hotel-principal/views/telaGerenciamento.php');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$novaSenha = $_POST['senha'] ?? '';
$confirmarSenha = $_POST['confirmar_senha'] ?? '';

if ($id <= 0) {
    header('Location: /landing-page-Hotel-principal/views/telaGerenciamento.php?e=Usuario invalido');
    exit;
}

if ($nome === '' || $email === '') {
    header("Location: /landing-page-Hotel-principal/views/updateUsuario.php?id={$id}&e=Preencha nome e email");
    exit;
}

$emailFiltrado = filter_var($email, FILTER_VALIDATE_EMAIL);
if (!$emailFiltrado) {
    header("Location: /landing-page-Hotel-principal/views/updateUsuario.php?id={$id}&e=Email invalido");
    exit;
}

$alterarSenha = false;
$novaSenhaTratada = '';
if ($novaSenha !== '' || $confirmarSenha !== '') {
    if (strlen($novaSenha) < 6) {
        header("Location: /landing-page-Hotel-principal/views/updateUsuario.php?id={$id}&e=Senha deve ter pelo menos 6 caracteres");
        exit;
    }

    if ($novaSenha !== $confirmarSenha) {
        header("Location: /landing-page-Hotel-principal/views/updateUsuario.php?id={$id}&e=Senha e confirmacao nao conferem");
        exit;
    }

    $alterarSenha = true;
    $novaSenhaTratada = password_hash($novaSenha, PASSWORD_DEFAULT);
}

$conexao = new Conexao();
$pdo = $conexao->getPdo();

try {
    // verifica existencia do usuario e evita atualizar email duplicado
    $stmtBusca = $pdo->prepare('SELECT id, email FROM usuarios_admin WHERE id = :id');
    $stmtBusca->execute([':id' => $id]);
    $usuarioAtual = $stmtBusca->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioAtual) {
        header('Location: /landing-page-Hotel-principal/views/telaGerenciamento.php?e=Usuario nao encontrado');
        exit;
    }

    $stmtDuplicado = $pdo->prepare('SELECT id FROM usuarios_admin WHERE email = :email AND id <> :id LIMIT 1');
    $stmtDuplicado->execute([
        ':email' => $emailFiltrado,
        ':id' => $id,
    ]);

    if ($stmtDuplicado->fetch()) {
        header("Location: /landing-page-Hotel-principal/views/updateUsuario.php?id={$id}&e=Email ja utilizado por outro usuario");
        exit;
    }

    if ($alterarSenha) {
        $stmtUpdate = $pdo->prepare('UPDATE usuarios_admin SET nome = :nome, email = :email, senha_hash = :senha WHERE id = :id');
        $stmtUpdate->execute([
            ':nome' => $nome,
            ':email' => $emailFiltrado,
            ':senha' => $novaSenhaTratada,
            ':id' => $id,
        ]);
    } else {
        $stmtUpdate = $pdo->prepare('UPDATE usuarios_admin SET nome = :nome, email = :email WHERE id = :id');
        $stmtUpdate->execute([
            ':nome' => $nome,
            ':email' => $emailFiltrado,
            ':id' => $id,
        ]);
    }

    header('Location: /landing-page-Hotel-principal/views/telaGerenciamento.php?m=Usuario atualizado com sucesso');
    exit;
} catch (PDOException $exception) {
    header("Location: /landing-page-Hotel-principal/views/updateUsuario.php?id={$id}&e=Erro ao atualizar usuario");
    exit;
}
