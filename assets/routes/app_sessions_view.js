import "../sass/utils/sessions_view.scss";

import checkUserDevice from "../functions/checkUserDevice.js";

const buttons = document.querySelectorAll('button[data-action="delete_session"]')

const modalContainer = document.querySelector('.modal--container')
const modalYes = document.querySelector('.choice .yes')
const modalNo = document.querySelector('.choice .no')

let maxNavCustomPathLength
checkUserDevice()?maxNavCustomPathLength = 10:maxNavCustomPathLength = 15

setTimeout(() => {
   const navSessionTitle = document.getElementById('navSessionTitle')
   const customNavPathContainer = document.querySelectorAll('.customNavPath--container span')

   customNavPathContainer.forEach(customPath => {
      if (!isNaN(parseInt(customPath.innerHTML))) {
         const text = navSessionTitle.innerHTML.toLowerCase()
         const textLength = text.length
         let lessText = text
         if (textLength > maxNavCustomPathLength) {
            lessText = text.slice(0, maxNavCustomPathLength) + '...'
         }
         customPath.innerHTML = lessText
      }
   })

   navSessionTitle.remove()
}, 1)


buttons.forEach(button => {
   button.addEventListener('click', () => {
      modalContainer.style.pointerEvents = 'all'
      modalContainer.style.opacity = 1

      modalYes.addEventListener('click', () => {
         document.querySelector(`#js-session-delete-${ button.dataset.id }`).submit()
      })
   })
})

modalNo.addEventListener('click', () => {
   modalContainer.style.opacity = 0
   modalContainer.style.pointerEvents = 'none'
})