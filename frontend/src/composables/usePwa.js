import { ref } from 'vue'

const deferredPrompt = ref(null)
const isInstallable = ref(false)
const showInstallModal = ref(false)
const installProgress = ref(0)
const installState = ref('idle') // 'idle' | 'installing' | 'completed'

export function usePwa() {
  const initPwa = () => {
    // Check if app is already running in standalone mode (installed PWA)
    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
      isInstallable.value = false
      return
    }

    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault()
      deferredPrompt.value = e
      isInstallable.value = true
    })

    window.addEventListener('appinstalled', () => {
      deferredPrompt.value = null
      isInstallable.value = false
      console.log('PWA was installed')
    })
  }

  const startSimulation = () => {
    installProgress.value = 0
    installState.value = 'installing'
    showInstallModal.value = true
    
    const timer = setInterval(() => {
      if (installProgress.value < 100) {
        installProgress.value += Math.floor(Math.random() * 12) + 6
        if (installProgress.value > 100) installProgress.value = 100
      } else {
        clearInterval(timer)
        installState.value = 'completed'
      }
    }, 250)
  }

  const triggerInstall = async () => {
    if (deferredPrompt.value) {
      try {
        deferredPrompt.value.prompt()
        const { outcome } = await deferredPrompt.value.userChoice
        if (outcome === 'accepted') {
          deferredPrompt.value = null
          isInstallable.value = false
        }
      } catch (err) {
        console.error('Failed to trigger PWA install prompt:', err)
        startSimulation()
      }
    } else {
      // Fallback: Start simulated installer
      startSimulation()
    }
  }

  return {
    isInstallable,
    showInstallModal,
    installProgress,
    installState,
    initPwa,
    triggerInstall
  }
}
