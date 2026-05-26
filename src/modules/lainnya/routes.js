import path from 'node:path';

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
  },
  {
    path: 'lainnya/ruangan',
    component: () => import('./pages/Ruangan.vue'),
    meta:{
      title: 'Manajemen Ruangan',
      parent: 'Lainnya',
      description: 'Pengelolaan data ruangan sekolah.'
    }
  },
  {
    path: 'lainnya/aset',
    component: () => import('./pages/Aset.vue'),
    meta:{
      title: 'Manajemen Aset',
      parent: 'Lainnya',
      description: 'Pengelolaan data aset sekolah.'
    }
  },
  {
    path: 'lainnya/perpustakaan',
    component: () => import('./pages/Perpustakaan.vue'),
    meta:{
      title: 'Manajemen Perpustakaan',
      parent: 'Lainnya',
      description: 'Pengelolaan data perpustakaan sekolah.'
    }
  }]
