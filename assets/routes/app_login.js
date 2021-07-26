import "../sass/utils/form.scss";
import { manageEmailInputOnSubmit, manageEmailInputBeforeSubmit } from "../functions/manageEmailInput.js";
import { fieldsError } from "../functions/checkFieldsErrors.js";

const formContainer = document.querySelector(".form--container .form--content form");
const fields = [];

fieldsError(formContainer, fields);
const errorMessages = document.querySelectorAll(".form--content ul");

errorMessages.forEach(error => {
   error.addEventListener('click', () => {
      error.remove()
   })
})

manageEmailInputOnSubmit("#login_form", "#inputEmail");
manageEmailInputBeforeSubmit("#login_form", "#inputEmail");