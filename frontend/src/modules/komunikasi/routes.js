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
      parent: 'Berita Kegiatan',
      description: 'Buat berita kegiatan baru.'
    }
  },
  {
    path: 'komunikasi/berita-kegiatan/edit/:id',
    component: () => import('./pages/EditBeritaKegiatan.vue'),
    meta: {
      title: 'Edit Berita Kegiatan',
      parent: 'Berita Kegiatan',
      description: 'Edit berita kegiatan.'
    }
  },
  {
    path: 'komunikasi/berita-kegiatan/lihat/:id',
    component: () => import('./pages/DetailBeritaKegiatan.vue'),
    meta: {
      title: 'Detail Berita Kegiatan',
      parent: 'Berita Kegiatan',
      description: 'Detail informasi berita kegiatan.'
    }
  },
  {
    path: 'komunikasi/feedback',
    component: () => import('./pages/FeedbackOrangTua.vue'),
    meta: {
      title: 'Feedback',
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
      roles: ['guru', 'siswa', 'wali_kelas', 'orang_tua']
    }
  },
  {
    path: 'komunikasi/persuratan/aktif',
    component: () => import('./pages/SuratKeteranganAktif.vue'),
    meta: {
      title: 'Surat Keterangan Aktif',
      parent: 'Komunikasi',
      description: 'Daftar surat keterangan siswa aktif yang diterbitkan.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/aktif/buat',
    component: () => import('./pages/BuatSuratAktif.vue'),
    meta: {
      title: 'Buat Surat Keterangan Aktif',
      parent: 'Surat Keterangan Aktif',
      description: 'Form pembuatan surat keterangan siswa aktif.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/aktif/edit/:id',
    component: () => import('./pages/EditSuratAktif.vue'),
    meta: {
      title: 'Edit Surat Keterangan Aktif',
      parent: 'Surat Keterangan Aktif',
      description: 'Form edit surat keterangan siswa aktif.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/dispensasi',
    component: () => import('./pages/SuratDispensasi.vue'),
    meta: {
      title: 'Surat Dispensasi',
      parent: 'Komunikasi',
      description: 'Manajemen surat izin dan dispensasi kegiatan siswa.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/dispensasi/buat',
    component: () => import('./pages/BuatSuratDispensasi.vue'),
    meta: {
      title: 'Buat Surat Dispensasi',
      parent: 'Surat Dispensasi',
      description: 'Form pembuatan surat izin atau dispensasi siswa.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/dispensasi/edit/:id',
    component: () => import('./pages/EditSuratDispensasi.vue'),
    meta: {
      title: 'Edit Surat Dispensasi',
      parent: 'Surat Dispensasi',
      description: 'Form edit surat izin atau dispensasi siswa.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/lulus',
    component: () => import('./pages/SuratKeteranganLulus.vue'),
    meta: {
      title: 'Surat Keterangan Lulus',
      parent: 'Komunikasi',
      description: 'Penerbitan Surat Keterangan Lulus (SKL) sementara.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/lulus/buat',
    component: () => import('./pages/BuatSuratLulus.vue'),
    meta: {
      title: 'Buat Surat Keterangan Lulus',
      parent: 'Surat Keterangan Lulus',
      description: 'Form pembuatan Surat Keterangan Lulus (SKL) sementara.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/lulus/edit/:id',
    component: () => import('./pages/EditSuratLulus.vue'),
    meta: {
      title: 'Edit Surat Keterangan Lulus',
      parent: 'Surat Keterangan Lulus',
      description: 'Form edit Surat Keterangan Lulus (SKL) sementara.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/peringatan',
    component: () => import('./pages/SuratPeringatan.vue'),
    meta: {
      title: 'Peringatan',
      parent: 'Komunikasi',
      description: 'Manajemen surat peringatan, tagihan, dan pemanggilan.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/peringatan/buat',
    component: () => import('./pages/BuatSuratPeringatan.vue'),
    meta: {
      title: 'Buat Surat Peringatan',
      parent: 'Surat Peringatan',
      description: 'Form pembuatan surat peringatan, tagihan, dan pemanggilan.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/peringatan/edit/:id',
    component: () => import('./pages/EditSuratPeringatan.vue'),
    meta: {
      title: 'Edit Surat Peringatan ',
      parent: 'Surat Peringatan',
      description: 'Form edit surat peringatan, tagihan, dan pemanggilan.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  }
]
