<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/../config/conexao.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

$id            = $_POST['id'] ?? null;
$quarto_id     = $_POST['quarto_id'] ?? null;
$nome_cliente  = trim($_POST['nome_cliente'] ?? ''); // << nome_cliente (POST)
$email         = trim($_POST['email'] ?? '');
$cpf           = trim($_POST['cpf'] ?? '');
$telefone      = trim($_POST['telefone'] ?? '');
$data_checkin  = $_POST['data_checkin'] ?? '';
$data_checkout = $_POST['data_checkout'] ?? '';
$status        = $_POST['status'] ?? 'confirmada';

if (!$id) die("ID da reserva não informado.");

try {
  $sql = "
    UPDATE reservas
       SET quarto_id     = :quarto_id,
           nome_cliente  = :nome_cliente,   -- << coluna correta
           email         = :email,
           cpf           = :cpf,
           telefone      = :telefone,
           data_checkin  = :data_checkin,
           data_checkout = :data_checkout,
           status        = :status
     WHERE id = :id
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':id'            => $id,
    ':quarto_id'     => $quarto_id,
    ':nome_cliente'  => $nome_cliente,   // << bind correto
    ':email'         => $email,
    ':cpf'           => $cpf,
    ':telefone'      => $telefone,
    ':data_checkin'  => $data_checkin,
    ':data_checkout' => $data_checkout,
    ':status'        => $status
  ]);

  header("Location: /landing-page-Hotel-principal/views/gerenciarReserva.php?m=Reserva atualizada com sucesso!");
  exit;

} catch (PDOException $e) {
  die("Erro ao atualizar reserva: " . $e->getMessage());
}
