<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/../config/conexao.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

$id = $_POST['id'] ?? null;
if (!$id) {
  die('ID da reserva não informado.');
}

try {
  $stmt = $pdo->prepare("DELETE FROM reservas WHERE id = ?");
  $stmt->execute([$id]);

  // (Opcional) recalcular AUTO_INCREMENT
  // $pdo->exec("ALTER TABLE reservas AUTO_INCREMENT = 1");

  header("Location: /landing-page-Hotel-principal/views/gerenciarReserva.php?m=Reserva excluída com sucesso!");
  exit;
} catch (PDOException $e) {
  die("Erro ao excluir reserva: " . $e->getMessage());
}
