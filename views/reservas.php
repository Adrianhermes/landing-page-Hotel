<?php 
require_once __DIR__ . '/../config/auth.php';
exigirLoginOuReservaPublica();
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../config/conexao.php';

// carrega quartos ativos para o <select>
$conexao = new Conexao();
$pdo = $conexao->getPdo();
$quartos = $pdo->query("SELECT id, numero, tipo FROM quartos WHERE ativo = 1 ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);
?>
<head>
  <link rel="stylesheet" href="/landing-page-Hotel-principal/assets/css/style.css">
</head>

<div class="container">
  <h2>Reservas</h2>

  <?php if (!usuarioLogado() && acessoReservaPublicaLiberado()): ?>
    <div class="alert alert-warning" role="alert">
      Esta e uma versao simplificada para visitantes. Faça login para acessar todas as funcoes do painel.
    </div>
  <?php endif; ?>

  <form action="/landing-page-Hotel-principal/controllers/processarReserva.php" method="POST" class="form-quarto" id="form-reserva">

    <!-- Quarto -->
    <div class="mb-3">
      <label for="quarto_id" class="form-label">Quarto:</label>
      <select class="form-control" id="quarto_id" name="quarto_id" required>
        <option value="">Selecione um quarto...</option>
        <?php foreach ($quartos as $q): ?>
          <option value="<?= (int)$q['id'] ?>">Nº <?= htmlspecialchars($q['numero']) ?> (<?= htmlspecialchars($q['tipo']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Nome Completo -->
    <div class="mb-3">
      <label for="nome_cliente" class="form-label">Nome Completo:</label>
      <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" required>
    </div>

    <!-- E-mail -->
    <div class="mb-3">
      <label for="email" class="form-label">E-mail:</label>
      <input type="email" class="form-control" id="email" name="email" required>
    </div>

    <!-- CPF -->
    <div class="mb-3">
      <label for="cpf" class="form-label">CPF:</label>
      <input type="text" class="form-control" id="cpf" name="cpf" required
             pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" placeholder="000.000.000-00">
    </div>

    <!-- Telefone -->
    <div class="mb-3">
      <label for="telefone" class="form-label">Telefone:</label>
      <input type="text" class="form-control" id="telefone" name="telefone" required
             pattern="\(\d{2}\)\s?\d{4,5}-\d{4}" placeholder="(99) 99999-9999">
    </div>

    <!-- Datas -->
    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="data_checkin" class="form-label">Check-in:</label>
        <input type="date" class="form-control" id="data_checkin" name="data_checkin" required>
      </div>
      <div class="col-md-6 mb-3">
        <label for="data_checkout" class="form-label">Check-out:</label>
        <input type="date" class="form-control" id="data_checkout" name="data_checkout" required>
      </div>
    </div>

    <div class="mb-3">
      <div id="aviso_disponibilidade" class="alert" style="display: none;"></div>
    </div>

    <!-- Status -->
    <div class="mb-3">
      <label for="status" class="form-label">Status:</label>
      <select class="form-control" id="status" name="status" required>
        <option value="confirmada">Confirmada</option>
        <option value="cancelada">Cancelada</option>
      </select>
    </div>

    <!-- Botões -->
    <div class="mb-3" style="display: flex; gap: 10px;">
      <button type="submit" class="btn btn-success">Cadastrar Reserva</button>
      <button type="button" class="btn btn-secondary" onclick="history.back()">Voltar</button>
    </div>

  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const tel = document.getElementById('telefone');
  const cpf = document.getElementById('cpf');
  const nome = document.getElementById('nome_cliente');
  const email = document.getElementById('email');
  const form = document.getElementById('form-reserva');
  const ckIn = document.getElementById('data_checkin');
  const ckOut = document.getElementById('data_checkout');
  const selectQuartoCadastro = document.getElementById('quarto_id');
  const avisoDisponibilidade = document.getElementById('aviso_disponibilidade');

  let reservasIndisponiveis = [];
  let disponibilidadeCarregando = false;

  function parseISODate(valor) {
    if (!valor || valor.indexOf('-') === -1) {
      return null;
    }
    const partes = valor.split('-').map(function(item) {
      return parseInt(item, 10);
    });
    const possuiInvalido = partes.length !== 3 || partes.some(function(numero) {
      return typeof Number.isNaN === 'function' ? Number.isNaN(numero) : isNaN(numero);
    });
    if (possuiInvalido) {
      return null;
    }
    return new Date(partes[0], partes[1] - 1, partes[2]);
  }

  function formatarDataBr(valor) {
    const data = parseISODate(valor);
    if (!data) {
      return valor;
    }
    const dia = String(data.getDate()).padStart(2, '0');
    const mes = String(data.getMonth() + 1).padStart(2, '0');
    const ano = data.getFullYear();
    return dia + '/' + mes + '/' + ano;
  }

  function exibirMensagemDisponibilidade(texto, variante = 'info') {
    if (!avisoDisponibilidade) {
      return;
    }
    if (!texto) {
      avisoDisponibilidade.style.display = 'none';
      avisoDisponibilidade.textContent = '';
      avisoDisponibilidade.className = 'alert';
      return;
    }
    avisoDisponibilidade.textContent = texto;
    avisoDisponibilidade.className = 'alert alert-' + variante;
    avisoDisponibilidade.style.display = 'block';
  }

  function atualizarListaReservas() {
    if (!selectQuartoCadastro || !selectQuartoCadastro.value) {
      exibirMensagemDisponibilidade('');
      return;
    }
    if (!reservasIndisponiveis.length) {
      exibirMensagemDisponibilidade('Nenhum bloqueio de datas para este quarto.', 'secondary');
      return;
    }
    const periodos = reservasIndisponiveis
      .map(function(reserva) {
        return formatarDataBr(reserva.data_checkin) + ' ate ' + formatarDataBr(reserva.data_checkout);
      })
      .join(' | ');
    exibirMensagemDisponibilidade('Periodos indisponiveis para este quarto: ' + periodos + '.', 'warning');
  }

  function haConflitoDatas(checkin, checkout) {
    if (!checkin || !checkout) {
      return false;
    }
    const inicioSelecionado = parseISODate(checkin);
    const fimSelecionado = parseISODate(checkout);
    if (!inicioSelecionado || !fimSelecionado) {
      return false;
    }
    return reservasIndisponiveis.some(function(reserva) {
      const inicio = parseISODate(reserva.data_checkin);
      const fim = parseISODate(reserva.data_checkout);
      if (!inicio || !fim) {
        return false;
      }
      return !(fimSelecionado <= inicio || inicioSelecionado >= fim);
    });
  }

  function validarDisponibilidade() {
    if (!selectQuartoCadastro || !selectQuartoCadastro.value) {
      exibirMensagemDisponibilidade('');
      return true;
    }

    atualizarListaReservas();

    if (!ckIn || !ckOut) {
      return true;
    }

    if (!ckIn.value || !ckOut.value) {
      ckIn.setCustomValidity('');
      ckOut.setCustomValidity('');
      return true;
    }

    const temConflito = haConflitoDatas(ckIn.value, ckOut.value);
    if (temConflito) {
      const mensagem = 'O quarto selecionado ja esta reservado no periodo informado.';
      ckIn.setCustomValidity(mensagem);
      ckOut.setCustomValidity(mensagem);
      exibirMensagemDisponibilidade(mensagem, 'danger');
      return false;
    }

    ckIn.setCustomValidity('');
    ckOut.setCustomValidity('');
    return true;
  }

  async function carregarReservas(quartoId) {
    reservasIndisponiveis = [];
    if (!selectQuartoCadastro) {
      return;
    }
    if (!quartoId) {
      exibirMensagemDisponibilidade('');
      return;
    }

    disponibilidadeCarregando = true;
    try {
      const resposta = await fetch('/landing-page-Hotel-principal/controllers/disponibilidadeQuarto.php?quarto_id=' + encodeURIComponent(quartoId), {
        cache: 'no-store',
      });
      if (!resposta.ok) {
        throw new Error('Resposta invalida');
      }
      const payload = await resposta.json();
      if (Array.isArray(payload.reservas)) {
        reservasIndisponiveis = payload.reservas;
      }
    } catch (erro) {
      const mensagemErro = 'Nao foi possivel verificar a disponibilidade do quarto selecionado. Tente novamente.';
      reservasIndisponiveis = [];
      if (ckIn) {
        ckIn.setCustomValidity(mensagemErro);
      }
      if (ckOut) {
        ckOut.setCustomValidity(mensagemErro);
      }
      exibirMensagemDisponibilidade(mensagemErro, 'danger');
      disponibilidadeCarregando = false;
      return;
    }
    disponibilidadeCarregando = false;
    validarDisponibilidade();
  }

  function validaDatas() {
    if (!ckIn || !ckOut) {
      return true;
    }
    const d1 = ckIn.value;
    const d2 = ckOut.value;
    ckIn.setCustomValidity('');
    ckOut.setCustomValidity('');
    if (d1 && d2 && d1 >= d2) {
      const mensagem = 'O check-out deve ser apos o check-in.';
      ckOut.setCustomValidity(mensagem);
      exibirMensagemDisponibilidade(mensagem, 'danger');
      return false;
    }
    return validarDisponibilidade();
  }

  if (tel) {
    tel.addEventListener('input', function(e) {
      let v = e.target.value.replace(/\D/g, '');
      if (v.length > 11) v = v.slice(0, 11);
      if (v.length > 6) {
        e.target.value = '(' + v.slice(0, 2) + ') ' + v.slice(2, 7) + '-' + v.slice(7);
      } else if (v.length > 2) {
        e.target.value = '(' + v.slice(0, 2) + ') ' + v.slice(2);
      } else if (v.length > 0) {
        e.target.value = '(' + v;
      }
    });
  }

  if (cpf) {
    cpf.addEventListener('input', function(e) {
      let v = e.target.value.replace(/\D/g, '');
      if (v.length > 11) v = v.slice(0, 11);
      if (v.length > 9) {
        e.target.value = v.slice(0, 3) + '.' + v.slice(3, 6) + '.' + v.slice(6, 9) + '-' + v.slice(9, 11);
      } else if (v.length > 6) {
        e.target.value = v.slice(0, 3) + '.' + v.slice(3, 6) + '.' + v.slice(6);
      } else if (v.length > 3) {
        e.target.value = v.slice(0, 3) + '.' + v.slice(3);
      } else {
        e.target.value = v;
      }
    });
  }

  if (nome) {
    nome.addEventListener('input', function(e) {
      e.target.value = e.target.value.toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
    });
  }

  if (email) {
    email.addEventListener('input', function(e) {
      e.target.value = e.target.value.toLowerCase();
    });
  }

  if (ckIn) {
    ckIn.addEventListener('change', function() {
      const valido = validaDatas();
      if (!valido) {
        ckIn.reportValidity();
      }
    });
  }

  if (ckOut) {
    ckOut.addEventListener('change', function() {
      const valido = validaDatas();
      if (!valido) {
        ckOut.reportValidity();
      }
    });
  }

  if (selectQuartoCadastro) {
    selectQuartoCadastro.addEventListener('change', function() {
      if (ckIn) {
        ckIn.setCustomValidity('');
      }
      if (ckOut) {
        ckOut.setCustomValidity('');
      }
      carregarReservas(this.value);
    });
  }

  if (form) {
    form.addEventListener('submit', function(e) {
      if (disponibilidadeCarregando) {
        e.preventDefault();
        e.stopPropagation();
        alert('Aguarde a validacao de disponibilidade do quarto selecionado.');
        return;
      }

      const datasValidas = validaDatas();
      if (!datasValidas || !form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        form.reportValidity();
      }
    });
  }

  if (selectQuartoCadastro && selectQuartoCadastro.value) {
    carregarReservas(selectQuartoCadastro.value);
  }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
