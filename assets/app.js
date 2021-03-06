/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// import './styles/app.css';
import './sass/main.scss';
import '@waaark/luge/dist/css/luge.css';

// start the Stimulus application
import './bootstrap';

import { gsap } from 'gsap'
import luge from '@waaark/luge'

// import { manageFlashMessages } from "./functions/manageFlashMessages.js";
import { nav } from "./functions/nav.js";

const domCache = {
   // Cursor
   cursor: document.querySelector('.cursor'),

   // Menu
   menuBackground: document.querySelector('.menu--background'),
   menuBurger: document.querySelector('.menuBurger'),
   menuBurgerLines: document.querySelectorAll('.menuBurger span'),
   menuBurgerCrossLine1: document.querySelector('.cross__1'),
   menuBurgerCrossLine2: document.querySelector('.cross__2'),
   menuContentContainer: document.querySelector('.menuContent--container'),
   menuContentLink: document.querySelectorAll('.menu--link'),}

let state = {
   isMenuOpen: false,
   isMouseHovering: false,
}

let posX = 0
let posY = 0
let lowestElapsedTime = 0

// Stylize nav path
nav()

// Split text menu
domCache.menuContentLink.forEach(content => {
   const contentText = content.children[0].children[0].innerHTML.split('')
   
   const fragment = document.createDocumentFragment()
   
   contentText.forEach(contentChar => {
      const span = document.createElement('span')
      span.innerHTML = contentChar
      if (span.innerHTML == " ") {
         span.style.marginLeft = '2px'
      } else {
         span.style.marginLeft = '.5px'
      }
      fragment.appendChild(span)
   })
   
   content.children[0].children[0].remove()
   content.children[0].appendChild(fragment)
})

// flash messages
if (document.querySelector('.flash_message')) {
   document.querySelector('.flash--container .cross').addEventListener('click', () => {
      const flashContainer = document.querySelector('.flash--container')
      flashContainer.style.opacity = 0
      setTimeout(() => {
         flashContainer.remove()
      }, 750);
   })
   document.querySelector('.flash--container .background').addEventListener('click', () => {
      const flashContainer = document.querySelector('.flash--container')
      flashContainer.style.opacity = 0
      setTimeout(() => {
         flashContainer.remove()
      }, 750);
   })
}

// modal messages
if (document.querySelector('.modal_message')) {
   document.querySelector('.modal--container .cross').addEventListener('click', () => {
      const modalContainer = document.querySelector('.modal--container')
      modalContainer.style.opacity = 0
      setTimeout(() => {
         modalContainer.style.pointerEvents = 'none'
      }, 500);
   })
   document.querySelector('.modal--container .background').addEventListener('click', () => {
      const modalContainer = document.querySelector('.modal--container')
      modalContainer.style.opacity = 0
      setTimeout(() => {
         modalContainer.style.pointerEvents = 'none'
      }, 500);
   })
}

// Get mouse postition
document.addEventListener('mousemove', (mouse) => {
   posX = mouse.pageX
   posY = mouse.pageY - window.scrollTop
})

// Interactive cursor custom position
domCache.hoveredItems = document.querySelectorAll('.hoveredItems')

domCache.hoveredItems.forEach(item => {
   item.addEventListener('mouseenter', () => {
      state.isMouseHovering = true
      const rect = item.getBoundingClientRect()
      const itemPosX = rect.x + rect.width - 20
      const itemPosY = rect.y + 10

      gsap.to(domCache.cursor, .5, { css: { left: itemPosX, top: itemPosY }, ease: 'Power3.easeInOut' })
   })

   item.addEventListener('mouseleave', () => {
      state.isMouseHovering = false
   })
})

// Menu hovered
domCache.menuBurger.addEventListener('mouseenter', () => {
   if (!state.isMenuOpen) {
      gsap.to(domCache.menuBurgerLines, .1, { stagger: { each: .05, y: 50}, ease: 'Expo3.easeOut' })
   }
})
domCache.menuBurger.addEventListener('mouseleave', () => {
   if (!state.isMenuOpen) {
      gsap.to(domCache.menuBurgerLines, .1, { stagger: { each: .05, y: 0}, ease: 'Expo3.easeOut' })
   }
})

// Menu opened
domCache.menuBurger.addEventListener('click', () => {
   if (!state.isMenuOpen) {
      state.isMenuOpen = true
      gsap.to(domCache.menuBackground, 1, { scale: 2, ease: 'Power3.easeInOut' })
      // domCache.menuBackground.classList.add('menuActive')

      gsap.to(domCache.cursor, .25, { backgroundColor: '#fff', ease: 'Power3.easeOut' })

      gsap.to(domCache.menuBurgerLines, .75, { backgroundColor: '#fff', ease: 'Power3.easeOut' })
      gsap.to(domCache.menuBurgerCrossLine1, .15, { rotate: 45, x: 3.5, ease: 'Power3.easeOut' })
      gsap.to(domCache.menuBurgerCrossLine2, .15, { rotate: -45, x: -3.5, ease: 'Power3.easeOut' })

      domCache.menuContentContainer.style.display = 'flex'
      gsap.to(domCache.menuContentContainer, .5, { opacity: 1, ease:'Power3.easeOut', delay: .75 })
   } else {
      state.isMenuOpen = false
      gsap.to(domCache.menuBackground, .75, { scale: 0, ease: 'Power3.easeInOut' })
      // gsap.to(domCache.menuBackground, 1.75, { scale: 0, ease: CustomEase.create("custom", "M0,0 C0.14,0 0.27,0.428 0.306,0.55 0.356,0.723 0.42,0.963 0.428,1 0.436,0.985 0.47,0.886 0.578,0.886 0.69,0.886 0.719,0.981 0.726,0.998 0.788,0.914 0.84,0.936 0.859,0.95 0.878,0.964 0.897,0.985 0.911,0.998 0.922,0.994 0.942,0.983 0.954,0.984 0.966,0.984 1,1 1,1 ") })
      // domCache.menuBackground.classList.remove('menuActive')

      gsap.to(domCache.cursor, .25, { backgroundColor: '#494949', ease: 'Power3.easeOut', delay: .7 })

      gsap.to(domCache.menuBurgerLines, .75, { backgroundColor: '#000', ease: 'Power3.easeOut', delay: .5 })
      gsap.to(domCache.menuBurgerLines, .15, { x: 0, rotate: 0, ease: 'Power3.easeOut' })

      gsap.to(domCache.menuContentContainer, .5, { opacity: 0, ease:'Power3.easeOut' })
      setTimeout(() => {
         domCache.menuContentContainer.style.display = 'none'
      }, 500);
   }
})

// Menu content hovered
domCache.menuContentLink.forEach(content => {
   content.addEventListener('mouseenter', () => {
      gsap.to(content.children[0].children, .25, { color: '#ff3a39', stagger: { each: .03, from: 'start'}, ease: 'Expo3.easeOut' })
   }) 
   content.addEventListener('mouseleave', () => {
      gsap.to(content.children[0].children, .25, { color: '#fff', stagger: { each: .03, from: 'start'}, ease: 'Expo3.easeOut' })
   }) 
})

luge.settings(
   {smooth: 
      {inertia: 0.1}
   },
   // {preloader: 
   //    {duration: 1000}
   // }
)

window.addEventListener('resize', () => {
   luge.lifecycle.refresh()
})

function raf() {
   lowestElapsedTime += 0.0006

   // cursor follow
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