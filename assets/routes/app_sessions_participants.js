import "../sass/utils/sessions_participants.scss";
import "../sass/utils/button.scss";

import checkUserDevice from "../functions/checkUserDevice.js";

const form = document.querySelector('form')

const modalContainer = document.querySelector('.modal--container')
const modalYes = document.querySelector('.choice .yes')
const modalNo = document.querySelector('.choice .no')

let maxNavCustomPathLength
checkUserDevice()?maxNavCustomPathLength = 5:maxNavCustomPathLength = 15

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