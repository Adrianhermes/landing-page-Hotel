<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /hotel/views/telaGerenciamento.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$emailSelect = $_POST['email_select'] ?? '';
$emailCustomRaw = trim($_POST['email_custom'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar_senha'] ?? '';

if ($nome === '' || $senha === '' || $confirmar === '') {
    header('Location: /hotel/views/telaGerenciamento.php?e=Preencha todos os campos');
    exit;
}

if ($emailSelect === '' && $emailCustomRaw === '') {
    header('Location: /hotel/views/telaGerenciamento.php?e=Selecione ou informe um email');
    exit;
}

if ($emailSelect === '__custom') {
    $email = filter_var($emailCustomRaw, FILTER_VALIDATE_EMAIL);
} else {
    $email = filter_var($emailSelect, FILTER_VALIDATE_EMAIL);
}

if (!$email) {
    header('Location: /hotel/views/telaGerenciamento.php?e=Email invalido');
    exit;
}

if (strlen($senha) < 6) {
    header('Location: /hotel/views/telaGerenciamento.php?e=Senha deve ter pelo menos 6 caracteres');
    exit;
}

if ($senha !== $confirmar) {
    header('Location: /hotel/views/telaGerenciamento.php?e=Senha e confirmacao nao conferem');
    exit;
}

$conexao = new Conexao();
$pdo = $conexao->getPdo();

$stmt = $pdo->prepare('SELECT id FROM usuarios_admin WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    header('Location: /hotel/views/telaGerenciamento.php?e=Email ja cadastrado');
    exit;
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $insert = $pdo->prepare('INSERT INTO usuarios_admin (nome, email, senha_hash) VALUES (:nome, :email, :hash)');
    $insert->execute([
        'nome' => $nome,
        'email' => $email,
        'hash' => $hash,
    ]);
    header('Location: /hotel/views/telaGerenciamento.php?m=Usuario cadastrado com sucesso');
    exit;
} catch (PDOException $e) {
    header('Location: /hotel/views/telaGerenciamento.php?e=Erro ao cadastrar usuario');
    exit;
}
