import { defineStore } from 'pinia'
import * as service from '@/services/landingPageService'

// Data Dummy Premium untuk Sekolah/Yayasan
const dummyData = {
  id: 1,
  theme: 'modern',
  slug: 'sd-cerdas-bangsa',
  is_published: true,
  meta_title: 'SD Cerdas Bangsa — Sekolah Unggul & Berkarakter',
  meta_description:
    'Selamat datang di SD Cerdas Bangsa. Kami berkomitmen menyelenggarakan pendidikan terbaik untuk melahirkan generasi yang cerdas, kreatif, berakhlak mulia dan siap menyongsong masa depan.',

  // Hero Carousel
  hero_title: 'Mendidik Generasi Pemimpin Masa Depan',
  hero_subtitle: '✨ SD Cerdas Bangsa Selaras & Berprestasi',
  hero_description:
    'Daftarkan putra/putri Anda sekarang dan berikan mereka lingkungan belajar terbaik yang menyenangkan, inovatif, dan berstandar internasional.',
  hero_image: 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?q=80&w=1200',
  hero_images: [
    {
      url: 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?q=80&w=1200',
      caption: 'Gedung Sekolah Utama'
    },
    {
      url: 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=1200',
      caption: 'Kegiatan Belajar Mengajar'
    },
    {
      url: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1200',
      caption: 'Fasilitas Laboratorium Modern'
    }
  ],
  hero_cta_text: '🎉 Daftar Sekarang!',
  hero_cta_link: '#registration_cta',

  // About Section
  about_title: 'Selamat Datang di SD Cerdas Bangsa',
  about_description:
    'Berdiri sejak tahun 2012, SD Cerdas Bangsa telah menjadi rumah belajar bagi ribuan anak untuk bertumbuh secara akademis, sosial, dan spiritual. Dengan kurikulum merdeka yang dipadukan dengan pembelajaran aktif berbasis proyek, kami memastikan setiap anak menemukan potensi terbaik mereka.',
  about_image: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=800',
  about_vision:
    'Menjadi lembaga pendidikan unggul yang menghasilkan generasi cerdas, berkarakter luhur, berbudaya lingkungan, dan berwawasan global.',
  about_mission: [
    'Menyelenggarakan pembelajaran aktif, inovatif, kreatif, dan menyenangkan.',
    'Menanamkan nilai-nilai karakter luhur dan keagamaan dalam kehidupan sehari-hari.',
    'Mengembangkan bakat dan minat siswa melalui program ekstrakurikuler yang variatif.',
    'Menerapkan budaya ramah lingkungan dan peduli terhadap sesama.'
  ],

  // Colors
  primary_color: '#7c3aed',
  secondary_color: '#f59e0b',
  accent_color: '#06b6d4',
  logo: '',

  // Contact & Socials
  contact_email: 'info@cerdasbangsa.sch.id',
  contact_phone: '+62 812-3456-7890',
  contact_address: 'Jl. Pendidikan No. 45, Kebayoran Baru, Jakarta Selatan, 12110',
  contact_maps_embed:
    'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2736417711413!2d106.79724127585258!3d-6.227606360984852!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f14be6d7d6f5%3A0x6e2df40fe930!2sJakarta%20Selatan!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid',
  social_instagram: 'https://instagram.com/cerdasbangsaschool',
  social_facebook: 'https://facebook.com/cerdasbangsaschool',
  social_youtube: 'https://youtube.com/c/cerdasbangsaschool',
  social_tiktok: 'https://tiktok.com/@cerdasbangsa',

  // Sections
  sections: [
    {
      id: 1,
      type: 'stats',
      title: 'Sekolah Kami Dalam Angka',
      is_visible: true,
      sort_order: 1,
      items: [
        { id: 11, title: 'Siswa Aktif', value: '850+' },
        { id: 12, title: 'Guru Profesional', value: '45' },
        { id: 13, title: 'Kelas / Rombel', value: '24' },
        { id: 14, title: 'Akreditasi', value: 'A+' }
      ]
    },
    {
      id: 2,
      type: 'features',
      title: 'Mengapa Memilih Kami?',
      is_visible: true,
      sort_order: 2,
      items: [
        {
          id: 21,
          title: 'Guru Tersertifikasi',
          description:
            'Tenaga pendidik profesional lulusan universitas ternama yang ramah dan kompeten.',
          icon: 'award'
        },
        {
          id: 22,
          title: 'Kurikulum Modern',
          description:
            'Kurikulum nasional yang diperkaya dengan program bilingual dan penguatan karakter.',
          icon: 'book-open'
        },
        {
          id: 23,
          title: 'Fasilitas Premium',
          description:
            'Ruang kelas ber-AC, laboratorium sains, ruang IT komputer, dan lapangan olahraga indoor.',
          icon: 'shield'
        }
      ]
    },
    {
      id: 3,
      type: 'programs',
      title: 'Program Unggulan',
      is_visible: true,
      sort_order: 3,
      items: [
        {
          id: 31,
          title: 'Bilingual Class (English & Indonesia)',
          description:
            'Pengenalan bahasa Inggris aktif sejak dini untuk melatih rasa percaya diri anak.',
          image: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=600'
        },
        {
          id: 32,
          title: 'Coding & Robotics Kid Club',
          description:
            'Mengasah logika berpikir komputasional siswa melalui kelas merakit robot seru.',
          image: 'https://images.unsplash.com/photo-1561557944-6e7860d1a7eb?q=80&w=600'
        }
      ]
    },
    {
      id: 4,
      type: 'gallery',
      title: 'Galeri Kegiatan Belajar',
      is_visible: true,
      sort_order: 4,
      items: [
        {
          id: 41,
          title: 'Keseruan Belajar Outbound',
          image: 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?q=80&w=600'
        },
        {
          id: 42,
          title: 'Pentas Seni Tahunan',
          image: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=600'
        },
        {
          id: 43,
          title: 'Kelas Eksperimen Sains',
          image: 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600'
        }
      ]
    },
    {
      id: 5,
      type: 'testimonials',
      title: 'Apa Kata Orang Tua Murid?',
      is_visible: true,
      sort_order: 5,
      items: [
        {
          id: 51,
          title: 'Bunda Larasati',
          description:
            'Sangat senang menyekolahkan anak di sini. Gurunya sangat peduli perkembangan emosional anak, bukan cuma nilai akademis saja.',
          value: 'Wali Murid Kelas 3'
        },
        {
          id: 52,
          title: 'Bapak Hermawan',
          description:
            'Fasilitas IT dan Coding-nya luar biasa. Anak saya jadi punya hobi baru yang sangat produktif semenjak sekolah di sini.',
          value: 'Wali Murid Kelas 5'
        }
      ]
    },
    {
      id: 6,
      type: 'faq',
      title: 'Pertanyaan yang Sering Diajukan',
      is_visible: true,
      sort_order: 6,
      items: [
        {
          id: 61,
          title: 'Bagaimana cara melakukan pendaftaran?',
          description:
            'Anda dapat menekan tombol Daftar Sekarang di atas lalu mengisi formulir secara online, atau datang langsung ke ruang sekretariat pendaftaran.'
        },
        {
          id: 62,
          title: 'Apakah tersedia antar jemput sekolah?',
          description:
            'Ya, sekolah menyediakan armada antar jemput resmi untuk area jangkauan radius maksimal 10 KM dari lokasi sekolah.'
        }
      ]
    }
  ]
}

