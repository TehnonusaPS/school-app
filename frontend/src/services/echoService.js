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

  // Detect environment
  const isProduction = import.meta.env.PROD || window.location.protocol === 'https:';
  const defaultHost = isProduction ? 'school-app-reverb.onrender.com' : '127.0.0.1';
  const defaultPort = isProduction ? 443 : 8090;
  const defaultScheme = isProduction ? 'https' : 'http';

  const host = import.meta.env.VITE_REVERB_HOST || defaultHost;
  const port = import.meta.env.VITE_REVERB_PORT || defaultPort;
  const scheme = import.meta.env.VITE_REVERB_SCHEME || defaultScheme;
  const forceTLS = scheme === 'https';

  const apiHost = import.meta.env.VITE_API_URL || (isProduction ? 'https://school-app-ewoy.onrender.com' : 'http://127.0.0.1:8000');

  echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'schoolkey', // Standard key used by Reverb config
    wsHost: host,
    wsPort: port,
    wssPort: port,
    forceTLS: forceTLS,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: `${apiHost}/api/broadcasting/auth`,
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
