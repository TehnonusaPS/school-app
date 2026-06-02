export const glassSlide = {
  initial: { 
    opacity: 0, 
    y: 30, 
    scale: 0.98 
  },
  visible: { 
    opacity: 1, 
    y: 0, 
    scale: 1, 
    transition: { 
      type: 'spring', 
      stiffness: 250, 
      damping: 25, 
      mass: 0.5 
    } 
  }
}

export const glassFade = {
  initial: { 
    opacity: 0, 
    scale: 0.95 
  },
  visible: { 
    opacity: 1, 
    scale: 1, 
    transition: { 
      type: 'spring', 
      stiffness: 300, 
      damping: 25, 
      mass: 0.5 
    } 
  }
}

export const sidebarSlide = {
  initial: { opacity: 0, x: -50 },
  enter: { 
    opacity: 1, 
    x: 0, 
    transition: { type: 'spring', stiffness: 200, damping: 20, mass: 0.8, delay: 100 } 
  }
}

export const topbarSlide = {
  initial: { opacity: 0, y: -30 },
  enter: { 
    opacity: 1, 
    y: 0, 
    transition: { type: 'spring', stiffness: 200, damping: 20, mass: 0.8, delay: 200 } 
  }
}

// Sidebar menu items — staggered slide from left
export const navItemSlide = {
  initial: { opacity: 0, x: -20 },
  enter: { 
    opacity: 1, 
    x: 0, 
    transition: { type: 'spring', stiffness: 260, damping: 22, mass: 0.6 } 
  }
}

// Page header — slide from left with fade
export const pageHeaderSlide = {
  initial: { opacity: 0, x: -25 },
  visible: { 
    opacity: 1, 
    x: 0, 
    transition: { type: 'spring', stiffness: 220, damping: 22, mass: 0.7 } 
  }
}

// Table rows — subtle fade up
export const tableRowFade = {
  initial: { opacity: 0, y: 12 },
  visible: { 
    opacity: 1, 
    y: 0, 
    transition: { type: 'spring', stiffness: 300, damping: 28, mass: 0.5 } 
  }
}
