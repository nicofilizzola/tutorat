import "../sass/utils/users.scss";

var distance = require('jaro-winkler');

const domCache = {
   validateFormButtons: document.querySelectorAll('.validate'),
   refuseFormButtons: document.querySelectorAll('.refuse'),
   deleteFormButtons: document.querySelectorAll('.delete'),
   filterButtons: document.querySelectorAll('button[data-active]'),

   usersListContainer: document.querySelector('.content--container'),
   usersList: document.querySelectorAll('.userContent--container__user'),

   searchBar: document.querySelector('.search--content input')
}

const sortedUsersList = []


domCache.usersList.forEach(user => {
   sortedUsersList.push(user)
   user.remove()
})

function sortByTutorHours(isDesc) {
   domCache.usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   sortedUsersList.sort(function (a, b) {
      return a.dataset.tutorhours.localeCompare(b.dataset.tutorhours)
   })

   if (isDesc) {
      sortedUsersList.reverse(function (a, b) {
         return b.dataset.tutorhours.localeCompare(a.dataset.tutorhours)
      })
   }

   sortedUsersList.forEach(user => {
      domCache.usersListContainer.appendChild(user)
   })
}

function sortByFirstname(isDesc) {
   domCache.usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   sortedUsersList.sort(function (a, b) {
      // console.log(a.dataset.firstname.charAt(0), b.dataset.firstname.charAt(0))
      console.log(a.dataset.firstname, b.dataset.firstname, a.dataset.firstname.charAt(0).localeCompare(b.dataset.firstname.charAt(0), {sensitivity: "base"}))
      return a.dataset.firstname.charAt(0).localeCompare(b.dataset.firstname.charAt(0), {ignorePunctuation: true})
   })

   if (isDesc) {
      sortedUsersList.reverse(function (a, b) {
         return a.dataset.firstname.charAt(0).localeCompare(b.dataset.firstname.charAt(0), {ignorePunctuation: true})
      })
   }

   sortedUsersList.forEach(user => {
      // console.log(user.dataset.firstname)
      domCache.usersListContainer.appendChild(user)
   })
}

function sortByLastname(isDesc) {
   domCache.usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   sortedUsersList.sort(function (a, b) {
      // console.log(a.dataset.firstname.charAt(0), b.dataset.firstname.charAt(0))
      return a.dataset.firstname.charAt(0).localeCompare(b.dataset.firstname.charAt(0), {ignorePunctuation: true})
   })

   if (isDesc) {
      sortedUsersList.reverse(function (a, b) {
         return a.dataset.firstname.charAt(0).localeCompare(b.dataset.firstname.charAt(0), {ignorePunctuation: true})
      })
   }

   sortedUsersList.sort(function (a, b) {
      return a.dataset.lastname.charAt(0).localeCompare(b.dataset.lastname.charAt(0), {ignorePunctuation: true})
   })

   if (isDesc) {
      sortedUsersList.reverse(function (a, b) {
         return b.dataset.lastname.charAt(0).localeCompare(a.dataset.lastname.charAt(0), {ignorePunctuation: true})
      })
   }

   sortedUsersList.forEach(user => {
      domCache.usersListContainer.appendChild(user)
   })
}

function sortByMail(isDesc) {
   domCache.usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   sortedUsersList.sort(function (a, b) {
      return a.dataset.mail.charAt(0).localeCompare(b.dataset.mail.charAt(0), {ignorePunctuation: true})
   })

   if (isDesc) {
      sortedUsersList.reverse(function (a, b) {
         return b.dataset.mail.charAt(0).localeCompare(a.dataset.mail.charAt(0), {ignorePunctuation: true})
      })
   }

   sortedUsersList.forEach(user => {
      domCache.usersListContainer.appendChild(user)
   })
}

function sortByRole(isDesc) {
   domCache.usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   sortedUsersList.sort(function (a, b) {
      return a.dataset.role.charAt(0).localeCompare(b.dataset.role.charAt(0), {ignorePunctuation: true})
   })

   if (isDesc) {
      sortedUsersList.reverse(function (a, b) {
         return b.dataset.role.charAt(0).localeCompare(a.dataset.role.charAt(0), {ignorePunctuation: true})
      })
   }

   sortedUsersList.forEach(user => {
      domCache.usersListContainer.appendChild(user)
   })
}

