const newsletterInput = document.getElementById("newsletter");
const newsletterButton = document.querySelector(".newsletter-form-horizontal button");
const newsletterModal = document.getElementById("newsletter-modal");
const newsletterMessage = document.getElementById("modal-message");

const reservaButton = document.getElementById("abrir-modal-reserva");
const reservaModal = document.getElementById("reserva-modal");
const reservaForm = document.getElementById("reserva-modal-form");
const reservaFeedback = document.getElementById("reserva-modal-feedback");
const reservaModalDescription = reservaModal ? reservaModal.querySelector(".modal-description") : null;

const reservaModalQuarto = document.getElementById("reserva-modal-quarto");
const reservaModalCheckin = document.getElementById("reserva-modal-checkin");
const reservaModalCheckout = document.getElementById("reserva-modal-checkout");
const reservaModalNome = document.getElementById("reserva-modal-nome");
const reservaModalEmail = document.getElementById("reserva-modal-email");
const reservaModalCpf = document.getElementById("reserva-modal-cpf");
const reservaModalTelefone = document.getElementById("reserva-modal-telefone");

const infoModal = document.getElementById("info-reserva-modal");
const infoTitulo = document.getElementById("info-reserva-titulo");
const infoSubtitulo = document.getElementById("info-reserva-subtitulo");
const infoImagem = document.getElementById("info-reserva-imagem");
const infoQuarto = document.getElementById("info-reserva-quarto");
const infoPeriodo = document.getElementById("info-reserva-periodo");
const infoTarifa = document.getElementById("info-reserva-tarifa");
const infoCapacidade = document.getElementById("info-reserva-capacidade");
const infoStatus = document.getElementById("info-reserva-status");
const infoCriada = document.getElementById("info-reserva-criada");
const infoMensagem = document.getElementById("info-reserva-mensagem");
const infoReservasButton = document.getElementById("info-reserva-ver-reservas");

const saibaMaisButtons = document.querySelectorAll(".acomodacao-btn");

const inputEntrada = document.querySelector(".reserva-entrada");
const inputSaida = document.querySelector(".reserva-saida");
const selectQuarto = document.querySelector(".reserva-quarto");
const selectAdulto = document.querySelector(".reserva-adulto");
const selectCrianca = document.querySelector(".reserva-crianca");

const navbarContainer = document.querySelector(".navbar-custom .container-navbar");
let navbarPlaceholder = null;
let navbarOffsetTop = 0;
let navbarIsFixed = false;

if (navbarContainer) {
  navbarPlaceholder = document.createElement("div");
  navbarPlaceholder.className = "navbar-placeholder";
  const parentNode = navbarContainer.parentNode;
  parentNode.insertBefore(navbarPlaceholder, navbarContainer.nextSibling);

  const measureNavbar = () => {
    const rect = navbarContainer.getBoundingClientRect();
    navbarOffsetTop = rect.top + window.scrollY;
  };

  const activateSticky = () => {
    if (navbarIsFixed) return;
    const navHeight = navbarContainer.offsetHeight;
    navbarContainer.classList.add("is-fixed");
    navbarPlaceholder.classList.add("is-active");
    navbarPlaceholder.style.height = `${navHeight}px`;
    navbarIsFixed = true;
  };

  const deactivateSticky = () => {
    if (!navbarIsFixed) return;
    navbarContainer.classList.remove("is-fixed");
    navbarPlaceholder.classList.remove("is-active");
    navbarPlaceholder.style.height = "0px";
    navbarPlaceholder.style.width = "";
    navbarIsFixed = false;
  };

  const onScrollNavbar = () => {
    if (window.scrollY > navbarOffsetTop) {
      activateSticky();
    } else {
      deactivateSticky();
    }
  };

  measureNavbar();
  window.addEventListener("scroll", onScrollNavbar);

  window.addEventListener("resize", () => {
    const wasFixed = navbarIsFixed;
    deactivateSticky();
    measureNavbar();
    if (wasFixed && window.scrollY > navbarOffsetTop) {
      activateSticky();
    }
  });

  onScrollNavbar();
}

function openModal(element) {
  if (!element) return;
  element.style.display = "flex";
  document.body.classList.add("modal-open");
}

