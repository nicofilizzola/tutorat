import '../sass/utils/home.scss';

import { gsap } from 'gsap'

let becomeTutorButton, cross, flashContainer = null
if (document.getElementById('becomeTutor')) {
   becomeTutorButton = document.getElementById('becomeTutor')
   cross = document.querySelector('.flash--container .cross')
   flashContainer = document.querySelector('.flash--container')
}

if (becomeTutorButton) {
   becomeTutorButton.addEventListener('click', () => {
      flashContainer.hidden = false
      gsap.to(flashContainer, .75, { opacity: 1, pointerEvents: 'all', ease: 'Power3.easeInOut' })    
   })
}

if (cross) {
   cross.addEventListener('click', () => {
      gsap.to(flashContainer, .75, { opacity: 0, pointerEvents: 'none', ease: 'Power3.easeInOut' })
      setTimeout(() => {
         flashContainer.hidden = true
      }, 750);
   })
}