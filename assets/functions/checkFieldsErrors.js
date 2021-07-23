const fieldsError = (formContainer, fields) => {
   formContainer.children.forEach(field => {
      if (field.classList.contains('collapse') || field.classList.contains('mail--container')) {
         field.children.forEach(fieldChildren => {
            fields.push(fieldChildren)
         })
      } else {
         fields.push(field)
      }
   })

   fields.forEach((field) => {
      if (field.children[1] instanceof HTMLUListElement) {
         field.children[2].classList.add('errorField')
      } else if (field.children[2]) {
         field.children[2].classList.remove("errorField");
      }
   })
}

export { fieldsError }