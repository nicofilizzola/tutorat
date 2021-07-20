document
  .querySelector("#registration_form_role")
  .addEventListener("change", function (event) {
    const yearInput = document.querySelector("#registration_form_year");
    if (event.target.value == 3) {
      yearInput.disabled = true;
    } else {
      yearInput.disabled = false;
    }
  });
