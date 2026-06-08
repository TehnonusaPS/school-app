import { useAuthStore } from '@/stores/authStore'

export default [
  {
    path: 'komunikasi/pengumuman',
    component: () => import('./pages/admin-yayasan/pengumuman/index.vue'),
    meta: {
      title: 'Pengumuman',
      parent: 'Komunikasi',
      description: 'Buat dan kelola pengumuman sekolah.'
    }
  },
  {
    path: 'komunikasi/berita-kegiatan',
    component: () => import('./pages/kepala-sekolah/berita-kegiatan/index.vue'),
    meta: {
      title: 'Berita Kegiatan',
      parent: 'Komunikasi',
      description: 'Kelola berita kegiatan untuk siswa dan orang tua.'
    }
  },
  {
    path: 'komunikasi/berita-kegiatan/buat',
    component: () => import('./pages/kepala-sekolah/berita-kegiatan/create.vue'),
    meta: {
      title: 'Buat Berita Kegiatan',
      parent: 'Berita Kegiatan',
      description: 'Buat berita kegiatan baru.'
    }
  },
  {
    path: 'komunikasi/berita-kegiatan/edit/:id',
    component: () => import('./pages/kepala-sekolah/berita-kegiatan/edit.vue'),
    meta: {
      title: 'Edit Berita Kegiatan',
      parent: 'Berita Kegiatan',
      description: 'Edit berita kegiatan.'
    }
  },
  {
    path: 'komunikasi/berita-kegiatan/lihat/:id',
    component: () => import('./pages/kepala-sekolah/berita-kegiatan/detail.vue'),
    meta: {
      title: 'Detail Berita Kegiatan',
      parent: 'Berita Kegiatan',
      description: 'Detail informasi berita kegiatan.'
    }
  },
  {
    path: 'komunikasi/feedback',
    component: () => import('./pages/Feedback.vue'),
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
      description: 'Pesan internal antar warga sekolah.',
      requiresAuth: true,
      roles: ['guru', 'wali_kelas', 'siswa', 'orang_tua']
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
    meta: {
      requiresAuth: true,
      roles: ['guru', 'siswa', 'wali_kelas', 'orang_tua']
    },
    redirect: to => {
      const auth = useAuthStore()
      const role = auth.user?.role
      if (role === 'guru') return '/komunikasi/guru/chat'
      if (role === 'wali_kelas') return '/komunikasi/wali-kelas/chat'
      if (role === 'siswa') return '/komunikasi/siswa/chat'
      if (role === 'orang_tua') return '/komunikasi/orang-tua/chat'
      return '/dashboard'
    }
  },
  {
    path: 'komunikasi/guru/chat',
    component: () => import('./pages/guru/chat/index.vue'),
    meta: {
      title: 'Chat Guru',
      parent: 'Komunikasi',
      requiresAuth: true,
      roles: ['guru']
    }
  },
  {
    path: 'komunikasi/wali-kelas/chat',
    component: () => import('./pages/wali-kelas/chat/index.vue'),
    meta: {
      title: 'Chat Wali Kelas',
      parent: 'Komunikasi',
      requiresAuth: true,
      roles: ['wali_kelas']
    }
  },
  {
    path: 'komunikasi/siswa/chat',
    component: () => import('./pages/siswa/chat/index.vue'),
    meta: {
      title: 'Chat Siswa',
      parent: 'Komunikasi',
      requiresAuth: true,
      roles: ['siswa']
    }
  },
  {
    path: 'komunikasi/orang-tua/chat',
    component: () => import('./pages/orang-tua/chat/index.vue'),
    meta: {
      title: 'Chat Orang Tua',
      parent: 'Komunikasi',
      requiresAuth: true,
      roles: ['orang_tua']
    }
  },
  {
    path: 'komunikasi/persuratan/aktif',
    component: () => import('./pages/tata-usaha/surat-aktif/index.vue'),
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
    component: () => import('./pages/tata-usaha/surat-aktif/create.vue'),
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
    component: () => import('./pages/tata-usaha/surat-aktif/edit.vue'),
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
    component: () => import('./pages/tata-usaha/surat-dispensasi/index.vue'),
    meta: {
      title: 'Surat Dispensasi',
      parent: 'Komunikasi',
      description: 'Manajemen surat izin and dispensasi kegiatan siswa.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  },
  {
    path: 'komunikasi/persuratan/dispensasi/buat',
    component: () => import('./pages/tata-usaha/surat-dispensasi/create.vue'),
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
    component: () => import('./pages/tata-usaha/surat-dispensasi/edit.vue'),
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
    component: () => import('./pages/tata-usaha/surat-lulus/index.vue'),
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
    component: () => import('./pages/tata-usaha/surat-lulus/create.vue'),
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
    component: () => import('./pages/tata-usaha/surat-lulus/edit.vue'),
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
    component: () => import('./pages/tata-usaha/surat-peringatan/index.vue'),
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
    component: () => import('./pages/tata-usaha/surat-peringatan/create.vue'),
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
    component: () => import('./pages/tata-usaha/surat-peringatan/edit.vue'),
    meta: {
      title: 'Edit Surat Peringatan ',
      parent: 'Surat Peringatan',
      description: 'Form edit surat peringatan, tagihan, dan pemanggilan.',
      requiresAuth: true,
      roles: ['tata_usaha']
    }
  }
]
