const manageAdminYearRole = () => {
  const manageDisabledInputs = (roleInputValue) => {
    const yearInput = document.querySelector("#registration_form_year");
    const adminCodeInput = document.querySelector("#registration_form_adminCode");

    if (roleInputValue == 3) {
      yearInput.disabled = true;
      adminCodeInput.disabled = false;
    } else {
      yearInput.disabled = false;
      adminCodeInput.disabled = true;
    }
  };
  
  const roleInput = document.querySelector("#registration_form_role");

  manageDisabledInputs(roleInput.value); // if default value is admin when reloaded with error
  roleInput.addEventListener("change", function () {
    manageDisabledInputs(roleInput.value);
  });
};

manageAdminYearRole();