function closeModal(element) {
  if (!element) return;
  element.style.display = "none";

  const hasAnyOpen = Array.from(document.querySelectorAll(".modal")).some(
    (modalElement) => modalElement.style.display === "flex"
  );

  if (!hasAnyOpen) {
    document.body.classList.remove("modal-open");
  }
}

function closeModalById(id) {
  if (!id) return;
  const modalElement = document.getElementById(id);
  closeModal(modalElement);
}

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email).toLowerCase());
}

function formatDateForHuman(value) {
  if (!value || value.indexOf("-") === -1) return value;
  const [year, month, day] = value.split("-");
  if (!year || !month || !day) return value;
  return `${day}/${month}/${year}`;
}

function formatDateTimeForHuman(value) {
  if (!value) return "";
  const parts = value.trim().split(/\s+/);
  if (parts.length === 2) {
    const [datePart, timePart] = parts;
    const formattedDate = formatDateForHuman(datePart);
    const formattedTime = timePart.slice(0, 5);
    return `${formattedDate} ${formattedTime}`;
  }
  return formatDateForHuman(value);
}

function formatCurrencyBRL(value) {
  if (value === null || value === undefined || value === "") {
    return "";
  }
  const number = Number(value);
  if (!Number.isFinite(number)) {
    return String(value);
  }
  try {
    return number.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
  } catch (error) {
    const fixed = number.toFixed(2).replace(".", ",");
    return `R$ ${fixed}`;
  }
}

function formatCpf(value) {
  const digits = value.replace(/\D/g, "").slice(0, 11);
  if (digits.length <= 3) return digits;
  if (digits.length <= 6) return `${digits.slice(0, 3)}.${digits.slice(3)}`;
  if (digits.length <= 9) return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`;
  return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`;
}

function formatTelefone(value) {
  const digits = value.replace(/\D/g, "").slice(0, 11);
  if (digits.length === 0) return "";
  if (digits.length < 3) return `(${digits}`;
  if (digits.length < 7) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
  if (digits.length < 11) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
  return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
}

function capitalizeWords(value) {
  return value
    .toLowerCase()
    .replace(/\b\w/g, (char) => char.toUpperCase());
}

