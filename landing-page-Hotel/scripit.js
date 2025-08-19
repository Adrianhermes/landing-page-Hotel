let txt = document.getElementById("newsletter");
let btn = document.querySelector(".newsletter-form-horizontal button");
let errorMessage = document.querySelector(".error-message");
let sucessMessage = document.querySelector(".sucess-message");

// Remover o segundo addEventListener incorreto
// btn.addEventListener ("click", function()) { ... }

btn.addEventListener("click", clicar);

// Função que dispara quando clicam no botão
function clicar(event) {
  event.preventDefault(); // impede recarregar a página

  let email = txt.value.trim();
  errorMessage.style.display = "none";
  sucessMessage.style.display = "none";

  if (!validarEmail(email)) {
    errorMessage.textContent = "Por favor, insira um e-mail válido.";
    errorMessage.style.display = "block";
    sucessMessage.style.display = "none";
  } else {
    sucessMessage.textContent = "E-mail cadastrado com sucesso!";
    sucessMessage.style.display = "block";
    errorMessage.style.display = "none";
  }
  txt.value = ""; // Limpa o campo de entrada
}

// Função de validação
function validarEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(String(email).toLowerCase());
}
