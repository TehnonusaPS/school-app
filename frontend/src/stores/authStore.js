import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    isJustLoggedIn: false,
    isLoggingOut: false
  }),

  actions: {
    login(email, password) {
      let user = null

      const normalizedEmail = email.toLowerCase().trim()

      if (normalizedEmail === 'superadmin@mail.com' && password === '123456') {
        user = { name: 'Super Admin', role: 'superadmin', roleLabel: 'SUPERADMIN', email: 'superadmin@mail.com' }
      } else if (normalizedEmail === 'adminyayasan@mail.com' && password === '123456') {
        user = { name: 'Admin Yayasan', role: 'admin_yayasan', roleLabel: 'ADMIN YAYASAN', email: 'adminyayasan@mail.com', yayasanId: 'Y0001' }
      } else if (normalizedEmail === 'kepalasekolah@mail.com' && password === '123456') {
        user = { name: 'Kepala Sekolah', role: 'kepala_sekolah', roleLabel: 'KEPALA SEKOLAH', email: 'kepalasekolah@mail.com' }
      } else if (normalizedEmail === 'adminsekolah@mail.com' && password === '123456') {
        user = { name: 'Admin Sekolah', role: 'admin_sekolah', roleLabel: 'ADMIN SEKOLAH', email: 'adminsekolah@mail.com' }
      } else if (normalizedEmail === 'tatausaha@mail.com' && password === '123456') {
        user = { name: 'Tata Usaha', role: 'tata_usaha', roleLabel: 'TATA USAHA', email: 'tatausaha@mail.com' }
      } else if (normalizedEmail === 'guru@mail.com' && password === '123456') {
        user = { name: 'Guru Pengajar', role: 'guru', roleLabel: 'GURU', email: 'guru@mail.com' }
      } else if (normalizedEmail === 'walikelas@mail.com' && password === '123456') {
        user = { name: 'Wali Kelas', role: 'wali_kelas', roleLabel: 'WALI KELAS', email: 'walikelas@mail.com' }
      } else if (normalizedEmail === 'siswa@mail.com' && password === '123456') {
        user = { id: 1, name: 'Ahmad Wibowo', role: 'siswa', roleLabel: 'SISWA', email: 'siswa@mail.com', kelas: '2 D' }
      } else if (normalizedEmail === 'orangtua@mail.com' && password === '123456') {
        user = { name: 'Orang Tua / Wali', role: 'orang_tua', roleLabel: 'ORANG TUA/WALI', email: 'orangtua@mail.com' }
      }

      if (user) {
        this.user = user
        this.isJustLoggedIn = true
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
