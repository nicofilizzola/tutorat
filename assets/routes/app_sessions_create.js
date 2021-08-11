import "../sass/utils/sessions_create.scss";
import { fieldsError } from "../functions/checkFieldsErrors.js";

const switchOptionContainer = document.getElementById("switchOption--container");
const formContainer = document.querySelector(".form--container .form--content form");
const fields = [];

fieldsError(formContainer, fields);
const errorMessages = document.querySelectorAll(".form--content ul");

errorMessages.forEach(error => {
  error.addEventListener('click', () => {
    error.remove()
  })
});

const toggleLinkAndClassroom = (faceToFaceInputValue, linkInput) => {
  if (faceToFaceInputValue == 1) {
    // faceToFace
    linkInput.disabled = true;

    // switchOptionContainer.children[0].style.display = "none";
    // switchOptionContainer.children[0].hidden = true;
  } else {
    //remote
    linkInput.disabled = false;

    // switchOptionContainer.children[0].style.display = "flex";
    // switchOptionContainer.children[0].hidden = false;
  }
};

const manageFaceToFace = () => {
  const getFTFAndToggle = (faceToFaceInputs, link) => {
    const faceToFaceValue = faceToFaceInputs[0].checked ? 1 : 2;
    toggleLinkAndClassroom(faceToFaceValue, link);
  };

  const link = document.querySelector("#session_link");

  const faceToFaceInputs = [
    document.querySelector("#session_faceToFace_0"),
    document.querySelector("#session_faceToFace_1"),
  ];

  getFTFAndToggle(faceToFaceInputs, link);

  faceToFaceInputs.forEach((element) => {
    element.addEventListener("change", function () {
      getFTFAndToggle(faceToFaceInputs, link);
    });
  });
};

manageFaceToFace();