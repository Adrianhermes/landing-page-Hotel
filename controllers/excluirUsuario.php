<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /landing-page-Hotel-principal/views/telaGerenciamento.php');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    header('Location: /landing-page-Hotel-principal/views/telaGerenciamento.php?e=Usuario invalido');
    exit;
}

$usuarioAtual = usuarioLogado();
if ($usuarioAtual && isset($usuarioAtual['id']) && (int) $usuarioAtual['id'] === $id) {
    header('Location: /landing-page-Hotel-principal/views/telaGerenciamento.php?e=Voce nao pode excluir o proprio usuario');
    exit;
}

$conexao = new Conexao();
$pdo = $conexao->getPdo();

try {
    $stmt = $pdo->prepare('DELETE FROM usuarios_admin WHERE id = :id');
    $stmt->execute([':id' => $id]);

    header('Location: /landing-page-Hotel-principal/views/telaGerenciamento.php?m=Usuario excluido com sucesso');
    exit;
} catch (PDOException $exception) {
    header('Location: /landing-page-Hotel-principal/views/telaGerenciamento.php?e=Erro ao excluir usuario');
    exit;
}
