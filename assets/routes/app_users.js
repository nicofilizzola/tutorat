import "../sass/utils/users.scss";

const validateFormButtons = document.querySelectorAll('.validate')
const refuseFormButtons = document.querySelectorAll('.refuse')
const deleteFormButtons = document.querySelectorAll('.delete')
const filterButtons = document.querySelectorAll('.filter button')

const usersListContainer = document.querySelector('.content--container')
const usersList = document.querySelectorAll('.userContent--container__user')
const tempUsersList = []

usersList.forEach(user => {
   tempUsersList.push(user)
   user.remove()
})

sortByFirstname(false)
// sortByLastname(true)
// sortByTutorHours(false)
// sortByMail(false)
// sortByRole(false)
// sortByStatut(true)


function sortByTutorHours(isDesc) {
   usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   tempUsersList.sort(function (a, b) {
      // return a.dataset.lastname.charAt(1).localeCompare(b.dataset.lastname.charAt(1), {ignorePunctuation: true})
      return a.dataset.tutorhours.localeCompare(b.dataset.tutorhours)
   })

   if (isDesc) {
      tempUsersList.reverse(function (a, b) {
         // return b.dataset.lastname.charAt(1).localeCompare(a.dataset.lastname.charAt(1), {ignorePunctuation: true})
         return b.dataset.tutorhours.localeCompare(a.dataset.tutorhours)
      })
   }

   tempUsersList.forEach(user => {
      usersListContainer.appendChild(user)
   })
}

function sortByFirstname(isDesc) {
   usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   tempUsersList.sort(function (a, b) {
      return a.dataset.firstname.charAt(1).localeCompare(b.dataset.firstname.charAt(1), {ignorePunctuation: true})
   })

   if (isDesc) {
      tempUsersList.reverse(function (a, b) {
         return a.dataset.firstname.charAt(1).localeCompare(b.dataset.firstname.charAt(1), {ignorePunctuation: true})
      })
   }

   tempUsersList.forEach(user => {
      usersListContainer.appendChild(user)
   })
}

function sortByLastname(isDesc) {
   usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   tempUsersList.sort(function (a, b) {
      return a.dataset.lastname.charAt(1).localeCompare(b.dataset.lastname.charAt(1), {ignorePunctuation: true})
   })

   if (isDesc) {
      tempUsersList.reverse(function (a, b) {
         return b.dataset.lastname.charAt(1).localeCompare(a.dataset.lastname.charAt(1), {ignorePunctuation: true})
      })
   }

   tempUsersList.forEach(user => {
      usersListContainer.appendChild(user)
   })
}

function sortByMail(isDesc) {
   usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   tempUsersList.sort(function (a, b) {
      return a.dataset.mail.charAt(1).localeCompare(b.dataset.mail.charAt(1), {ignorePunctuation: true})
   })

   if (isDesc) {
      tempUsersList.reverse(function (a, b) {
         return b.dataset.mail.charAt(1).localeCompare(a.dataset.mail.charAt(1), {ignorePunctuation: true})
      })
   }

   tempUsersList.forEach(user => {
      usersListContainer.appendChild(user)
   })
}

function sortByRole(isDesc) {
   usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   tempUsersList.sort(function (a, b) {
      return a.dataset.role.charAt(1).localeCompare(b.dataset.role.charAt(1), {ignorePunctuation: true})
   })

   if (isDesc) {
      tempUsersList.reverse(function (a, b) {
         return b.dataset.role.charAt(1).localeCompare(a.dataset.role.charAt(1), {ignorePunctuation: true})
      })
   }

   tempUsersList.forEach(user => {
      usersListContainer.appendChild(user)
   })
}

function sortByStatut(isDesc) {
   usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   tempUsersList.sort(function (a, b) {
      return a.dataset.statut - b.dataset.statut
   })

   if (isDesc) {
      tempUsersList.reverse(function (a, b) {
         return b.dataset.statut - a.dataset.statut
      })
   }

   tempUsersList.forEach(user => {
      usersListContainer.appendChild(user)
   })
}

const getCard = (button) => {
   return button.parentNode.parentNode
}

validateFormButtons.forEach(button => {
   button.addEventListener('mouseenter', () => {
      getCard(button).style.backgroundColor = 'rgba(22, 198, 12, .5)'
   })

   button.addEventListener('mouseleave', () => {
      getCard(button).style.backgroundColor = ''
   })
})

refuseFormButtons.forEach(button => {
   button.addEventListener('mouseenter', () => {
      getCard(button).style.backgroundColor = 'rgba(255, 58, 57, .5)'
   })

   button.addEventListener('mouseleave', () => {
      getCard(button).style.backgroundColor = ''
   })
})

deleteFormButtons.forEach(button => {
   button.addEventListener('mouseenter', () => {
      getCard(button).style.backgroundColor = 'rgba(226, 226, 226, .5)'
   })
   
   button.addEventListener('mouseleave', () => {
      getCard(button).style.backgroundColor = ''
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