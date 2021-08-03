import "../sass/utils/session_create.scss";
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

const toggleLinkAndClassroom = (faceToFaceInputValue, linkInput, classroomInput) => {
  if (faceToFaceInputValue == 1) {
    // faceToFace
    linkInput.disabled = true;
    classroomInput.disabled = false;

    switchOptionContainer.children[0].style.display = "none";
    switchOptionContainer.children[0].hidden = true;
    switchOptionContainer.children[1].style.display = "flex";
    switchOptionContainer.children[1].hidden = false;
  } else {
    //remote
    linkInput.disabled = false;
    classroomInput.disabled = true;

    switchOptionContainer.children[0].style.display = "flex";
    switchOptionContainer.children[0].hidden = false;
    switchOptionContainer.children[1].style.display = "none";
    switchOptionContainer.children[1].hidden = true;
  }
};

const manageFaceToFace = () => {
  const getFTFAndToggle = (faceToFaceInputs, link, classroom) => {
    const faceToFaceValue = faceToFaceInputs[0].checked ? 1 : 2;
    toggleLinkAndClassroom(faceToFaceValue, link, classroom);
  };

  const link = document.querySelector("#session_link");
  const classroom = document.querySelector("#session_classroom");

  const faceToFaceInputs = [
    document.querySelector("#session_faceToFace_0"),
    document.querySelector("#session_faceToFace_1"),
  ];

  getFTFAndToggle(faceToFaceInputs, link, classroom);

  faceToFaceInputs.forEach((element) => {
    element.addEventListener("change", function () {
      getFTFAndToggle(faceToFaceInputs, link, classroom);
    });
  });
};

manageFaceToFace();