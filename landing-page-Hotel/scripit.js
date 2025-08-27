let txt = document.getElementById("newsletter");
let btn = document.querySelector(".newsletter-form-horizontal button");

let modal = document.getElementById("newsletter-modal");
let modalMessage = document.getElementById("modal-message");
let modalClose = document.getElementById("modal-close");

btn.addEventListener("click", function(event) {
  event.preventDefault();
  let email = txt.value.trim();

  if (!validarEmail(email)) {
    showModal("Por favor, insira um e-mail válido.", false);
  } else {
    showModal("E-mail cadastrado com sucesso!", true);
  }
  txt.value = "";
});

function validarEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(String(email).toLowerCase());
}

function showModal(message, isSuccess) {
  modalMessage.textContent = message;
  modalMessage.style.color = isSuccess ? "#388e3c" : "#d32f2f";
  modal.style.display = "flex";
}

// Fecha o modal ao clicar no X
modalClose.onclick = function() {
  modal.style.display = "none";
};

// Fecha o modal ao clicar fora do conteúdo
window.onclick = function(event) {
  if (event.target === modal) {
    modal.style.display = "none";
  }
};