function sortByStatut(isDesc) {
   domCache.usersListContainer.children.forEach(user => {
      if (user.classList.contains('userContent--container__user')) {
         user.remove()
      }
   })

   sortedUsersList.sort(function (a, b) {
      return a.dataset.statut - b.dataset.statut
   })

   if (isDesc) {
      sortedUsersList.reverse(function (a, b) {
         return b.dataset.statut - a.dataset.statut
      })
   }

   sortedUsersList.forEach(user => {
      domCache.usersListContainer.appendChild(user)
   })
}

const getCard = (button) => {
   return button.parentNode.parentNode
}

domCache.validateFormButtons.forEach(button => {
   button.addEventListener('mouseenter', () => {
      getCard(button).style.backgroundColor = 'rgba(22, 198, 12, .5)'
   })

   button.addEventListener('mouseleave', () => {
      getCard(button).style.backgroundColor = ''
   })
})

domCache.refuseFormButtons.forEach(button => {
   button.addEventListener('mouseenter', () => {
      getCard(button).style.backgroundColor = 'rgba(255, 58, 57, .5)'
   })

   button.addEventListener('mouseleave', () => {
      getCard(button).style.backgroundColor = ''
   })
})

domCache.deleteFormButtons.forEach(button => {
   button.addEventListener('mouseenter', () => {
      getCard(button).style.backgroundColor = 'rgba(226, 226, 226, .5)'
   })
   
   button.addEventListener('mouseleave', () => {
      getCard(button).style.backgroundColor = ''
   })
})

switchUserSortList('down', 'statut')

function switchMarker(id) {
   domCache.filterButtons.forEach(filter => {
      // 1 => arrow down
      // 2 => arrow up
      // 3 => line
      const tmpId = id
      if (filter.dataset.active == 'line') {
         filter.children[1].style.display = 'block'
         filter.children[1].hidden = false
         filter.children[2].style.display = 'none'
         filter.children[2].hidden = true
         filter.children[3].style.display = 'none'
         filter.children[3].hidden = true
      } else if (filter.dataset.active == 'down') {
         filter.children[1].style.display = 'block'
         filter.children[1].hidden = false
         filter.children[2].style.display = 'none'
         filter.children[2].hidden = true
         filter.children[3].style.display = 'none'
         filter.children[3].hidden = true
      } else {
         filter.children[1].style.display = 'none'
         filter.children[1].hidden = true
         filter.children[2].style.display = 'block'
         filter.children[2].hidden = false
      }

      domCache.filterButtons.forEach(filter => {
         if (filter.dataset.id != tmpId) {
            filter.dataset.active = 'line'
            filter.children[1].style.display = 'none'
            filter.children[1].hidden = true
            filter.children[2].style.display = 'none'
            filter.children[2].hidden = true
            filter.children[3].style.display = 'block'
            filter.children[3].hidden = false
         }
      })
   })
}

function switchUserSortList(active, id) {   
   switchMarker(id)

   let isDesc = false
   active=='up'?isDesc=true:null
   
   switch (id) {
      case 'tutorHours':
         sortByTutorHours(isDesc)
         break;
      case 'firstname':
         sortByFirstname(isDesc)
         break;
      case 'lastname':
         sortByLastname(isDesc)
         break;
      case 'mail':
         sortByMail(isDesc)
         break;
      case 'role':
         sortByRole(isDesc)
         break;
      case 'statut':
         sortByStatut(isDesc)
         break;
      default:
         sortByStatut(isDesc)
         break;
   }

}

domCache.filterButtons.forEach(button => {
   button.addEventListener('click', () => {
      if (button.dataset.active == 'line') {
         button.dataset.active = 'down'
      } else if (button.dataset.active == 'down') {
         button.dataset.active = 'up'
      } else {
         button.dataset.active = 'down'
      }
      switchUserSortList(button.dataset.active, button.dataset.id)
   })
})

domCache.searchBar.addEventListener('keyup', () => {
   const search = domCache.searchBar.value
   
   sortedUsersList.forEach(user => {
      if (search.length > 0) {
         const firstname = distance(search, user.dataset.firstname, {caseSensitive: false})
         const lastname = distance(search, user.dataset.lastname, {caseSensitive: false})
         const mail = distance(search, user.dataset.mail, {caseSensitive: false})

         if (firstname > .6 || lastname > .6 || mail > .75) {
            user.hidden = false
            user.style.display = 'flex'
         } else {
            user.hidden = true
            user.style.display = 'none'
         }
      } else {
         user.hidden = false
         user.style.display = 'flex'
      }
   })
})