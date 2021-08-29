import "../sass/utils/form.scss";
import "../sass/utils/users.scss";
import "../sass/utils/classrooms.scss";

const deleteFormButtons = document.querySelectorAll('.delete')

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

deleteFormButtons.forEach(button => {
   button.addEventListener('click', () => {
      modalContainer.style.pointerEvents = 'all'
      modalContainer.style.opacity = 1

      modalYes.addEventListener('click', () => {
         document.querySelector(`#js-classroom-delete-form-${ button.dataset.id }`).submit()
      })
   })
})

modalNo.addEventListener('click', () => {
   modalContainer.style.opacity = 0
   modalContainer.style.pointerEvents = 'none'
})