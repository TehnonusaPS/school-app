import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null
  }),

  actions: {
    login(email, password) {
      // 🔥 dummy multi-role
      let user = null

      if (email === 'admin@mail.com' && password === '123456') {
        user = { name: 'Admin', role: 'admin' }
      } else if (email === 'guru@mail.com' && password === '123456') {
        user = { name: 'Guru', role: 'guru' }
      } else if (email === 'siswa@mail.com' && password === '123456') {
        user = { name: 'Siswa', role: 'siswa' }
      }

      if (user) {
        this.user = user
        localStorage.setItem('user', JSON.stringify(user))
        return user
      }

      return null
    },

    logout() {
      this.user = null
      localStorage.removeItem('user')
    }
  }
})
