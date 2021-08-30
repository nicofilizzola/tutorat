import "../sass/utils/form.scss";
import "../sass/utils/users.scss";
import "../sass/utils/semesters.scss";

const deleteFormButtons = document.querySelectorAll('.delete')
const yearSelects = document.querySelectorAll('select[data-year]')

const buttons = document.querySelectorAll('.manage utton')
const form = document.querySelector('.form--content form')

const modalContainer = document.querySelector('.modal--container')
const modalYes = document.querySelector('.choice .yes')
const modalNo = document.querySelector('.choice .no')

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

buttons.forEach(button => {
   button.addEventListener('click', () => {
      modalContainer.style.pointerEvents = 'all'
      modalContainer.style.opacity = 1

      modalYes.addEventListener('click', () => {
         document.querySelector(`#js-semester-delete-form-${ button.dataset.id }`).submit()
      })
   })
})

form.addEventListener('submit', (event) => {
   event.preventDefault()

   modalContainer.style.pointerEvents = 'all'
   modalContainer.style.opacity = 1

   modalYes.addEventListener('click', () => {
      event.target.submit();
   })
})

modalNo.addEventListener('click', () => {
   modalContainer.style.opacity = 0
   modalContainer.style.pointerEvents = 'none'
})