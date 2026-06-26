import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

export let echo = null

/**
 * Connect to Laravel Reverb WebSocket server with the user's Sanctum token.
 */
export function connectEcho(token) {
  if (echo) {
    disconnectEcho()
  }

  echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'schoolkey', // Standard key used by Reverb config
    wsHost: import.meta.env.VITE_REVERB_HOST || 'school-app-reverb.onrender.com',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8090,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8090,
    forceTLS: false, // Local development
    enabledTransports: ['ws', 'wss'],
    authEndpoint: 'http://127.0.0.1:8000/api/broadcasting/auth',
    auth: {
      headers: {
        Authorization: `Bearer ${token}`
      }
    }
  })
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
