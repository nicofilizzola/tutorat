import '../sass/utils/form.scss';

const formContainer = document.querySelector(".form--container .form--content form");
const adminOrYearContainer = document.getElementById("adminOrYear--container");
const yearInput = document.getElementById("registration_form_year");
const adminCodeInput = document.getElementById("registration_form_adminCode");
const roleInput = document.getElementById("registration_form_role");
const SendFormBtn = document.querySelector(".form--btn");
const fields = []

const manageAdminYearRole = () => {
  const manageDisabledInputs = (roleInputValue) => {

    if (roleInputValue == 3) {
      adminOrYearContainer.children[0].style.display = 'flex'
      adminOrYearContainer.children[0].hidden = false
      adminOrYearContainer.children[1].style.display = 'none'
      adminOrYearContainer.children[1].hidden = true
      yearInput.disabled = true;
      adminCodeInput.disabled = false;
    } else {
      adminOrYearContainer.children[0].style.display = 'none'
      adminOrYearContainer.children[0].hidden = true
      adminOrYearContainer.children[1].style.display = 'flex'
      adminOrYearContainer.children[1].hidden = false
      yearInput.disabled = false;
      adminCodeInput.disabled = true;
    }
  };

  manageDisabledInputs(roleInput.value); // if default value is admin when reloaded with error
  roleInput.addEventListener("change", function () {
    manageDisabledInputs(roleInput.value);
  });
};

const fieldsError = () => {
  formContainer.children.forEach(field => {
    if (field.classList.contains('collapse')) {
      field.children.forEach(fieldChildren => {
        fields.push(fieldChildren)
        // console.log(fieldChildren)
      });
    } else {
      fields.push(field)
      // console.log(field)
    }
    // console.log(fields)
  });

  fields.forEach(field => {
    if (field.children[1] instanceof HTMLUListElement) {
      // console.log('sheeeeesh')
      field.children[2].classList.add('errorField')
    } else if (field.children[2]) {
      field.children[2].classList.remove('errorField')
    }
  });
}

SendFormBtn.addEventListener('click', () => {
  fieldsError()
})

fieldsError()
manageAdminYearRole();