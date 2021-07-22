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

export default manageEmailInputOnSubmit;
