export default [
  {
    path: 'lainnya/pengaturan',
    component: () => import('./pages/Pengaturan.vue'),
    meta: {
      title: 'Pengaturan Sekolah',
      parent: 'Lainnya',
      description: 'Konfigurasi profil dan pengaturan umum sekolah.'
    }
  },
  {
    path: 'lainnya/pengguna',
    component: () => import('./pages/Pengguna.vue'),
    meta: {
      title: 'Manajemen Pengguna',
      parent: 'Lainnya',
      description: 'Kelola akun pengguna dan hak akses.'
    }
  },
  {
    path: 'lainnya/backup',
    component: () => import('./pages/Backup.vue'),
    meta: {
      title: 'Backup & Restore',
      parent: 'Lainnya',
      description: 'Backup dan pemulihan data aplikasi.'
    }
  }
]
