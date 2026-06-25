import axios from 'axios'

const api = axios.create({
 // baseURL: 'http://127.0.0.1:8000/api',
   baseURL: 'https://school-app-ewoy.onrender.com/api',
  headers: {
    'Accept': 'application/json'
  }
})

// Request interceptor — sisipkan Bearer token secara otomatis jika ada
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Response interceptor — tangani 401 Unauthorized secara global
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      // Token expired atau tidak valid, bersihkan data auth
      localStorage.removeItem('token')
      localStorage.removeItem('user')

      // Redirect ke login jika belum di halaman login
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)

export default api
