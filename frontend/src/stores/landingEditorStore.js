import { defineStore } from 'pinia'
import * as service from '@/services/landingPageService'
import { useAuthStore } from '@/stores/authStore'

/**
 * Landing Editor Store
 *
 * Semua operasi menggunakan endpoint yang SAMA dengan Konfigurasi Global:
 * - admin_yayasan  → /landing-page/foundations/{foundation_id}
 * - admin_sekolah/kepala_sekolah → /landing-page/schools/{school_id}
 *
 * Tidak ada lagi dummy data fallback. Jika API gagal, tampilkan error yang jelas.
 */
export const useLandingEditorStore = defineStore('landingEditor', {
  state: () => ({
    landingPage: null,
    loading: false,
    saving: false,
    error: null,
    sidebarOpen: true
  }),

  actions: {
    /**
     * Fetch landing page config dari database sesuai entitas user yang sedang login.
     */
    async fetchLandingPage() {
      this.loading = true
      this.error = null
      try {
        const authStore = useAuthStore()
        const res = await service.getMyEntityLandingPage(authStore.user)

        // Backend mengembalikan { id, landing_page_enabled, landing_page_theme, landing_page_config }
        // Kita "flatten" landing_page_config ke dalam state agar konsisten dengan format editor
        if (res) {
          const config = res.landing_page_config
            ? (typeof res.landing_page_config === 'string'
                ? JSON.parse(res.landing_page_config)
                : res.landing_page_config)
            : {}

          this.landingPage = {
            ...config,
            id: res.id,
            // Data profil dari top-level (readonly di editor — sama dengan super admin)
            meta_title:            res.name         || config.meta_title    || '',
            legal_number:          res.legal_number || config.legal_number  || '',
            logo:                  res.logo         || config.logo          || '',
            landing_page_enabled:  res.landing_page_enabled,
            theme:                 res.landing_page_theme || config.theme || 'modern',
          }
        }
      } catch (err) {
        this.error = err.message || 'Gagal memuat konfigurasi landing page.'
        console.error('[LandingEditorStore] fetchLandingPage error:', err)
      } finally {
        this.loading = false
      }
    },

    /**
     * Simpan pengaturan ke database.
     * Payload dikirim dalam format yang dipahami oleh updateFoundationConfig/updateSchoolConfig.
     */
    async saveSettings(data) {
      this.saving = true
      try {
        const authStore = useAuthStore()

        // Payload harus match format yang diterima backend controller
        const payload = {
          landing_page_enabled: this.landingPage?.landing_page_enabled ?? false,
          landing_page_theme: data.theme || this.landingPage?.theme || 'modern',
          landing_page_config: {
            ...(this.landingPage || {}), // existing config sebagai base
            ...data,                     // data baru dari form menimpa base
          }
        }

        const res = await service.saveMyEntityLandingPage(authStore.user, payload)

        // Refresh dari response backend agar state sinkron
        if (res?.data) {
          const config = res.data.landing_page_config
            ? (typeof res.data.landing_page_config === 'string'
                ? JSON.parse(res.data.landing_page_config)
                : res.data.landing_page_config)
            : {}

          this.landingPage = {
            ...config,
            id: res.data.id,
            landing_page_enabled: res.data.landing_page_enabled,
            theme: res.data.landing_page_theme || config.theme || 'modern',
          }
        } else {
          // Jika backend tidak mengembalikan data lengkap, update state secara optimistic
          this.landingPage = { ...this.landingPage, ...data }
        }

        return res
      } catch (err) {
        const message = err.response?.data?.message || err.message || 'Gagal menyimpan perubahan.'
        throw new Error(message)
      } finally {
        this.saving = false
      }
    },

    /**
     * Upload gambar ke backend dan kembalikan URL-nya.
     */
    async uploadImage(file) {
      this.saving = true
      try {
        const res = await service.uploadLandingImage(file)
        return res
      } catch (err) {
        const message = err.response?.data?.error || err.message || 'Gagal mengupload gambar.'
        throw new Error(message)
      } finally {
        this.saving = false
      }
    },

    /**
     * Toggle publish/draft status landing page.
     * Menggunakan saveSettings untuk menyimpan perubahan is_published.
     */
    async togglePublishStatus() {
      if (!this.landingPage) throw new Error('Data landing page belum dimuat.')

      const newStatus = !this.landingPage.is_published
      this.saving = true
      try {
        const authStore = useAuthStore()
        const payload = {
          landing_page_enabled: this.landingPage.landing_page_enabled ?? false,
          landing_page_theme: this.landingPage.theme || 'modern',
          landing_page_config: {
            ...(this.landingPage || {}),
            is_published: newStatus,
          }
        }

        const res = await service.saveMyEntityLandingPage(authStore.user, payload)

        // Update state lokal
        this.landingPage = { ...this.landingPage, is_published: newStatus }

        return {
          is_published: newStatus,
          message: newStatus
            ? 'Landing page berhasil dipublikasikan!'
            : 'Landing page dinonaktifkan (Draft).',
          data: res?.data
        }
      } catch (err) {
        const message = err.response?.data?.message || err.message || 'Gagal mengubah status publikasi.'
        throw new Error(message)
      } finally {
        this.saving = false
      }
    },

    /**
     * Update urutan sections dan simpan ke backend.
     */
    async updateSectionOrders(sections) {
      if (!this.landingPage) return
      this.saving = true
      try {
        const authStore = useAuthStore()
        const payload = {
          landing_page_enabled: this.landingPage.landing_page_enabled ?? false,
          landing_page_theme: this.landingPage.theme || 'modern',
          landing_page_config: {
            ...(this.landingPage || {}),
            sections,
          }
        }

        await service.saveMyEntityLandingPage(authStore.user, payload)
        this.landingPage = { ...this.landingPage, sections }
      } catch (err) {
        const message = err.response?.data?.message || err.message || 'Gagal menyimpan urutan section.'
        throw new Error(message)
      } finally {
        this.saving = false
      }
    },

    /**
     * Tambah item ke sebuah section dan simpan ke backend.
     */
    async addItem(sectionId, itemData) {
      if (!this.landingPage) return
      const section = this.landingPage.sections?.find(s => s.id === sectionId)
      if (!section) throw new Error('Section tidak ditemukan.')

      const newItem = { id: Date.now(), ...itemData }
      const updatedSections = this.landingPage.sections.map(s =>
        s.id === sectionId
          ? { ...s, items: [...(s.items || []), newItem] }
          : s
      )

      await this.updateSectionOrders(updatedSections)
    },

    /**
     * Update sebuah item dalam section dan simpan ke backend.
     */
    async updateItem(sectionId, itemId, itemData) {
      if (!this.landingPage) return

      const updatedSections = this.landingPage.sections.map(s => {
        if (s.id !== sectionId) return s
        return {
          ...s,
          items: (s.items || []).map(item =>
            item.id === itemId ? { ...item, ...itemData } : item
          )
        }
      })

      await this.updateSectionOrders(updatedSections)
    },

    /**
     * Hapus sebuah item dari section dan simpan ke backend.
     */
    async removeItem(sectionId, itemId) {
      if (!this.landingPage) return

      const updatedSections = this.landingPage.sections.map(s => {
        if (s.id !== sectionId) return s
        return {
          ...s,
          items: (s.items || []).filter(item => item.id !== itemId)
        }
      })

      await this.updateSectionOrders(updatedSections)
    }
  }
})
