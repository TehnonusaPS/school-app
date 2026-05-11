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
  }
]
