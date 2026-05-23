export default [
  {
    path: 'komunikasi/pengumuman',
    component: () => import('./pages/Pengumuman.vue'),
    meta: {
      title: 'Pengumuman',
      parent: 'Komunikasi',
      description: 'Buat dan kelola pengumuman sekolah.'
    }
  },
  {
    path: 'komunikasi/berita-kegiatan',
    component: () => import('./pages/BeritaKegiatan.vue'),
    meta: {
      title: 'Berita Kegiatan',
      parent: 'Komunikasi',
      description: 'Kelola berita kegiatan untuk siswa dan orang tua.'
    }
  },
  {
    path: 'komunikasi/berita-kegiatan/buat',
    component: () => import('./pages/BuatBeritaKegiatan.vue'),
    meta: {
      title: 'Buat Berita Kegiatan',
      parent: 'Komunikasi',
      description: 'Buat berita kegiatan baru.'
    }
  },
  {
    path: 'komunikasi/berita-kegiatan/edit/:id',
    component: () => import('./pages/EditBeritaKegiatan.vue'),
    meta: {
      title: 'Edit Berita Kegiatan',
      parent: 'Komunikasi',
      description: 'Edit berita kegiatan.'
    }
  },
  {
    path: 'komunikasi/berita-kegiatan/lihat/:id',
    component: () => import('./pages/DetailBeritaKegiatan.vue'),
    meta: {
      title: 'Detail Berita Kegiatan',
      parent: 'Komunikasi',
      description: 'Detail informasi berita kegiatan.'
    }
  },
  {
    path: 'komunikasi/feedback',
    component: () => import('./pages/FeedbackOrangTua.vue'),
    meta: {
      title: 'Feedback Orang Tua',
      parent: 'Komunikasi',
      description: 'Daftar masukan dan saran dari orang tua murid.'
    }
  },
  {
    path: 'komunikasi/pesan',
    component: () => import('./pages/Pesan.vue'),
    meta: {
      title: 'Pesan Internal',
      parent: 'Komunikasi',
      description: 'Pesan internal antar warga sekolah.'
    }
  },
  {
    path: 'komunikasi/notifikasi',
    component: () => import('./pages/Notifikasi.vue'),
    meta: {
      title: 'Notifikasi',
      parent: 'Komunikasi',
      description: 'Pengaturan notifikasi dan pemberitahuan.'
    }
  },
  {
    path: 'komunikasi/chat',
    component: () => import('./pages/Chat.vue'),
    meta: {
      title: 'Chat',
      parent: 'Komunikasi',
      description: 'Pesan dan percakapan antara guru dan wali murid.',
      requiresAuth: true,
      roles: ['guru', 'siswa']
    }
  }
]
