import { gsap } from 'gsap'

function manageSuccesMessages(message) {
   const successContainer = document.createElement('div')
   successContainer.classList.add('success--container')

   const successContainerBackground = document.createElement('div')
   successContainerBackground.classList.add('background')

   const textContainer = document.createElement('div')
   textContainer.classList.add('text--container')

   textContainer.appendChild(message)
   successContainer.appendChild(textContainer)
   successContainer.appendChild(successContainerBackground)

   document.body.appendChild(successContainer)

   gsap.to(successContainer, .75, { opacity: 0, ease: 'Power3.easeInOut', delay: 2 })
   setTimeout(() => {
      successContainer.remove()
   }, 2750);
}

export { manageSuccesMessages }