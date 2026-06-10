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
      title: 'Hak Akses',
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
    meta: {
      title: 'Ruangan',
      parent: 'Lainnya',
      description: 'Pengelolaan data ruangan sekolah.'
    }
  },
  {
    path: 'lainnya/ruangan/tambah',
    component: () => import('./pages/AddRuangan.vue'),
    meta: {
      title: 'Tambah Ruangan',
      parent: 'Lainnya',
      description: 'Form untuk menambahkan data ruangan baru.'
    }
  },
  {
    path: 'lainnya/ruangan/edit/:id',
    component: () => import('./pages/EditRuangan.vue'),
    meta: {
      title: 'Edit Ruangan',
      parent: 'Lainnya',
      description: 'Form untuk mengedit data ruangan.'
    }
  },
  {
    path: 'lainnya/aset',
    component: () => import('./pages/Aset.vue'),
    meta: {
      title: 'Aset',
      parent: 'Lainnya',
      description: 'Pengelolaan data aset sekolah.',
      roles: ['admin_sekolah']
    }
  },
  {
    path: 'lainnya/aset/tambah',
    component: () => import('./pages/AddAset.vue'),
    meta: {
      title: 'Tambah Aset',
      parent: 'Lainnya',
      description: 'Form untuk menambahkan data aset baru.',
      roles: ['admin_sekolah']
    }
  },
  {
    path: 'lainnya/aset/edit/:id',
    component: () => import('./pages/EditAset.vue'),
    meta: {
      title: 'Edit Aset',
      parent: 'Lainnya',
      description: 'Form untuk mengedit data aset.',
      roles: ['admin_sekolah']
    }
  },
  {
    path: 'lainnya/perpustakaan',
    component: () => import('./pages/Perpustakaan.vue'),
    meta: {
      title: 'Perpustakaan',
      parent: 'Lainnya',
      description: 'Pengelolaan data buku di perpustakaan sekolah.',
      roles: ['admin_sekolah']
    }
  },
  {
    path: 'lainnya/perpustakaan/tambah',
    component: () => import('./pages/AddPerpustakaan.vue'),
    meta: {
      title: 'Tambah Buku Perpustakaan',
      parent: 'Lainnya',
      description: 'Form untuk menambahkan data buku baru.',
      roles: ['admin_sekolah']
    }
  },
  {
    path: 'lainnya/perpustakaan/edit/:id',
    component: () => import('./pages/EditPerpustakaan.vue'),
    meta: {
      title: 'Edit Buku',
      parent: 'Lainnya',
      description: 'Form untuk mengedit data buku.',
      roles: ['admin_sekolah']
    }
  },
  {
    path: 'lainnya/konfigurasi-global',
    component: () => import('./pages/KonfigurasiGlobal.vue'),
    meta: {
      title: 'Konfigurasi Sistem Global',
      parent: 'Lainnya',
      description: 'Pengaturan lisensi, audit sistem, dan konfigurasi global dasbor.',
      roles: ['superadmin']
    }
  },
  {
    path: 'lainnya/informasi-perpustakaan',
    component: () => import('./pages/InformasiPerpustakaanSekolah.vue'),
    meta: {
      title: 'Informasi Perpustakaan Sekolah',
      parent: 'Lainnya',
      description: 'Detail dan informasi umum mengenai perpustakaan sekolah.',
      roles: ['kepala_sekolah', 'admin_yayasan']
    }
  },
  {
    path: 'lainnya/informasi-aset',
    component: () => import('./pages/InformasiAsetSekolah.vue'),
    meta: {
      title: 'Informasi Aset Sekolah',
      parent: 'Lainnya',
      description: 'Detail dan informasi umum mengenai aset sekolah.',
      roles: ['kepala_sekolah', 'admin_yayasan']
    }
  },
  {
    path: 'lainnya/informasi-ruangan',
    component: () => import('./pages/InformasiRuanganSekolah.vue'),
    meta: {
      title: 'Informasi Ruangan Sekolah',
      parent: 'Lainnya',
      description: 'Detail dan informasi umum mengenai ruangan sekolah.',
      roles: ['kepala_sekolah', 'admin_yayasan']
    }
  }
]
