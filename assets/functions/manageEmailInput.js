const manageEmailInputOnSubmit = (formSelector, emailInputSelector) => {
  document.querySelector(formSelector).addEventListener("submit", function (event) {
      event.preventDefault();

      const emailInput = document.querySelector(emailInputSelector);

      if (emailInput.value.indexOf('@') > -1) {
        if (emailInput.value.indexOf('@iut-tarbes.fr') == -1) {
          const keepedValue = emailInput.value.split('@')[0];
          emailInput.value = keepedValue + "@iut-tarbes.fr";
        }
      } else {
        const keepedValue = emailInput.value.split('@')[0];
        emailInput.value = keepedValue + "@iut-tarbes.fr";
      }

      event.target.submit();
    });
};

const manageEmailInputBeforeSubmit = (formSelector, emailInputSelector) => {
  document.querySelector(formSelector);
  const emailInput = document.querySelector(emailInputSelector);

  if (emailInput.value.indexOf('@') > -1) {
    const keepedValue = emailInput.value.split('@')[0];
    emailInput.value = keepedValue;
  }
};

export { manageEmailInputOnSubmit, manageEmailInputBeforeSubmit };