import "../sass/utils/form.scss";
import { manageEmailInputOnSubmit, manageEmailInputBeforeSubmit } from "../functions/manageEmailInput.js";
import { fieldsError } from "../functions/checkFieldsErrors.js";

const formContainer = document.querySelector(".form--container .form--content form");
const adminOrYearContainer = document.getElementById("adminOrYear--container");
const yearInput = document.getElementById("registration_form_year");
const adminCodeInput = document.getElementById("registration_form_adminCode");
const roleInput = document.getElementById("registration_form_role");
const fields = [];

const manageAdminYearRole = () => {
  const manageDisabledInputs = (roleInputValue) => {
    if (roleInputValue == 3) {
      adminOrYearContainer.children[0].style.display = "flex";
      adminOrYearContainer.children[0].hidden = false;
      adminOrYearContainer.children[1].style.display = "none";
      adminOrYearContainer.children[1].hidden = true;
      yearInput.disabled = true;
      adminCodeInput.disabled = false;
    } else {
      adminOrYearContainer.children[0].style.display = "none";
      adminOrYearContainer.children[0].hidden = true;
      adminOrYearContainer.children[1].style.display = "flex";
      adminOrYearContainer.children[1].hidden = false;
      yearInput.disabled = false;
      adminCodeInput.disabled = true;
    }
  };

  manageDisabledInputs(roleInput.value); // if default value is admin when reloaded with error
  roleInput.addEventListener("change", function () {
    manageDisabledInputs(roleInput.value);
  });
};

fieldsError(formContainer, fields);
manageAdminYearRole();
manageEmailInputOnSubmit("#registration_form", "#registration_form_email");
manageEmailInputBeforeSubmit("#registration_form", "#registration_form_email");