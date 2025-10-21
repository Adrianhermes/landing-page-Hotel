<?php
require_once __DIR__ . '/../config/conexao.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

$id = $_POST['id'] ?? null;
$numero = $_POST['numero'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$preco = $_POST['preco'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$ativo = isset($_POST['ativo']) ? 1 : 0;

if (!$id) {
  die('ID do quarto não informado.');
}

try {
  $sql = "UPDATE quartos
          SET numero = :numero,
              tipo = :tipo,
              preco = :preco,
              descricao = :descricao,
              ativo = :ativo
          WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':numero' => $numero,
    ':tipo' => $tipo,
    ':preco' => $preco,
    ':descricao' => $descricao,
    ':ativo' => $ativo,
    ':id' => $id
  ]);

  header("Location: /hotel/views/gerenciarQuarto.php?m=Quarto atualizado com sucesso!");
  exit;

} catch (PDOException $e) {
  // Trata erro de número duplicado, por exemplo (unique em 'numero')
  if ($e->getCode() == 23000) {
    header("Location: /hotel/views/updateQuarto.php?id=".$id."&m=Já existe um quarto com esse número.");
    exit;
  }
  die("Erro ao atualizar quarto: " . $e->getMessage());
}