async function handleSaibaMais(button) {
  if (!button || !infoModal) {
    return;
  }

  const quartoIdRaw = button.dataset.quartoId;
  if (!quartoIdRaw) {
    return;
  }

  const nome = button.dataset.nome || "Acomodacao";
  const descricao = button.dataset.descricao || "";
  const imagem = button.dataset.imagem || "";
  const area = Number.parseInt(button.dataset.area || "", 10);
  const adultos = Number.parseInt(button.dataset.adultos || "", 10);
  const preco = Number.parseFloat(button.dataset.preco || "");

  if (infoTitulo) {
    infoTitulo.textContent = nome;
  }

  if (infoSubtitulo) {
    infoSubtitulo.textContent = descricao;
  }

  if (infoImagem) {
    if (imagem) {
      infoImagem.src = imagem;
      infoImagem.alt = `Acomodacao ${nome}`;
      infoImagem.style.display = "block";
    } else {
      infoImagem.removeAttribute("src");
      infoImagem.style.display = "none";
    }
  }

  if (infoQuarto) infoQuarto.textContent = "Carregando...";
  if (infoPeriodo) infoPeriodo.textContent = "Gerando periodo...";
  if (infoStatus) infoStatus.textContent = "Criando...";
  if (infoCriada) infoCriada.textContent = "-";
  if (infoMensagem) {
    infoMensagem.textContent = "Estamos criando automaticamente uma reserva desta acomodacao. Aguarde um instante...";
  }

  const detalhesCapacidade = [];
  if (Number.isFinite(adultos) && adultos > 0) {
    detalhesCapacidade.push(adultos === 1 ? "Ate 1 adulto" : `Ate ${adultos} adultos`);
  }
  if (Number.isFinite(area) && area > 0) {
    detalhesCapacidade.push(`${area} m2`);
  }
  if (infoCapacidade) {
    infoCapacidade.textContent = detalhesCapacidade.length > 0 ? detalhesCapacidade.join(" • ") : "-";
  }

  if (infoTarifa) {
    if (Number.isFinite(preco)) {
      infoTarifa.textContent = `${formatCurrencyBRL(preco)} por noite`;
    } else {
      infoTarifa.textContent = "-";
    }
  }

  openModal(infoModal);

  const originalDisabled = button.disabled;
  button.disabled = true;

  try {
    const payload = { quarto_id: Number.parseInt(quartoIdRaw, 10) };
    let response;
    let data;

    try {
      response = await fetch("/landing-page-Hotel-principal/controllers/criarReservaRapida.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
    } catch (networkError) {
      throw new Error("Nao foi possivel conectar ao servidor. Verifique sua conexao.");
    }

    try {
      data = await response.json();
    } catch (parseError) {
      throw new Error("Resposta inesperada ao criar a reserva.");
    }

    if (!response.ok || !data || data.ok !== true) {
      const mensagem = data && data.mensagem ? data.mensagem : "Nao foi possivel gerar a reserva. Tente novamente.";
      throw new Error(mensagem);
    }

    const reserva = data.reserva || {};
    const quarto = data.quarto || {};

    if (infoQuarto) {
      const partes = [];
      if (quarto.numero) partes.push(`No. ${quarto.numero}`);
      if (quarto.tipo) partes.push(quarto.tipo);
      infoQuarto.textContent = partes.length > 0 ? partes.join(" • ") : nome;
    }

    if (infoPeriodo) {
      const checkin = reserva.data_checkin ? formatDateForHuman(reserva.data_checkin) : "-";
      const checkout = reserva.data_checkout ? formatDateForHuman(reserva.data_checkout) : "-";
      infoPeriodo.textContent = `${checkin} ate ${checkout}`;
    }

    if (infoStatus) {
      const status = typeof reserva.status === "string" && reserva.status.length > 0
        ? reserva.status.charAt(0).toUpperCase() + reserva.status.slice(1)
        : "Confirmada";
      infoStatus.textContent = status;
    }

    if (infoCriada) {
      infoCriada.textContent = reserva.created_at ? formatDateTimeForHuman(reserva.created_at) : "-";
    }

    if (infoTarifa && !Number.isFinite(preco) && quarto.preco) {
      infoTarifa.textContent = `${formatCurrencyBRL(quarto.preco)} por noite`;
    }

    if (infoMensagem) {
      const idTexto = reserva.id ? `#${reserva.id}` : "";
      infoMensagem.textContent = `Reserva ${idTexto} criada automaticamente. Acesse a aba de reservas para acompanhar.`;
    }
  } catch (error) {
    if (infoMensagem) {
      const mensagemErro = error instanceof Error ? error.message : "Nao foi possivel gerar a reserva.";
      infoMensagem.textContent = mensagemErro;
    }
    if (infoPeriodo) infoPeriodo.textContent = "-";
    if (infoStatus) infoStatus.textContent = "Indisponivel";
    if (infoCriada) infoCriada.textContent = "-";
  } finally {
    button.disabled = originalDisabled;
  }
}

document.querySelectorAll("[data-modal-close]").forEach((trigger) => {
  trigger.addEventListener("click", () => closeModalById(trigger.dataset.modalClose));
});

window.addEventListener("click", (event) => {
  if (event.target.classList && event.target.classList.contains("modal")) {
    closeModal(event.target);
  }
});

window.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    document.querySelectorAll(".modal").forEach((modalElement) => {
      if (modalElement.style.display === "flex") {
        closeModal(modalElement);
      }
    });
  }
});

if (newsletterButton && newsletterInput && newsletterModal && newsletterMessage) {
  newsletterButton.addEventListener("click", (event) => {
    event.preventDefault();
    const email = newsletterInput.value.trim();

    if (!isValidEmail(email)) {
      newsletterMessage.textContent = "Por favor, insira um e-mail valido.";
      newsletterMessage.style.color = "#d32f2f";
    } else {
      newsletterMessage.textContent = "E-mail cadastrado com sucesso!";
      newsletterMessage.style.color = "#388e3c";
    }

    openModal(newsletterModal);
    newsletterInput.value = "";
  });
}

