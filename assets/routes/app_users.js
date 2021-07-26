import "../sass/utils/users.scss";

const validateFormButtons = document.querySelectorAll('.validate')
const refuseFormButtons = document.querySelectorAll('.refuse')

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