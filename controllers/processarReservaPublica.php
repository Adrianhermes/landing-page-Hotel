<?php

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/email.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

function redirecionar(string $situacao, string $mensagem = ''): void {
    $url = '/hotel/index.php?reserva=' . rawurlencode($situacao);
    if ($mensagem !== '') {
        $url .= '&msg=' . rawurlencode($mensagem);
    }
    header("Location: {$url}");
    exit;
}

$quarto_id     = $_POST['quarto_id']     ?? null;
$nome_cliente  = trim($_POST['nome_cliente'] ?? '');
$email         = strtolower(trim($_POST['email'] ?? ''));
$cpf           = trim($_POST['cpf'] ?? '');
$telefone      = trim($_POST['telefone'] ?? '');
$data_checkin  = $_POST['data_checkin']  ?? '';
$data_checkout = $_POST['data_checkout'] ?? '';
$status        = $_POST['status']        ?? 'confirmada';

$erros = [];

if (empty($quarto_id) || !ctype_digit((string) $quarto_id)) {
    $erros[] = 'Selecione um quarto válido.';
}

if ($nome_cliente === '' || strlen($nome_cliente) < 3) {
    $erros[] = 'Informe o nome completo.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'E-mail inválido.';
}

if (!preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf)) {
    $erros[] = 'CPF inválido. Use o formato 000.000.000-00.';
}

if (!preg_match('/^\(\d{2}\)\s?\d{4,5}-\d{4}$/', $telefone)) {
    $erros[] = 'Telefone inválido. Use o formato (99) 99999-9999.';
}

if (empty($data_checkin) || empty($data_checkout)) {
    $erros[] = 'Informe as datas de entrada e saída.';
} else {
    $checkin  = strtotime($data_checkin);
    $checkout = strtotime($data_checkout);

    if (!$checkin || !$checkout) {
        $erros[] = 'Datas inválidas.';
    } elseif ($checkin >= $checkout) {
        $erros[] = 'A data de saída deve ser posterior à data de entrada.';
    }
}

$status = strtolower($status);
$statusValidos = ['confirmada', 'cancelada'];
if (!in_array($status, $statusValidos, true)) {
    $status = 'confirmada';
}

if (!empty($erros)) {
    redirecionar('erro', $erros[0]);
}

$dadosQuarto = null;

try {
    $stmtQuarto = $pdo->prepare('SELECT numero, tipo FROM quartos WHERE id = :id AND ativo = 1');
    $stmtQuarto->execute([':id' => $quarto_id]);
    $dadosQuarto = $stmtQuarto->fetch(PDO::FETCH_ASSOC);

    if (!$dadosQuarto) {
        redirecionar('erro', 'Quarto indisponível para reserva.');
    }
} catch (PDOException $e) {
    redirecionar('erro', 'Não foi possível validar o quarto selecionado.');
}

try {
    $sqlDisponibilidade = "
        SELECT COUNT(*)
          FROM reservas
         WHERE quarto_id = :quarto_id
           AND status = 'confirmada'
           AND NOT (
             :data_checkout <= data_checkin
             OR :data_checkin >= data_checkout
           )
    ";

    $verifica = $pdo->prepare($sqlDisponibilidade);
    $verifica->execute([
        ':quarto_id' => $quarto_id,
        ':data_checkin' => $data_checkin,
        ':data_checkout' => $data_checkout,
    ]);

    if ((int) $verifica->fetchColumn() > 0) {
        redirecionar('erro', 'O quarto escolhido não está disponível nas datas informadas.');
    }
} catch (PDOException $e) {
    redirecionar('erro', 'Não foi possível verificar a disponibilidade no momento.');
}

try {
    $sqlInsert = "
        INSERT INTO reservas
            (quarto_id, nome_cliente, email, cpf, telefone, data_checkin, data_checkout, status)
        VALUES
            (:quarto_id, :nome_cliente, :email, :cpf, :telefone, :data_checkin, :data_checkout, :status)
    ";

    $insert = $pdo->prepare($sqlInsert);
    $insert->execute([
        ':quarto_id' => $quarto_id,
        ':nome_cliente' => $nome_cliente,
        ':email' => $email,
        ':cpf' => $cpf,
        ':telefone' => $telefone,
        ':data_checkin' => $data_checkin,
        ':data_checkout' => $data_checkout,
        ':status' => $status,
    ]);
} catch (PDOException $e) {
    redirecionar('erro', 'Não foi possível registrar a sua reserva. Tente novamente mais tarde.');
}

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

redirecionar('sucesso');
