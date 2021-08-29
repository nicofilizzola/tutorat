import "../sass/utils/form.scss";
import { manageEmailInputOnSubmit, manageEmailInputBeforeSubmit } from "../functions/manageEmailInput.js";
import { fieldsError } from "../functions/checkFieldsErrors.js";

const formContainer = document.querySelector(".form--container .form--content form");
const switchOptionContainer = document.getElementById("switchOption--container");
const yearInput = document.getElementById("registration_form_year");
const adminCodeInput = document.getElementById("registration_form_adminCode");
const roleInput = document.querySelector("select#registration_form_role");
const fields = [];

console.log(roleInput)

fieldsError(formContainer, fields);
const errorMessages = document.querySelectorAll(".form--content ul");

errorMessages.forEach(error => {
  error.addEventListener('click', () => {
    error.remove()
  })
});

const manageAdminYearRole = () => {
  const manageDisabledInputs = (roleInputValue) => {
    if (roleInputValue == 4) {
      switchOptionContainer.children[0].style.display = "flex";
      switchOptionContainer.children[0].hidden = false;
      switchOptionContainer.children[1].style.display = "none";
      switchOptionContainer.children[1].hidden = true;
      yearInput.disabled = true;
      adminCodeInput.disabled = false;
    } else {
      switchOptionContainer.children[0].style.display = "none";
      switchOptionContainer.children[0].hidden = true;
      switchOptionContainer.children[1].style.display = "flex";
      switchOptionContainer.children[1].hidden = false;
      yearInput.disabled = false;
      adminCodeInput.disabled = true;
    }
  };

  manageDisabledInputs(roleInput.value); // if default value is admin when reloaded with error
};

roleInput.addEventListener("change", function () {
  manageAdminYearRole();
})

manageAdminYearRole();
manageEmailInputOnSubmit("#registration_form", "#registration_form_email");
manageEmailInputBeforeSubmit("#registration_form", "#registration_form_email");