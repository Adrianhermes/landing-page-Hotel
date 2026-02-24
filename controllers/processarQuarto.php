<?php
require_once __DIR__ . '/../config/auth.php';
exigirLogin();
// /landing-page-Hotel-principal/controllers/processarQuarto.php
require_once __DIR__ . '/../config/conexao.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();

/* 1) Coleta e normalização */
$numero    = isset($_POST['numero']) ? (int)$_POST['numero'] : 0;
$tipo      = trim($_POST['tipo'] ?? '');
$precoRaw  = trim($_POST['preco'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

// suporta vírgula como decimal (ex.: 199,90)
$precoRaw = str_replace(',', '.', $precoRaw);
$preco    = is_numeric($precoRaw) ? (float)$precoRaw : 0.0;

// se quiser marcar ativo por padrão (se a coluna existir)
$ativo = 1;

/* 2) Validações simples (server-side) */
$tiposValidos = ['Standard','Luxo','Suite','Suíte'];
$erros = [];

if ($numero < 1)                      $erros[] = 'Número do quarto inválido.';
if (!in_array($tipo, $tiposValidos))  $erros[] = 'Tipo de quarto inválido.';
if ($preco <= 0)                      $erros[] = 'Preço deve ser maior que zero.';
if (mb_strlen($descricao) < 5)        $erros[] = 'Descrição muito curta.';

if ($erros) {
  // você pode trocar por retorno à view de cadastro com ?m=
  die('Erro no cadastro: ' . implode(' ', $erros));
}

/* 3) Insert */
try {
  // TENTE com a coluna 'ativo' (se existir)
  $sql = "INSERT INTO quartos (numero, tipo, preco, descricao, ativo)
          VALUES (:numero, :tipo, :preco, :descricao, :ativo)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':numero'    => $numero,
    ':tipo'      => $tipo,
    ':preco'     => $preco,
    ':descricao' => $descricao,
    ':ativo'     => $ativo,
  ]);

  header('Location: /landing-page-Hotel-principal/views/sucesso.php');
  exit;

} catch (PDOException $e) {
  // Se a coluna 'ativo' não existir no seu schema, descomente o bloco abaixo
  // para tentar novamente SEM a coluna 'ativo'.
  if (strpos($e->getMessage(), 'Unknown column') !== false) {
    try {
      $sql2 = "INSERT INTO quartos (numero, tipo, preco, descricao)
               VALUES (:numero, :tipo, :preco, :descricao)";
      $stmt2 = $pdo->prepare($sql2);
      $stmt2->execute([
        ':numero'    => $numero,
        ':tipo'      => $tipo,
        ':preco'     => $preco,
        ':descricao' => $descricao,
      ]);

      header('Location: /landing-page-Hotel-principal/views/sucesso.php');
      exit;
    } catch (PDOException $e2) {
      // trata duplicidade ou outros erros
      if ($e2->getCode() === '23000') {
        header('Location: /landing-page-Hotel-principal/views/quartoExistente.php');
        exit;
      }
      die('Erro ao cadastrar: ' . $e2->getMessage());
    }
  }

  // Trata duplicidade (unique em 'numero') ou demais erros
  if ($e->getCode() === '23000') {
    header('Location: /landing-page-Hotel-principal/views/quartoExistente.php');
    exit;
  }

  die('Erro ao cadastrar: ' . $e->getMessage());
}
