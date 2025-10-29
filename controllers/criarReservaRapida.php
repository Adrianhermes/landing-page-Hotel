<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensagem' => 'Metodo nao permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$rawInput = file_get_contents('php://input');
$payload = [];

if (!empty($rawInput)) {
    try {
        $decoded = json_decode($rawInput, true, 512, JSON_THROW_ON_ERROR);
        if (is_array($decoded)) {
            $payload = $decoded;
        }
    } catch (\JsonException $exception) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'mensagem' => 'JSON invalido recebido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (empty($payload)) {
    $payload = $_POST;
}

$quartoId = isset($payload['quarto_id']) ? (int) $payload['quarto_id'] : 0;

if ($quartoId <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'mensagem' => 'Quarto invalido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $conexao = new Conexao();
    $pdo = $conexao->getPdo();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Nao foi possivel conectar ao banco de dados.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmtQuarto = $pdo->prepare('SELECT id, numero, tipo, preco, descricao FROM quartos WHERE id = :id AND ativo = 1');
    $stmtQuarto->execute([':id' => $quartoId]);
    $quarto = $stmtQuarto->fetch(PDO::FETCH_ASSOC);

    if (!$quarto) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'mensagem' => 'Quarto nao encontrado ou inativo.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Falha ao consultar dados do quarto.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nomeLead = sprintf('Lead Saiba Mais - Quarto %s', $quarto['numero']);

try {
    $stmtReservaExistente = $pdo->prepare("
        SELECT id, quarto_id, nome_cliente, email, cpf, telefone, data_checkin, data_checkout, status, created_at
          FROM reservas
         WHERE quarto_id = :quarto_id
           AND nome_cliente = :nome_cliente
           AND DATE(created_at) = CURDATE()
         ORDER BY id DESC
         LIMIT 1
    ");
    $stmtReservaExistente->execute([
        ':quarto_id' => $quartoId,
        ':nome_cliente' => $nomeLead,
    ]);
    $reservaExistente = $stmtReservaExistente->fetch(PDO::FETCH_ASSOC);

    if ($reservaExistente) {
        echo json_encode([
            'ok' => true,
            'mensagem' => 'Reserva ja existente reutilizada.',
            'reserva' => $reservaExistente,
            'quarto' => $quarto,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Falha ao verificar reservas existentes.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$dataCheckin = new DateTimeImmutable('+7 days');
$dataCheckout = $dataCheckin->modify('+2 days');
$tentativas = 0;
$maxTentativas = 6;
$periodoDisponivel = false;

$sqlConflito = "
    SELECT COUNT(*)
      FROM reservas
     WHERE quarto_id = :quarto_id
       AND status <> 'cancelada'
       AND NOT (
            :data_checkout <= data_checkin
         OR :data_checkin >= data_checkout
       )
";

while ($tentativas < $maxTentativas) {
    $stmtConflito = $pdo->prepare($sqlConflito);
    $stmtConflito->execute([
        ':quarto_id' => $quartoId,
        ':data_checkin' => $dataCheckin->format('Y-m-d'),
        ':data_checkout' => $dataCheckout->format('Y-m-d'),
    ]);

    $reservaEmUso = (int) $stmtConflito->fetchColumn() > 0;

    if (!$reservaEmUso) {
        $periodoDisponivel = true;
        break;
    }

    $tentativas++;
    $dataCheckin = $dataCheckin->modify('+3 days');
    $dataCheckout = $dataCheckin->modify('+2 days');
}

if (!$periodoDisponivel) {
    http_response_code(409);
    echo json_encode(['ok' => false, 'mensagem' => 'Nao ha periodo disponivel para criar a reserva de demonstracao.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$emailLead = sprintf('lead+%s@hotelteste.com', preg_replace('/\D+/', '', (string) $quarto['numero']));
$cpfLead = '000.000.000-00';
$telefoneLead = '(00) 00000-0000';
$statusLead = 'confirmada';

try {
    $pdo->beginTransaction();

    $stmtInsert = $pdo->prepare("
        INSERT INTO reservas
            (quarto_id, nome_cliente, email, cpf, telefone, data_checkin, data_checkout, status)
        VALUES
            (:quarto_id, :nome_cliente, :email, :cpf, :telefone, :data_checkin, :data_checkout, :status)
    ");

    $stmtInsert->execute([
        ':quarto_id' => $quartoId,
        ':nome_cliente' => $nomeLead,
        ':email' => $emailLead,
        ':cpf' => $cpfLead,
        ':telefone' => $telefoneLead,
        ':data_checkin' => $dataCheckin->format('Y-m-d'),
        ':data_checkout' => $dataCheckout->format('Y-m-d'),
        ':status' => $statusLead,
    ]);

    $reservaId = (int) $pdo->lastInsertId();

    $stmtBusca = $pdo->prepare("
        SELECT id, quarto_id, nome_cliente, email, cpf, telefone, data_checkin, data_checkout, status, created_at
          FROM reservas
         WHERE id = :id
         LIMIT 1
    ");
    $stmtBusca->execute([':id' => $reservaId]);
    $reserva = $stmtBusca->fetch(PDO::FETCH_ASSOC);

    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Nao foi possivel criar a reserva de demonstracao.'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'ok' => true,
    'mensagem' => 'Reserva criada com sucesso.',
    'reserva' => $reserva,
    'quarto' => $quarto,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
