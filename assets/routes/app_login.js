import "../sass/utils/form.scss";
import { manageEmailInputOnSubmit, manageEmailInputBeforeSubmit } from "../functions/manageEmailInput.js";
import { fieldsError } from "../functions/checkFieldsErrors.js";

const formContainer = document.querySelector(".form--container .form--content form");
const fields = [];

fieldsError(formContainer, fields);
manageEmailInputOnSubmit("#login_form", "#inputEmail");
manageEmailInputBeforeSubmit("#login_form", "#inputEmail");