<?php
require_once __DIR__ . '/../config/conexao.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

// Verifica se veio o ID via POST
$id = $_POST['id'] ?? null;

if (!$id) {
    die("ID do quarto não informado.");
}

try {
    // Excluir o quarto
    $stmt = $pdo->prepare("DELETE FROM quartos WHERE id = ?");
    $stmt->execute([$id]);

    // Redireciona de volta com mensagem de sucesso
    header("Location: /hotel/views/gerenciarQuarto.php?m=Quarto excluído com sucesso!");
    exit;

} catch (PDOException $e) {
    die("Erro ao excluir quarto: " . $e->getMessage());
}
