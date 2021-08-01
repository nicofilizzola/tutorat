import "../sass/utils/session.scss";

import { gsap } from 'gsap'
import { _forEachName } from "gsap/gsap-core";

const domeCache = {
   cards: document.querySelectorAll('.card--container .inscription'),
   hoveredCards: document.querySelectorAll('.card--hovered'),
   hoveredCardsBackground: document.querySelectorAll('.card--hovered .background'),
   hoveredCardsText: document.querySelectorAll('.card--hovered .texte span'),
}

domeCache.cards.forEach(card => {
   card.addEventListener('mouseenter', () => {
      // console.log(card.children[0].children[0].children)
      // gsap.to(card.children[0].children[1], .75, { xPercent: 100, ease: 'Expo3.easeOut' })
      // gsap.to(card.children[0].children[0].children, .3, { opacity: 1, y: 0, rotationZ: 0, stagger: { each: .05, from: 'start' }, ease: 'Expo3.easeOut', delay: .25 })
   })
   
   card.addEventListener('mouseleave', () => {
      // console.log(card.children[0])
      // gsap.to(card.children[0].children[1], 1, { xPercent: 0, ease: 'Expo3.easeOut' })
      // gsap.to(card.children[0].children[0].children, .3, { opacity: 0,  y: -50, rotationZ: -15, stagger: { each: .05, from: 'end'}, ease: 'Expo3.easeOut' })
      // gsap.to(card.children[0].children[0].children, 0, { opacity: 0, y: 50, rotationZ: 15, stagger: { each: .05, from: 'end' }, delay: .5 })
   })
})