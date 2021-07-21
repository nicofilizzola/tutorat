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


const menuBurger = document.querySelector('.menuBurger')
const menuBurgerLines = menuBurger.querySelectorAll('span')

menuBurger.addEventListener('mouseenter', () => {
   menuBurgerLines.forEach(line => {
      line.classList.toggle('menuHovered')
      line.classList.toggle('menuNotHovered')
   })
})
menuBurger.addEventListener('mouseleave', () => {
   menuBurgerLines.forEach(line => {
      line.classList.toggle('menuHovered')
      line.classList.toggle('menuNotHovered')
   })
})