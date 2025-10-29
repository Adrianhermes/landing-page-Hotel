<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /hotel/views/login.php');
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    header('Location: /hotel/views/login.php?e=Informe email e senha validos');
    exit;
}

$conexao = new Conexao();
$pdo = $conexao->getPdo();

$stmt = $pdo->prepare('SELECT id, nome, email, senha_hash FROM usuarios_admin WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch();

if (!$usuario || empty($usuario['senha_hash']) || !password_verify($senha, $usuario['senha_hash'])) {
    header('Location: /hotel/views/login.php?e=Credenciais invalidas');
    exit;
}

if (password_needs_rehash($usuario['senha_hash'], PASSWORD_DEFAULT)) {
    $novoHash = password_hash($senha, PASSWORD_DEFAULT);
    $update = $pdo->prepare('UPDATE usuarios_admin SET senha_hash = :hash WHERE id = :id');
    $update->execute([
        'hash' => $novoHash,
        'id' => $usuario['id'],
    ]);
    $usuario['senha_hash'] = $novoHash;
}

registrarLogin($usuario);

header('Location: /hotel/views/telaGerenciamento.php?m=Login realizado com sucesso');
exit;
