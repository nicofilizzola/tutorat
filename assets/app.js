/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './sass/main.scss';

// start the Stimulus application
import './bootstrap';

import { gsap } from 'gsap'
import luge from '@waaark/luge'

const domCache = {
   menuBackground: document.querySelector('.menu--background'),
   menuBurger: document.querySelector('.menuBurger'),
   menuBurgerLines: document.querySelectorAll('.menuBurger span'),
   menuBurgerCrossLine1: document.querySelector('.cross__1'),
   menuBurgerCrossLine2: document.querySelector('.cross__2'),
   hoveredItems: document.querySelectorAll('.hoveredItems'),
   cursor: document.querySelector('.cursor')
}

let state = {
   isMenuOpen: false,
   isMouseHovering: false,
}

let posX = 0
let posY = 0
let lowestElapsedTime = 0

// menuBurger.addEventListener('mouseover', (mouse) => {
//    menuBackground.style.left = `${mouse.x}px`
//    menuBackground.style.top = `${mouse.y}px`
// })
// menuBurger.addEventListener('mouseleave', () => {
//    menuBurgerLines.forEach(line => {
//       line.classList.toggle('menuHovered')
//       line.classList.toggle('menuNotHovered')
//    })
// })

document.addEventListener('mousemove', (mouse) => {
   posX = mouse.pageX
   posY = mouse.pageY
})

domCache.hoveredItems.forEach(item => {
   item.addEventListener('mouseenter', (e) => {
      state.isMouseHovering = true
      const rect = item.getBoundingClientRect()
      const itemPosX = rect.x + rect.width - 20
      const itemPosY = rect.y + 10

      gsap.to(domCache.cursor, .5, {
         css: {
            left: itemPosX,
            top: itemPosY
         },
         ease: 'Power3.easeInOut'
      })
   })

   item.addEventListener('mouseleave', (e) => {
      state.isMouseHovering = false
   })
})

domCache.menuBurger.addEventListener('mouseenter', () => {
   if (!state.isMenuOpen) {
      gsap.to(domCache.menuBurgerLines, .1, {
         stagger: { each: .05, y: 50},
         ease: 'Expo3.easeOut'
      })
   }
})

domCache.menuBurger.addEventListener('mouseleave', () => {
   if (!state.isMenuOpen) {
      gsap.to(domCache.menuBurgerLines, .1, {
         stagger: { each: .05, y: 0},
         ease: 'Expo3.easeOut'
      })
   }
})

domCache.menuBurger.addEventListener('click', () => {
   if (!state.isMenuOpen) {
      state.isMenuOpen = true
      domCache.menuBackground.classList.add('menuActive')

      gsap.to(domCache.cursor, .25, {
         backgroundColor: '#fff',
         ease: 'Power3.easeOut',
      })

      gsap.to(domCache.menuBurgerLines, .75, {
         backgroundColor: '#fff',
         ease: 'Power3.easeOut',
      })

      gsap.to(domCache.menuBurgerCrossLine1, .15, {
         rotate: 45,
         x: 3.5,
         ease: 'Power3.easeOut',
      })
      
      gsap.to(domCache.menuBurgerCrossLine2, .15, {
         rotate: -45,
         x: -3.5,
         ease: 'Power3.easeOut',
      })
   } else {
      state.isMenuOpen = false
      domCache.menuBackground.classList.remove('menuActive')

      gsap.to(domCache.cursor, .25, {
         backgroundColor: '#494949',
         ease: 'Power3.easeOut',
         delay: .7
      })

      gsap.to(domCache.menuBurgerLines, .75, {
         backgroundColor: '#000',
         ease: 'Power3.easeOut',
         delay: .5
      })

      gsap.to(domCache.menuBurgerLines, .15, {
         x: 0,
         rotate: 0,
         ease: 'Power3.easeOut',
      })
   }
})

window.addEventListener('keypress', (e) => {
   console.log(e)
})

function raf() {
   lowestElapsedTime += 0.0006


   if (!state.isMouseHovering) {
      gsap.to(domCache.cursor, .09, {
         css: {
            left: posX,
            top: posY
         },
         ease: 'Power3.easeInOut'
      })
   }

   domCache.menuBackground.style.top = domCache.cursor.style.top
   domCache.menuBackground.style.left = domCache.cursor.style.left

   window.requestAnimationFrame(raf)
}

raf()


luge.settings({smooth: {inertia: 0.1}})