<?php
require_once __DIR__ . '/config/conexao.php';

$conexao = new Conexao();
$pdo = $conexao->getPdo();
$quartos = $pdo->query("SELECT id, numero, tipo FROM quartos WHERE ativo = 1 ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Projeto</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet" />
  
</head>
<!-- By: Adrian Hermes -->

<body>
  <div class="navbar-custom">
    <div class="banner">
      <div class="container-navbar">
        <img src="img/sharai.png" alt="sharai" class="logo-navbar" />
        <ul class="nav-links">
          <li><a href="#">Home</a></li>
          <li><a href="#">Post detail</a></li>
          <li><a href="#">Pages</a></li>
          <li><a href="#">Projects</a></li>
          <li><a href="#">Shortcodes</a></li>
          <li><a href="/hotel/views/login.php">Login</a></li>
        </ul>
      </div>

      <div class="container-reserva">
        <div class="reserva-heading">
          <h3 class="reserva-text">Reserva</h3>
        </div>
        <div class="reserva-fields">
          <div class="list-item list-item--entrada">
            <label for="">Entrada/Sa&iacute;da</label>
            <div class="input-grupo">
              <div class="input-wrapper">
                <span class="seta">&rarr;</span>
                <input type="date" class="reserva-entrada" placeholder="Entrada">
              </div>
              <div class="input-wrapper">
                <span class="seta">&rarr;</span>
                <input type="date" class="reserva-saida" placeholder="Sa&iacute;da">
              </div>
            </div>
          </div>

          <div class="list-item">
            <label for="">Quarto</label>
            <select name="reserva-quarto" id="" class="reserva-quarto" <?= empty($quartos) ? 'disabled' : '' ?>>
              <?php if (empty($quartos)): ?>
                <option value="">Nenhum quarto dispon&iacute;vel</option>
              <?php else: ?>
                <option value="" selected disabled>Selecione</option>
                <?php foreach ($quartos as $quarto): ?>
                  <option value="<?= (int) $quarto['id'] ?>">
                    No. <?= htmlspecialchars($quarto['numero']) ?> - <?= htmlspecialchars($quarto['tipo']) ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="list-item">
            <label for="">Adulto</label>
            <select name="reserva-adulto" id="" class="reserva-adulto">
              <option value="1">1</option>
              <option value="1">2</option>
              <option value="1">3</option>
              <option value="1">4</option>
            </select>
          </div>

          <div class="list-item">
            <label for="">Crian&ccedil;a</label>
            <select name="reserva-crianca" id="" class="reserva-crianca">
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
            </select>
          </div>

          <div class="list-item list-item--button">
            <button class="list-botom" id="abrir-modal-reserva" <?= empty($quartos) ? 'disabled' : '' ?>>ENVIAR</button>
          </div>

          <?php if (empty($quartos)): ?>
            <p class="reserva-alerta">Nenhum quarto dispon&iacute;vel no momento. Tente novamente em instantes.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="container-central">
    <div class="container-central-sobre">
      <h1 class="text-sobre1-central">Sobre</h1>
      <h2 class="text-sobre2-central">Sobre</h2>
      <div class="line-cetral"></div>
      <h3 class="text-info-central">
        We will be so proud to be our guest.Lorem Ipsum is simply dummy text
        of the printing.
      </h3>
      <p class="text-msg-central">
        Lorem Ipsum is simply dummy text of the printing and typesetting
        industry. Lorem Ipsum has been the typesetting industry's standard
        dummy text ever since the when.Lorem Ipsum is simply dummy text of
        the printing and typesetting industry.
      </p>
      <div class="cards-menus">
        <div class="card-menu">
          <div class="card-icon">
            <img src="img/service.png" alt="service" class="icon-service" />
          </div>
          <div class="card-info">
            <h4 class">Restaurante</h4>
            <p>Lorem ipsum dolor sit piscing sed nonmy</p>
          </div>
        </div>

        <div class="card-menu">
          <div class="card-icon">
            <img src="img/vela.png" alt="vela" class="icon-vela" />
          </div>
          <div class="card-info">
            <h4>Wellness & Spa</h4>
            <p>Lorem ipsum dolor sit piscing sed nonmy</p>
          </div>
        </div>

        <div class="card-menu">
          <div class="card-icon">
            <img src="img/wifi.png" alt="wifi" class="icon-wifi" />
          </div>
          <div class="card-info">
            <h4>Free Wifi</h4>
            <p>Lorem ipsum dolor sit piscing sed nonmy</p>
          </div>
        </div>

        <div class="card-menu">
          <div class="card-icon">
            <img src="img/cards.png" alt="carta" class="icon-carta" />
          </div>
          <div class="card-info">
            <h4>Espa&ccedil;o de jogos</h4>
            <p>Lorem ipsum dolor sit piscing sed nonmy</p>
          </div>
        </div>
      </div>

      <button class="more-info-button" data-sobre-modal>SAIBA MAIS
        <div class="info-traco"></div>
      </button>
    </div>

    <div class="cocontainer-central image">
      <img src="img/hotel-quarto.png" alt="Imagem do Quarto" class="image-quarto" />
    </div>
  </div>

  <div class="container-acomodacao">
    <div class="home-acomodacao">
      <h1 class="text-acomodacao-g">ACOMODA&Ccedil;&Otilde;ES</h1>
      <h3 class="text-acomodacao-p">Acomoda&ccedil;&otilde;es</h3>
      <div class="barra-acomodacao"></div>
    </div>

    <div class="menu-home-acomodacao">
      <ul class="links">
        <li><a href="#">TODOS</a></li>
        <li>/</li>
        <li><a href="#">CASAL</a></li>
        <li>/</li>
        <li><a href="#">SOLTEIRO</a></li>
        <li>/</li>
        <li><a href="#">SU&Iacute;TE</a></li>
      </ul>
    </div>



    <div class="acomodacoes-cards">
      <div class="acomodacao-card">
        <div class="acomodacao-img">
          <img src="img/casal01.png" alt="Casal 01" class="acomodacao-img">
          <span class="acomodacao-titulo">Casal 01</span>
        </div>
        <div class="acomodacao-info">
          <img src="img/Border.png" alt="borer" class="acomodacao-border">
          <div class="container-info">
            <div>
              <span class="acomodacao-preco">R$ 299,00/NOITE</span>
            </div>
            <div class="acomodacao-detalhes">
              <span> <img src="img/Vector.png" class="icon-info"> tamanho 30m&sup2; </span>
              <span> <img src="img/pessoa.png" class="icon-info"> Adultos: 3 </span>
            </div>
            <button
              class="acomodacao-btn"
              data-quarto-id="1"
              data-nome="Casal 01"
              data-imagem="img/casal01.png"
              data-area="30"
              data-adultos="3"
              data-preco="299.00"
              data-descricao="Suite casal com cama queen, enxoval premium e varanda privativa."
            >SAIBA MAIS</button>
          </div>
        </div>
      </div>
      <div class="acomodacao-card">
        <div class="acomodacao-img">
          <img src="img/solteiro.png" alt="Solteiro 01" class="acomodacao-img">
          <span class="acomodacao-titulo">Solteiro 01</span>
        </div>
        <div class="acomodacao-info">
          <img src="img/Border.png" alt="borer" class="acomodacao-border">
          <div class="container-info">
            <div>
              <span class="acomodacao-preco">R$ 199,00/NOITE</span>
            </div>
            <div class="acomodacao-detalhes">
              <span> <img src="img/Vector.png" class="icon-info"> tamanho 30m&sup2; </span>
              <span> <img src="img/pessoa.png" class="icon-info"> Adultos: 3 </span>
            </div>
            <button
              class="acomodacao-btn"
              data-quarto-id="3"
              data-nome="Solteiro 01"
              data-imagem="img/solteiro.png"
              data-area="28"
              data-adultos="1"
              data-preco="199.00"
              data-descricao="Quarto individual reservado e silencioso para viagens a trabalho."
            >SAIBA MAIS</button>
          </div>
        </div>
      </div>
      <div class="acomodacao-card">
        <div class="acomodacao-img">
          <img src="img/casal02.png" alt="Casal 02" class="acomodacao-img">
          <span class="acomodacao-titulo">Casal 02</span>
        </div>
        <div class="acomodacao-info">
          <img src="img/Border.png" alt="borer" class="acomodacao-border">
          <div class="container-info">
            <div>
              <span class="acomodacao-preco">R$ 299,00/NOITE</span>
            </div>
            <div class="acomodacao-detalhes">
              <span> <img src="img/Vector.png" class="icon-info"> tamanho 30m&sup2; </span>
              <span> <img src="img/pessoa.png" class="icon-info"> Adultos: 3 </span>
            </div>
            <button
              class="acomodacao-btn"
              data-quarto-id="28"
              data-nome="Casal 02"
              data-imagem="img/casal02.png"
              data-area="34"
              data-adultos="3"
              data-preco="299.00"
              data-descricao="Ambiente sofisticado com area de estar integrada e enxoval premium."
            >SAIBA MAIS</button>
          </div>
      </div>
    </div>
  </div>
  </div>

  <?php require_once __DIR__ . '/views/includes/footer.php'; ?>

<div id="info-reserva-modal" class="modal">
  <div class="modal-content modal-content-info">
    <span class="close" data-modal-close="info-reserva-modal">&times;</span>
    <h3 id="info-reserva-titulo">Detalhes da acomodacao</h3>
    <p class="modal-info-subtitle" id="info-reserva-subtitulo"></p>
    <div class="modal-info-body">
      <img src="" alt="Imagem da acomodacao" id="info-reserva-imagem" class="modal-info-cover" loading="lazy">
      <ul class="modal-info-list">
        <li><span class="modal-info-label">Quarto:</span> <span id="info-reserva-quarto" class="modal-info-value"></span></li>
        <li><span class="modal-info-label">Periodo:</span> <span id="info-reserva-periodo" class="modal-info-value"></span></li>
        <li><span class="modal-info-label">Tarifa:</span> <span id="info-reserva-tarifa" class="modal-info-value"></span></li>
        <li><span class="modal-info-label">Capacidade:</span> <span id="info-reserva-capacidade" class="modal-info-value"></span></li>
        <li><span class="modal-info-label">Status:</span> <span id="info-reserva-status" class="modal-info-value"></span></li>
        <li><span class="modal-info-label">Criada em:</span> <span id="info-reserva-criada" class="modal-info-value"></span></li>
      </ul>
    </div>
    <p class="modal-info-message" id="info-reserva-mensagem"></p>
    <div class="modal-info-actions">
      <button type="button" class="modal-submit" id="info-reserva-ver-reservas">Ir para a aba de reservas</button>
    </div>
  </div>
</div>

<div id="reserva-modal" class="modal">
  <div class="modal-content modal-content-reserva">
    <span class="close" data-modal-close="reserva-modal">&times;</span>
    <h3>Finalize sua reserva</h3>
    <p class="modal-description">Preencha seus dados para que possamos concluir o agendamento.</p>
    <form id="reserva-modal-form" action="/hotel/controllers/processarReservaPublica.php" method="POST" novalidate>
      <input type="hidden" name="quarto_id" id="reserva-modal-quarto">
      <input type="hidden" name="data_checkin" id="reserva-modal-checkin">
      <input type="hidden" name="data_checkout" id="reserva-modal-checkout">
      <input type="hidden" name="status" value="confirmada">

      <div class="modal-form-group">
        <label for="reserva-modal-nome">Nome completo</label>
        <input type="text" id="reserva-modal-nome" name="nome_cliente" required minlength="3" placeholder="Ex.: Maria Pereira">
      </div>

      <div class="modal-form-group">
        <label for="reserva-modal-email">E-mail</label>
        <input type="email" id="reserva-modal-email" name="email" required placeholder="exemplo@dominio.com">
      </div>

      <div class="modal-form-group">
        <label for="reserva-modal-cpf">CPF</label>
        <input type="text" id="reserva-modal-cpf" name="cpf" required placeholder="000.000.000-00" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}">
      </div>

      <div class="modal-form-group">
        <label for="reserva-modal-telefone">Telefone</label>
        <input type="text" id="reserva-modal-telefone" name="telefone" required placeholder="(99) 99999-9999">
      </div>

      <div class="modal-form-actions">
        <button type="submit" class="modal-submit">Confirmar Reserva</button>
        <button type="button" class="modal-cancel" data-modal-close="reserva-modal">Cancelar</button>
      </div>

      <p class="modal-feedback" id="reserva-modal-feedback" role="alert" aria-live="polite"></p>
    </form>
  </div>
</div>

<div id="sobre-modal" class="modal modal-sobre">
  <div class="modal-content">
    <span class="close" data-modal-close="sobre-modal">&times;</span>
    <div class="modal-sobre-header">
      <h3>Experiência Essentia</h3>
      <p class="modal-sobre-subtitle">Detalhes que transformam cada estadia.</p>
    </div>
    <div class="modal-sobre-body">
      <p>
        Relaxe em suítes com enxoval premium, assinatura aromática e atendimento dedicado.
        Para completar, oferecemos café da manhã artesanal, sala de jogos e concierge 24h.
      </p>
      <ul class="modal-sobre-lista">
        <li><strong>Gastronomia:</strong> Menu autoral e vinhos selecionados.</li>
        <li><strong>Spa &amp; Wellness:</strong> Ritual Essentia com terapias regenerativas.</li>
        <li><strong>Experiências:</strong> Tours privativos e agenda cultural personalizada.</li>
      </ul>
    </div>
    <div class="modal-sobre-footer">
      <button type="button" class="modal-sobre-fechar" data-modal-close="sobre-modal">
        Fechar
      </button>
    </div>
  </div>
</div>

<script src="scripit.js"></script>
</body>
</html>