if (reservaButton && reservaModal && reservaForm && reservaModalQuarto && reservaModalCheckin && reservaModalCheckout) {
  reservaButton.addEventListener("click", (event) => {
    event.preventDefault();

    const missing = [];
    const entradaValue = inputEntrada ? inputEntrada.value : "";
    const saidaValue = inputSaida ? inputSaida.value : "";
    const quartoValue = selectQuarto ? selectQuarto.value : "";

    if (!entradaValue) missing.push("data de entrada");
    if (!saidaValue) missing.push("data de saida");
    if (!quartoValue) missing.push("quarto");

    if (missing.length > 0) {
      alert(`Por favor, informe ${missing.join(", ")} antes de continuar.`);
      return;
    }

    if (entradaValue && saidaValue) {
      const entradaDate = new Date(entradaValue);
      const saidaDate = new Date(saidaValue);

      if (Number.isFinite(entradaDate.getTime()) && Number.isFinite(saidaDate.getTime()) && entradaDate >= saidaDate) {
        alert("A data de saida deve ser posterior a data de entrada.");
        return;
      }
    }

    reservaModalQuarto.value = quartoValue;
    reservaModalCheckin.value = entradaValue;
    reservaModalCheckout.value = saidaValue;

    if (reservaModalDescription && selectQuarto) {
      const selectedOption = selectQuarto.options[selectQuarto.selectedIndex];
      const quartoText = selectedOption ? selectedOption.text.trim() : "";
      const adultosText = selectAdulto ? selectAdulto.value : "1";
      const criancasText = selectCrianca ? selectCrianca.value : "0";
      reservaModalDescription.textContent = `Reserva para o quarto ${quartoText} de ${formatDateForHuman(entradaValue)} ate ${formatDateForHuman(saidaValue)} para ${adultosText} adulto(s) e ${criancasText} crianca(s).`;
    }

    if (reservaFeedback) {
      reservaFeedback.textContent = "";
    }

    openModal(reservaModal);
  });
}

if (reservaModalCpf) {
  reservaModalCpf.addEventListener("input", (event) => {
    event.target.value = formatCpf(event.target.value);
  });
}

if (reservaModalTelefone) {
  reservaModalTelefone.addEventListener("input", (event) => {
    event.target.value = formatTelefone(event.target.value);
  });
}

if (reservaModalNome) {
  reservaModalNome.addEventListener("input", (event) => {
    event.target.value = capitalizeWords(event.target.value);
  });
}

if (reservaModalEmail) {
  reservaModalEmail.addEventListener("input", (event) => {
    event.target.value = event.target.value.toLowerCase();
  });
}

if (reservaForm && reservaFeedback && reservaModalCheckin && reservaModalCheckout) {
  reservaForm.addEventListener("submit", (event) => {
    reservaFeedback.textContent = "";

    if (!reservaForm.checkValidity()) {
      event.preventDefault();
      reservaForm.reportValidity();
      reservaFeedback.textContent = "Verifique os campos destacados antes de continuar.";
      return;
    }

    const checkinValue = reservaModalCheckin.value;
    const checkoutValue = reservaModalCheckout.value;

    if (checkinValue && checkoutValue) {
      const checkinDate = new Date(checkinValue);
      const checkoutDate = new Date(checkoutValue);

      if (Number.isFinite(checkinDate.getTime()) && Number.isFinite(checkoutDate.getTime()) && checkinDate >= checkoutDate) {
        event.preventDefault();
        reservaFeedback.textContent = "A data de saida deve ser posterior a data de entrada.";
        return;
      }
    }
  });
}

if (saibaMaisButtons.length > 0 && infoModal) {
  saibaMaisButtons.forEach((button) => {
    button.addEventListener("click", () => {
      handleSaibaMais(button);
    });
  });
}

if (infoReservasButton) {
  infoReservasButton.addEventListener("click", () => {
    window.location.href = "/landing-page-Hotel-principal/controllers/liberarReservaPublica.php";
  });
}

const urlParams = new URLSearchParams(window.location.search);
const reservaStatusParam = urlParams.get("reserva");

if (reservaStatusParam) {
  const mensagem = urlParams.get("msg");

  if (reservaStatusParam === "sucesso") {
    alert("Reserva enviada com sucesso! Em breve entraremos em contato.");
  } else if (reservaStatusParam === "erro") {
    alert(mensagem || "Nao foi possivel registrar a reserva. Tente novamente.");
  }

  urlParams.delete("reserva");
  urlParams.delete("msg");

  const novaQuery = urlParams.toString();
  const novaUrl = novaQuery ? `${window.location.pathname}?${novaQuery}` : window.location.pathname;
  window.history.replaceState({}, "", novaUrl);
}