export const useLandingEditorStore = defineStore('landingEditor', {
  state: () => ({
    landingPage: dummyData, // gunakan dummyData secara instan
    loading: false,
    saving: false,
    error: null,
    sidebarOpen: true
  }),

  actions: {
    async fetchLandingPage() {
      this.loading = true
      this.error = null
      try {
        const res = await service.getMyLandingPage()
        if (res?.data) {
          this.landingPage = res.data
        }
      } catch (err) {
        console.warn('API error, using dummy data instead:', err)
        this.landingPage = dummyData
      } finally {
        this.loading = false
      }
    },

    async saveSettings(data) {
      this.saving = true
      try {
        const res = await service.saveLandingPage(data)
        this.landingPage = res.data
        return res
      } catch (err) {
        // mode offline dummy
        this.landingPage = { ...this.landingPage, ...data }
        return { message: 'Pengaturan disimpan secara lokal (Offline mode)' }
      } finally {
        this.saving = false
      }
    },

    async uploadImage(file, type) {
      this.saving = true
      try {
        const res = await service.uploadLandingImage(file, type)
        await this.fetchLandingPage()
        return res
      } catch (err) {
        // mode offline uploader simulation
        const fakeUrl = URL.createObjectURL(file)
        if (type === 'hero') {
          this.landingPage.hero_image = fakeUrl
        } else if (type === 'about') {
          this.landingPage.about_image = fakeUrl
        }
        return { url: fakeUrl, message: 'Upload simulasi lokal berhasil!' }
      } finally {
        this.saving = false
      }
    },

    async updateSectionOrders(sections) {
      this.saving = true
      try {
        await service.updateSections(sections)
        await this.fetchLandingPage()
      } catch (err) {
        // mode offline reordering
        this.landingPage.sections = sections
      } finally {
        this.saving = false
      }
    },

    async addItem(sectionId, itemData) {
      this.saving = true
      try {
        await service.addSectionItem(sectionId, itemData)
        await this.fetchLandingPage()
      } catch (err) {
        // mode offline add item
        const section = this.landingPage.sections.find(s => s.id === sectionId)
        if (section) {
          const newItem = { id: Date.now(), ...itemData }
          if (!section.items) section.items = []
          section.items.push(newItem)
        }
      } finally {
        this.saving = false
      }
    },

    async updateItem(sectionId, itemId, itemData) {
      this.saving = true
      try {
        await service.updateSectionItem(sectionId, itemId, itemData)
        await this.fetchLandingPage()
      } catch (err) {
        // mode offline edit item
        const section = this.landingPage.sections.find(s => s.id === sectionId)
        if (section && section.items) {
          const idx = section.items.findIndex(item => item.id === itemId)
          if (idx !== -1) {
            section.items[idx] = { ...section.items[idx], ...itemData }
          }
        }
      } finally {
        this.saving = false
      }
    },

    async removeItem(sectionId, itemId) {
      this.saving = true
      try {
        await service.deleteSectionItem(sectionId, itemId)
        await this.fetchLandingPage()
      } catch (err) {
        // mode offline delete item
        const section = this.landingPage.sections.find(s => s.id === sectionId)
        if (section && section.items) {
          section.items = section.items.filter(item => item.id !== itemId)
        }
      } finally {
        this.saving = false
      }
    },

    async togglePublishStatus() {
      this.saving = true
      try {
        const res = await store.togglePublishStatus()
        this.landingPage.is_published = res.is_published
        return res
      } catch (err) {
        this.landingPage.is_published = !this.landingPage.is_published
        return {
          is_published: this.landingPage.is_published,
          message: `Status halaman diubah ke: ${this.landingPage.is_published ? 'Published' : 'Draft'} (Offline)`
        }
      } finally {
        this.saving = false
      }
    }
  }
})
