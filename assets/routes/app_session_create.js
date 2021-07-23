const toggleLinkAndClassroom = (faceToFaceInputValue, linkInput, classroomInput) => {
  if (faceToFaceInputValue == 1) {
    // faceToFace
    linkInput.disabled = true;
    classroomInput.disabled = false;
  } else {
    //remote
    linkInput.disabled = false;
    classroomInput.disabled = true;
  }
};

const manageFaceToFace = () => {
  const faceToFaceInput = document.querySelector("#session_faceToFace");
  const link = document.querySelector("#session_link");
  const classroom = document.querySelector("#session_classroom");
  toggleLinkAndClassroom(faceToFaceInput.value, link, classroom); // onload

  faceToFaceInput.addEventListener("change", function (event) {
    toggleLinkAndClassroom(faceToFaceInput.value, link, classroom);
  });
};

manageFaceToFace();
