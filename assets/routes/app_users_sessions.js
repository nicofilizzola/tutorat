import checkUserDevice from "../functions/checkUserDevice.js";

let maxNavCustomPathLength
checkUserDevice()?maxNavCustomPathLength = 5:maxNavCustomPathLength = 15

setTimeout(() => {
   const navUserName = document.getElementById('userName')
   const customNavPathContainer = document.querySelectorAll('.customNavPath--container span')

   customNavPathContainer.forEach(customPath => {
      if (!isNaN(parseInt(customPath.innerHTML))) {
         const text = navUserName.innerHTML.toLowerCase()
         const textLength = text.length
         let lessText = text
         if (textLength > maxNavCustomPathLength) {
            lessText = text.slice(0, maxNavCustomPathLength) + '...'
         }
         customPath.innerHTML = lessText
      }
   })

   navUserName.remove()
}, 1)