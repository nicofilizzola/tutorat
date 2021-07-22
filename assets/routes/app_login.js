import "../sass/utils/form.scss";

const formContainer = document.querySelector(
  ".form--container .form--content form"
);
const SendFormBtn = document.querySelector(".form--btn");
const fields = [];

const fieldsError = () => {
  formContainer.children.forEach((field) => {
    if (field.classList.contains("collapse")) {
      field.children.forEach((fieldChildren) => {
        fields.push(fieldChildren);
        // console.log(fieldChildren)
      });
    } else {
      fields.push(field);
      // console.log(field)
    }
    // console.log(fields)
  });

  fields.forEach((field) => {
    if (field.children[1] instanceof HTMLUListElement) {
      // console.log('sheeeeesh')
      field.children[2].classList.add("errorField");
    } else if (field.children[2]) {
      field.children[2].classList.remove("errorField");
    }
  });
};

SendFormBtn.addEventListener("click", () => {
  fieldsError();
});

import manageEmailInputOnSubmit from "../functions/manageEmailInputOnSubmit";

fieldsError();
manageEmailInputOnSubmit("#login_form", "#inputEmail");
