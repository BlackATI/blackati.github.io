const isFirefox = navigator.userAgent.toLowerCase().indexOf("firefox") > -1;

const correctMail = "blackati@github.io";
const correctPass = "Aa123456!";

const MESSAGE_TEXT = {
  // !!! взял на себя смелость исправить статус valid на succesful
  // Потому что valid могут быть любые данные, соответствующие валидатору,
  // а successful - только корректная пара логин/пароль
  successful: 'Авторизация прошла успешно', 
  invalid: "Нужно исправить проблему валидации",
  fail: "Такого логина или пароля нет",
  sendMail: "Успешно отправили пароль на e-mail",
};

console.info("информирование");
console.warn("предупреждение");
console.error("ошибка");

const loginButton = document.getElementById("loginButton");
const mailInput = document.getElementById("mail");
const passInput = document.getElementById("pass");
const form = document.getElementById("form");
const messageContainer = document.getElementById("message");
const messageHeader = document.getElementById("messageHeader");
const exitMessageButton = document.getElementById("exitMessageButton");

const forgotForm = document.getElementById("forgotForm");
const forgotEmailButton = document.getElementById("forgotEmailButton");
const restoreEmailButton = document.getElementById("restoreEmailButton");
const exitRestoreButton = document.getElementById("exitRestoreButton");
const mailForgotInput = document.getElementById("mailForgot");

loginButton.addEventListener("click", submitForm);
exitMessageButton.addEventListener("click", exitMessage);
mailInput.addEventListener("input", inputHandler);
mailInput.addEventListener("input", resize);
passInput.addEventListener("input", inputHandler);
forgotEmailButton.addEventListener("click", openForgotForm);
exitRestoreButton.addEventListener("click", exitMessage);
restoreEmailButton.addEventListener("click", restoreEmail);

function submitForm(e) {
  e.preventDefault();
  if (isFirefox) {
    return;
  }

  // !!! Сперва проверим, что соблюдены формальные требования - имейл валиден и пароль не пуст
  if (!validateEmail(mailInput.value) && passInput.value.length > 0) {
    loginButton.disabled = true;
    showMessage(MESSAGE_TEXT.invalid);
    return;
  }

  // !!! Теперь проверяем логин/пароль 
  if (mailInput.value === correctMail && passInput.value === correctPass) {
    showMessage(MESSAGE_TEXT.successful);
  } else {
    showMessage(MESSAGE_TEXT.fail);
  }
}

function showMessage(messageText = "") {
  form.style.display = "none";
  forgotForm.style.display = "none";
  messageContainer.style.display = "block";
  messageHeader.innerText = messageText;
}

function exitMessage(e) {
  e.preventDefault();
  mailInput.value = "";
  passInput.value = "";
  form.style.display = "flex";
  messageContainer.style.display = "none";
  forgotForm.style.display = "none";
}

function inputHandler() {
  loginButton.disabled = !mailInput.value || !passInput.value;
}

function openForgotForm(e) {
  e.preventDefault();
  form.style.display = "none";
  forgotForm.style.display = "flex";
}

function restoreEmail(e) {
  e.preventDefault();

  // !!! Проверим эмейл, прежде чем отправлять письмо на восстановление
  if (!validateEmail(mailForgotInput.value)) {
    showMessage(MESSAGE_TEXT.invalid);
    return;
  }
  
  showMessage(MESSAGE_TEXT.sendMail);
}

function resize() {
  this.style.width = `${this.value.length * 10}px`;
}

// !!! Функция валидации эмейл адреса на основе регулярного выражения
function validateEmail(email) {
  const emailPattern = new RegExp(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,20})+$/)
  return emailPattern.test(email)
}
