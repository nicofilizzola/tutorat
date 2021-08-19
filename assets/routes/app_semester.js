import "../sass/utils/form.scss";
import "../sass/utils/users.scss";
import "../sass/utils/semesters.scss";

const deleteFormButtons = document.querySelectorAll('.delete')
const yearSelects = document.querySelectorAll('select[data-year]')

const getCard = (button) => {
   return button.parentNode.parentNode
}

deleteFormButtons.forEach(button => {
   button.addEventListener('mouseenter', () => {
      getCard(button).style.backgroundColor = 'rgba(226, 226, 226, .5)'
   })
   
   button.addEventListener('mouseleave', () => {
      getCard(button).style.backgroundColor = ''
   })
})

yearSelects[0].addEventListener('change', () => {
   for (let i = 0; i < yearSelects[1].children.length; i++) {
      if (yearSelects[0].value >= yearSelects[1].children[i].value) {
         yearSelects[1].children[i].disabled = true
         yearSelects[1].selectedIndex = i+1
      } else {
         yearSelects[1].children[i].disabled = false
      }
   }
})