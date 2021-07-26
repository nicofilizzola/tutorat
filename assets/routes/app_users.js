import "../sass/utils/users.scss";

const validateFormButtons = document.querySelectorAll('.validate')
const refuseFormButtons = document.querySelectorAll('.refuse')

const getCard = (button) => {
   return button.parentNode.parentNode
}

validateFormButtons.forEach(button => {
   button.addEventListener('submit', (event) => {
      event.preventDefault();
      document.querySelector(`#js-user-validate-form-${getCard(button).dataset.id}`).submit();
   })

   button.addEventListener('mouseenter', () => {
      getCard(button).style.borderColor = 'green'
   })

   button.addEventListener('mouseleave', () => {
      getCard(button).style.borderColor = '#000'
   })
})

refuseFormButtons.forEach(button => {
   button.addEventListener('submit', (event) => {
      event.preventDefault();
      document.querySelector(`#js-user-cancel-form-${getCard(button).dataset.id}`).submit();
   })

   
   button.addEventListener('mouseenter', () => {
      getCard(button).style.borderColor = 'red'
   })

   button.addEventListener('mouseleave', () => {
      getCard(button).style.borderColor = '#000'
   })
})