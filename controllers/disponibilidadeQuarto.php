<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
require_once __DIR__ . '/../config/conexao.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido.']);
    exit;
}

$quartoId = filter_input(INPUT_GET, 'quarto_id', FILTER_VALIDATE_INT);
$reservaIgnorar = filter_input(INPUT_GET, 'reserva_id', FILTER_VALIDATE_INT);

if (!$quartoId) {
    http_response_code(400);
    echo json_encode(['erro' => 'Parametro quarto_id obrigatorio.']);
    exit;
}

try {
    $conexao = new Conexao();
    $pdo = $conexao->getPdo();

    $sql = "
        SELECT id, data_checkin, data_checkout, status
          FROM reservas
         WHERE quarto_id = :quarto_id
           AND status = 'confirmada'
    ";

    $params = [':quarto_id' => $quartoId];

    if ($reservaIgnorar) {
        $sql .= " AND id <> :reserva_id";
        $params[':reserva_id'] = $reservaIgnorar;
    }

    $sql .= " ORDER BY data_checkin ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $reservas = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reservas[] = [
            'id' => (int) $row['id'],
            'data_checkin' => $row['data_checkin'],
            'data_checkout' => $row['data_checkout'],
            'status' => $row['status'],
        ];
    }

    echo json_encode([
        'quarto_id' => $quartoId,
        'reservas' => $reservas,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao consultar reservas existentes.']);
}
