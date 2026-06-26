import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

export let echo = null

/**
 * Connect to Laravel Reverb WebSocket server with the user's Sanctum token.
 */
// export function connectEcho(token) {
//   if (echo) {
//     disconnectEcho()
//   }

//   echo = new Echo({
//     broadcaster: 'reverb',
//     key: import.meta.env.VITE_REVERB_APP_KEY || 'schoolkey', // Standard key used by Reverb config
//     wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
//     wsPort: import.meta.env.VITE_REVERB_PORT || 8090,
//     wssPort: import.meta.env.VITE_REVERB_PORT || 8090,
//     forceTLS: false, // Local development
//     enabledTransports: ['ws', 'wss'],
//     authEndpoint: 'http://127.0.0.1:8000/api/broadcasting/auth',
//     auth: {
//       headers: {
//         Authorization: `Bearer ${token}`
//       }
//     }
//   })
// }

export function connectEcho(token) {
  if (echo) {
    disconnectEcho()
  }

  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api'
  const broadcaster = import.meta.env.VITE_BROADCASTER || 'reverb'

  if (broadcaster === 'pusher') {
    // --- KONFIGURASI PUSHER ---
    echo = new Echo({
      broadcaster: 'pusher',
      key: import.meta.env.VITE_PUSHER_APP_KEY || 'ee5ef2c776d9a338df22',
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
      forceTLS: true,
      authEndpoint: `${apiBaseUrl}/broadcasting/auth`,
      auth: {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    })
  } else {
    // --- KONFIGURASI REVERB (Lokal default) ---
    const reverbHost = import.meta.env.VITE_REVERB_HOST || '127.0.0.1'
    const reverbPort = import.meta.env.VITE_REVERB_PORT || 8090
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http'
    const forceTLS = reverbScheme === 'https'

    echo = new Echo({
      broadcaster: 'reverb',
      key: import.meta.env.VITE_REVERB_APP_KEY || 'schoolkey',
      wsHost: reverbHost,
      wsPort: parseInt(reverbPort),
      wssPort: parseInt(reverbPort),
      forceTLS: forceTLS,
      enabledTransports: ['ws', 'wss'],
      authEndpoint: `${apiBaseUrl}/broadcasting/auth`,
      auth: {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    })
  }
}


/**
 * Disconnect from Laravel Reverb.
 */
export function disconnectEcho() {
  if (echo) {
    echo.disconnect()
    echo = null
  }
}
