import { defineStore } from 'pinia'
import * as authService from '@/services/authService'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    isJustLoggedIn: false,
    isLoggingOut: false
  }),

  actions: {
    /**
     * Login melalui API backend Laravel Sanctum.
     * Mengirim email & password, menerima token + data user.
     */
    async login(email, password) {
      try {
        const data = await authService.login(email, password)

        // Simpan token dan user ke state & localStorage
        this.token = data.access_token
        this.user = data.user
        this.isJustLoggedIn = true

        localStorage.setItem('token', data.access_token)
        localStorage.setItem('user', JSON.stringify(data.user))

        return data.user
      } catch (error) {
        // Jika backend mengembalikan error (401, 403, 422), lempar pesan error
        if (error.response) {
          throw new Error(error.response.data.message || 'Login gagal.')
        }
        throw new Error('Tidak dapat terhubung ke server.')
      }
    },

    /**
     * Logout — hapus token di server dan bersihkan data lokal.
     */
    async logout() {
      try {
        if (this.token) {
          await authService.logout()
        }
      } catch (error) {
        // Abaikan error saat logout (misal token sudah expired)
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
      }
    }
  }
})
