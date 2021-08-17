import "../sass/utils/sessions.scss";

const domCache = {
   // Filtres
   filterButton: document.querySelector('.filterButton'),
   filterButtonArrow: document.querySelector('.filterButton svg'),
   filterContent: document.querySelector('.filter--content'),

   // Cartes de cours
   cards: document.querySelectorAll('.card--container .inscription'),
   hoveredCards: document.querySelectorAll('.card--hovered'),
   hoveredCardsBackground: document.querySelectorAll('.card--hovered .background'),
   hoveredCardsText: document.querySelectorAll('.card--hovered .texte span'),
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