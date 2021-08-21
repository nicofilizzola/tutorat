const nav = () => {
   const domCache = {
      customNavPathContainer: document.querySelector('.customNavPath--container'),
      navPath: document.querySelector('.navPath'),
   }
   
   const navPathNames = {
      'home':'accueil',
      'sessions':'cours',
      'create':'créer',
      'log':'logs',
      'users':'utilisateurs',
      'subject':'modules',
      'classroom':'salles',
      'login':'connexion',
      'register':'inscription',
      'ownSessions':'mes cours',
      'pending':'attribution'
   }

   const cutPath = domCache.navPath.innerHTML.split('/')
   
   const customPathFragment = document.createDocumentFragment()
   const paths = []
   
   cutPath.forEach(path => {
      if (path != '') {
         const gap = document.createElement('span')
         gap.innerHTML = '<'
         gap.classList.add('gap')
   
         const linkPath = document.createElement('a')
         paths.push(path)
         paths.forEach(path => {
            linkPath.href += `/${path}`
         })
         linkPath.classList.add('hoveredItems')
         linkPath.classList.add('lineHoverEffect')
         
         const textPath = document.createElement('span')
         if (navPathNames[path] != undefined)
            textPath.innerHTML = navPathNames[path]
         else
            textPath.innerHTML = path

   
         linkPath.appendChild(textPath)
   
         customPathFragment.appendChild(gap)
         customPathFragment.appendChild(linkPath)
      }
   });
   
   domCache.customNavPathContainer.appendChild(customPathFragment)
   domCache.navPath.remove()
}

export { nav }