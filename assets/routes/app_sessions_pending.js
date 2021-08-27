import "../sass/utils/sessions_pending.scss";
import "../sass/utils/button.scss";
import "../sass/utils/form.scss";

let requests, roomSelects, validateButtons

if (document.querySelectorAll('.request--container')) {
   requests = document.querySelectorAll('.request--container')
   roomSelects = document.querySelectorAll('.request--container select')
   validateButtons = document.querySelectorAll('.request--container #validateButton')
}

roomSelects.forEach(select => {
   select.addEventListener('change', () => {
      if (select.value != "") {
         validateButtons.forEach(button => {
            if (button.dataset.id == select.dataset.id) {
               button.disabled = false
               button.classList.remove('disabled')
            }
         })
      } else {
         validateButtons.forEach(button => {
            if (button.dataset.id == select.dataset.id) {
               button.disabled = true
               button.classList.add('disabled')
            }
         })
      }
   })
})