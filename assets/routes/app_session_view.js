import "../sass/utils/session_view.scss";

// import { gsap } from 'gsap'

// const domCache = {
// }

setTimeout(() => {
   const navSessionTitle = document.getElementById('navSessionTitle')
   const customNavPathContainer = document.querySelectorAll('.customNavPath--container span')

   customNavPathContainer.forEach(text => {
      if (!isNaN(parseInt(text.innerHTML))) {
         text.innerHTML = navSessionTitle.innerHTML.toLowerCase()
      }
   })

   navSessionTitle.remove()
}, 1)

