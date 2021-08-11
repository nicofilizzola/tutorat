import "../sass/utils/add_subject.scss";
import "../sass/utils/form.scss";
import "../sass/utils/users.scss";

const deleteFormButtons = document.querySelectorAll('.delete')

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