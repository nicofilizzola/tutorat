import "../sass/utils/sessions.scss";
import "../sass/utils/ownSessions.scss";

import Splide from '@splidejs/splide';
import checkUserDevice from "../functions/checkUserDevice.js";

let paddingWidth, studentSessionLength, tutorSessionLength
checkUserDevice()?paddingWidth = 0:paddingWidth = 15
checkUserDevice()?studentSessionLength = 1:studentSessionLength = 2
checkUserDevice()?tutorSessionLength = 1:tutorSessionLength = 2

const splideSliders = document.querySelectorAll('.splide')

const splideSlider__1 = document.querySelector('.splide__1')
const splideSlider__2 = document.querySelector('.splide__2')

if (parseInt(document.getElementById('studentSessionsLength').innerHTML) > studentSessionLength) {
   splideSlider__1.style.cursor = 'grab'
   splideSlider__1.style.width = '900px'

   new Splide( splideSlider__1, {
      type: 'loop',
      padding: {
         right: `${paddingWidth}rem`,
         left : `${paddingWidth}rem`,
      },
      autoWidth: true,
      gap: '1rem',
      perPage: '1',
      arrowPath: svg
   } ).mount()
}

if (parseInt(document.getElementById('tutorSessionsLength').innerHTML) > tutorSessionLength) {
   splideSlider__2.style.cursor = 'grab'
   splideSlider__2.style.width = '900px'

   new Splide( splideSlider__2, {
      type: 'loop',
      padding: {
         right: `${paddingWidth}rem`,
         left : `${paddingWidth}rem`,
      },
      autoWidth: true,
      gap: '1rem',
      perPage: '1',
      arrowPath: svg
   } ).mount()
}

const svg =
`<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
   <g id="swippe_arrow" transform="translate(-180 3164)">
      <rect id="bg" width="40" height="40" rx="2" transform="translate(180 -3164)" fill="rgba(204,46,46,0.5)"/>
      <path id="arrow" d="M0,9.73l9.975,9.731ZM9.975,0,0,9.73Z" transform="translate(194.063 -3153.82)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
   </g>
</svg>`

splideSliders.forEach(splideSlider => {
   splideSlider.addEventListener('mousedown', () => {
      splideSlider.style.cursor = 'grabbing'
   })
   
   splideSlider.addEventListener('mouseup', () => {
      splideSlider.style.cursor = 'grab'
   })
   
   splideSlider.addEventListener('mouseleave', () => {
      splideSlider.style.cursor = 'grab'
   })
})