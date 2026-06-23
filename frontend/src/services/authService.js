import api from './api'

/**
 * Login — kirim email & password ke backend, terima token & data user.
 */
export async function login(email, password) {
  const response = await api.post('/login', { email, password })
  return response.data
}

/**
 * Logout — hapus token aktif di server.
 */
export async function logout() {
  const response = await api.post('/logout')
  return response.data
}

/**
 * Get User — ambil profil user yang sedang login.
 */
export async function getUser() {
  const response = await api.get('/user')
  return response.data
}
