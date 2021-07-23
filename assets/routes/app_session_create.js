const toggleLinkAndClassroom = (
  faceToFaceInputValue,
  linkInput,
  classroomInput
) => {
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
  const getFTFAndToggle = (faceToFaceInputs, link, classroom) => {
    const faceToFaceValue = faceToFaceInputs[0].checked ? 1 : 2;
    toggleLinkAndClassroom(faceToFaceValue, link, classroom);
  };

  const link = document.querySelector("#session_link");
  const classroom = document.querySelector("#session_classroom");
  faceToFaceInputs = [
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
