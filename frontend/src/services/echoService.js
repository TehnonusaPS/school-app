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

  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'https://school-app-ewoy.onrender.com/api'
  const broadcaster = import.meta.env.VITE_BROADCASTER || 'reverb'

  if (broadcaster === 'pusher') {
    // --- KONFIGURASI PUSHER ---
    const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY || 'ee5ef2c776d9a338df22'
    const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1'
    console.log('Laravel Echo: Initializing Pusher', { key: pusherKey, cluster: pusherCluster, authEndpoint: `${apiBaseUrl}/broadcasting/auth` })
    
    echo = new Echo({
      broadcaster: 'pusher',
      key: pusherKey,
      cluster: pusherCluster,
      forceTLS: true,
      authEndpoint: `${apiBaseUrl}/broadcasting/auth`,
      auth: {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    })

    if (echo.connector && echo.connector.pusher) {
      echo.connector.pusher.connection.bind('state_change', (states) => {
        console.log('Laravel Echo Pusher: Connection state changed from', states.previous, 'to', states.current)
      })
      echo.connector.pusher.connection.bind('error', (err) => {
        console.error('Laravel Echo Pusher: Connection error:', err)
      })
    }
  } else {
    // --- KONFIGURASI REVERB (Lokal default) ---
    const reverbHost = import.meta.env.VITE_REVERB_HOST || '127.0.0.1'
    const reverbPort = import.meta.env.VITE_REVERB_PORT || 8090
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http'
    const forceTLS = reverbScheme === 'https'

    console.log('Laravel Echo: Initializing Reverb', { key: import.meta.env.VITE_REVERB_APP_KEY || 'schoolkey', wsHost: reverbHost, wsPort: reverbPort, scheme: reverbScheme, authEndpoint: `${apiBaseUrl}/broadcasting/auth` })

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

    if (echo.connector && echo.connector.pusher) {
      echo.connector.pusher.connection.bind('state_change', (states) => {
        console.log('Laravel Echo Reverb: Connection state changed from', states.previous, 'to', states.current)
      })
      echo.connector.pusher.connection.bind('error', (err) => {
        console.error('Laravel Echo Reverb: Connection error:', err)
      })
    }
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
