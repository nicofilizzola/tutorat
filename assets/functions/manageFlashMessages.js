import { gsap } from 'gsap'

function manageFlashMessages(message) {
   const flashContainer = document.createElement('div')
   flashContainer.classList.add('flash--container')

   const flashContainerBackground = document.createElement('div')
   flashContainerBackground.classList.add('background')

   const textContainer = document.createElement('div')
   textContainer.classList.add('text--container')

   const cross = document.createElement('span')
   cross.classList.add('cross')
   cross.innerHTML = '&times;'

   textContainer.appendChild(message)
   textContainer.appendChild(cross)
   flashContainer.appendChild(textContainer)
   flashContainer.appendChild(flashContainerBackground)

   document.body.appendChild(flashContainer)
}

export { manageFlashMessages }