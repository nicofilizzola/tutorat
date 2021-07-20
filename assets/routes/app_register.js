const manageAdminYearRole = () => {
  const roleInput = document.querySelector("#registration_form_role");
  const manageYearDisable = (roleInputValue) => {
    const yearInput = document.querySelector("#registration_form_year");

    if (roleInputValue == 3) {
      yearInput.disabled = true;
    } else {
      yearInput.disabled = false;
    }
  };

  manageYearDisable(roleInput.value); // if default value is admin when reloaded with error
  roleInput.addEventListener("change", function (event) {
    manageYearDisable(roleInput.value);
  });
};

manageAdminYearRole();
