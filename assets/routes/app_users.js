import "../sass/utils/users.scss";
import manageSuccesMessages from "../functions/manageSuccesMessages.js";

const validateFormButtons = document.querySelectorAll('.validate')
const refuseFormButtons = document.querySelectorAll('.refuse')
const filterButtons = document.querySelectorAll('.filter button')

const getCard = (button) => {
   return button.parentNode.parentNode
}

validateFormButtons.forEach(button => {
   button.addEventListener('mouseenter', () => {
      getCard(button).style.borderColor = 'green'
   })

   button.addEventListener('mouseleave', () => {
      getCard(button).style.borderColor = '#000'
   })
})

refuseFormButtons.forEach(button => {
   button.addEventListener('mouseenter', () => {
      getCard(button).style.borderColor = 'red'
   })

   button.addEventListener('mouseleave', () => {
      getCard(button).style.borderColor = '#000'
   })
})

filterButtons.forEach(button => {
   // 1 => arrow down
   // 2 => arrow up
   // 3 => line
   button.addEventListener('click', () => {
      const tmpId = button.dataset.id
      if (button.dataset.active == 'line') {
         button.dataset.active = 'down'
         button.children[3].style.display = 'none'
         button.children[3].hidden = true
         button.children[1].style.display = 'block'
         button.children[1].hidden = false
      } else if (button.dataset.active == 'down') {
         button.dataset.active = 'up'
         button.children[1].style.display = 'none'
         button.children[1].hidden = true
         button.children[2].style.display = 'block'
         button.children[2].hidden = false
      } else {
         button.dataset.active = 'down'
         button.children[2].style.display = 'none'
         button.children[2].hidden = true
         button.children[1].style.display = 'block'
         button.children[1].hidden = false
      }

      filterButtons.forEach(button => {
         if (button.dataset.id != tmpId) {
            button.dataset.active = 'line'
            button.children[1].style.display = 'none'
            button.children[1].hidden = true
            button.children[2].style.display = 'none'
            button.children[2].hidden = true
            button.children[3].style.display = 'block'
            button.children[3].hidden = false
         }
      })
   })
})

document.querySelector('p.success')?manageSuccesMessages(document.querySelector("p.success")):null