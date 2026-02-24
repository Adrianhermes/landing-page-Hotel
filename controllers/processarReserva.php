<?php
require_once __DIR__ . '/../config/auth.php';
exigirLoginOuReservaPublica();
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/email.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

/* ---------- 1. Receber e limpar dados ---------- */
$quarto_id     = $_POST['quarto_id']     ?? null;
$nome_cliente  = trim($_POST['nome_cliente'] ?? '');
$email         = strtolower(trim($_POST['email'] ?? ''));
$cpf           = trim($_POST['cpf'] ?? '');
$telefone      = trim($_POST['telefone'] ?? '');
$data_checkin  = $_POST['data_checkin']  ?? '';
$data_checkout = $_POST['data_checkout'] ?? '';
$status        = $_POST['status']        ?? 'confirmada';

/* ---------- 2. Validação básica ---------- */
$erros = [];

// quarto
if (empty($quarto_id) || !ctype_digit((string)$quarto_id)) {
  $erros[] = "Selecione um quarto válido.";
}

// nome
if ($nome_cliente === '' || strlen($nome_cliente) < 3) {
  $erros[] = "Informe o nome completo do cliente.";
}

// email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $erros[] = "E-mail inválido.";
}

// CPF
if (!preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf)) {
  $erros[] = "CPF inválido. Use o formato 000.000.000-00.";
}

// telefone
if (!preg_match('/^\(\d{2}\)\s?\d{4,5}-\d{4}$/', $telefone)) {
  $erros[] = "Telefone inválido. Use o formato (99) 99999-9999.";
}

// datas
if (empty($data_checkin) || empty($data_checkout)) {
  $erros[] = "Informe as datas de check-in e check-out.";
} else {
  $checkin  = strtotime($data_checkin);
  $checkout = strtotime($data_checkout);
  if (!$checkin || !$checkout) {
    $erros[] = "Datas inválidas.";
  } elseif ($checkin >= $checkout) {
    $erros[] = "A data de check-out deve ser posterior ao check-in.";
  }
}

// status
$valid_status = ['confirmada', 'cancelada'];
if (!in_array($status, $valid_status, true)) {
  $erros[] = "Status inválido.";
}

// Se tiver erros, redireciona de volta com mensagem
if (!empty($erros)) {
  $msg = urlencode(implode(' ', $erros));
  header("Location: /landing-page-Hotel-principal/views/reserva.php?m=$msg");
  exit;
}

/* ---------- 3. Validar quarto e verificar conflito de reserva ---------- */
$dadosQuarto = null;

try {
  $sqlQuarto = "
    SELECT numero, tipo
      FROM quartos
     WHERE id = :id
  ";
  $stmtQuarto = $pdo->prepare($sqlQuarto);
  $stmtQuarto->execute([':id' => $quarto_id]);
  $dadosQuarto = $stmtQuarto->fetch(PDO::FETCH_ASSOC);

  if (!$dadosQuarto) {
    $msg = urlencode("Quarto selecionado nao foi encontrado.");
    header("Location: /landing-page-Hotel-principal/views/reserva.php?m=$msg");
    exit;
  }
} catch (PDOException $e) {
  die("Erro ao validar quarto: " . $e->getMessage());
}

try {
  $sql_conf = "
    SELECT COUNT(*) 
      FROM reservas
     WHERE quarto_id = :quarto_id
       AND status = 'confirmada'
       AND NOT (
          :data_checkout <= data_checkin
          OR :data_checkin >= data_checkout
       )
  ";
  $st_conf = $pdo->prepare($sql_conf);
  $st_conf->execute([
    ':quarto_id' => $quarto_id,
    ':data_checkin' => $data_checkin,
    ':data_checkout' => $data_checkout
  ]);
  $conflitos = (int) $st_conf->fetchColumn();

  if ($conflitos > 0) {
    $msg = urlencode("Já existe uma reserva para este quarto nesse período.");
    header("Location: /landing-page-Hotel-principal/views/reserva.php?m=$msg");
    exit;
  }
} catch (PDOException $e) {
  die("Erro ao verificar disponibilidade: " . $e->getMessage());
}

/* ---------- 4. Inserir reserva ---------- */
try {
  $sql = "
    INSERT INTO reservas
      (quarto_id, nome_cliente, email, cpf, telefone, data_checkin, data_checkout, status)
    VALUES
      (:quarto_id, :nome_cliente, :email, :cpf, :telefone, :data_checkin, :data_checkout, :status)
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':quarto_id' => $quarto_id,
    ':nome_cliente' => $nome_cliente,
    ':email' => $email,
    ':cpf' => $cpf,
    ':telefone' => $telefone,
    ':data_checkin' => $data_checkin,
    ':data_checkout' => $data_checkout,
    ':status' => $status
  ]);
} catch (PDOException $e) {
  die("Erro ao cadastrar reserva: " . $e->getMessage());
}

/* ---------- 5. Enviar e-mail de confirmacao ---------- */
if ($status !== 'cancelada') {
  enviarEmailConfirmacaoReserva([
    'nome_cliente' => $nome_cliente,
    'email' => $email,
    'numero_quarto' => $dadosQuarto['numero'] ?? null,
    'tipo_quarto' => $dadosQuarto['tipo'] ?? null,
    'data_checkin' => $data_checkin,
    'data_checkout' => $data_checkout,
    'status' => $status,
  ]);
}

// Redireciona para sucesso
header("Location: /landing-page-Hotel-principal/views/sucessoReserva.php");
exit;
