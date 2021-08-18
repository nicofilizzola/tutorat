import "../sass/utils/sessions.scss";

var distance = require('jaro-winkler');

const domCache = {
   // Filtres
   filterButton: document.querySelector('.filterButton'),
   filterButtonArrow: document.querySelector('.filterButton svg'),
   filterContent: document.querySelector('.filter--content'),
   
   // Filtre buttons
   filterInputs: document.querySelectorAll('input[type=checkbox]'),

   // Cartes de cours
   cardContainers: document.querySelectorAll('.card--container'),
   cards: document.querySelectorAll('.card--container .inscription'),
   hoveredCards: document.querySelectorAll('.card--hovered'),
   hoveredCardsBackground: document.querySelectorAll('.card--hovered .background'),
   hoveredCardsText: document.querySelectorAll('.card--hovered .texte span'),
}

const tempFilters = []

domCache.filterInputs.forEach(filter => {
   filter.checked = false
})

domCache.filterInputs.forEach(filter => {
   filter.addEventListener('change', () => {
      let checkFilter = false
      const tempFiltersLength = tempFilters.length
      for (let i = 0; i < tempFiltersLength; i++) {
         if (tempFilters[i] === filter) {
            tempFilters.splice(i, 1)
            checkFilter = true
         }
      }
      if (!checkFilter) {
         tempFilters.push(filter)
      }

      globalFilter(tempFilters)
      console.log(tempFilters)
   })
})

function globalFilter(filters) {
   domCache.cardContainers.forEach(card => {
      if (filters.length == 0) {
         card.hidden = false
         card.style.display = ''
      } else {
         let check = false
         filters.forEach(filter => {
            if (!check) {
               if (card.classList.contains(filter.dataset.filter)) {
                  check = true
               } else {
                  check = false
               }
            }
         })
   
         if (check) {
            card.hidden = false
            card.style.display = ''
         } else {
            card.hidden = true
            card.style.display = 'none'
         }
      }
   })
}

let state = {
   isFilterPanelOpen: false
}

domCache.filterButton.addEventListener('click', () => {
   if (!state.isFilterPanelOpen) {
      state.isFilterPanelOpen = true
      domCache.filterButtonArrow.style.transform = "rotate(180deg)"
      domCache.filterContent.style.opacity = 1
      domCache.filterContent.style.top = "75px"
      domCache.filterContent.style.pointerEvents = "all"
   } else {
      state.isFilterPanelOpen = false
      domCache.filterButtonArrow.style.transform = "rotate(0deg)"
      domCache.filterContent.style.opacity = 0
      domCache.filterContent.style.top = "65px"
      domCache.filterContent.style.pointerEvents = "none"
   }

})