const manageEmailInputOnSubmit = (formSelector, emailInputSelector) => {
  document
    .querySelector(formSelector)
    .addEventListener("submit", function (event) {
      event.preventDefault();

      const emailInput = document.querySelector(emailInputSelector);
      emailInput.value = emailInput.value + "@iut-tarbes.fr";

      event.target.submit();
    });
};

const manageEmailInputBeforeSubmit = (formSelector, emailInputSelector) => {
  document.querySelector(formSelector);
  const emailInput = document.querySelector(emailInputSelector);

  if (emailInput.value.indexOf('@') > -1) {
    emailInput.value = emailInput.value.replace('@iut-tarbes.fr', '');
  }
};

export { manageEmailInputOnSubmit, manageEmailInputBeforeSubmit };