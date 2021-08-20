import "../sass/utils/sessions.scss";
import "../sass/utils/ownSessions.scss";

import Splide from '@splidejs/splide';
import checkUserDevice from "../functions/checkUserDevice.js";

let paddingWidth
checkUserDevice()?paddingWidth = 0:paddingWidth = 15


if (parseInt(document.getElementById('sessionsLength').innerHTML) > 1) {
   document.getElementById('splide').style.cursor = 'grab'
   document.getElementById('splide').style.width = '900px'

   new Splide( '#splide', {
      type   : 'loop',
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


const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
               <g id="swippe_arrow" transform="translate(-180 3164)">
                  <rect id="bg" width="40" height="40" rx="2" transform="translate(180 -3164)" fill="rgba(204,46,46,0.5)"/>
                  <path id="arrow" d="M0,9.73l9.975,9.731ZM9.975,0,0,9.73Z" transform="translate(194.063 -3153.82)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
               </g>
            </svg>`