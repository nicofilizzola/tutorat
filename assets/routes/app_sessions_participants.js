import "../sass/utils/sessions_participants.scss";
import "../sass/utils/button.scss";

import checkUserDevice from "../functions/checkUserDevice.js";

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