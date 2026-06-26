export default [
  {
    path: 'administrasi/loket',
    component: () => import('./pages/loket.vue'),
    meta: {
      title: 'Pendaftaran Siswa Baru',
      parent: 'Administrasi',
      description: 'Manajemen data loket administrasi.'
    }
  },
  {
    path: 'administrasi/tambah-siswa',
    component: () => import('./pages/tambahsiswa.vue'),
    meta: {
      title: 'Tambah Siswa Baru',
      parent: 'Administrasi',
      description: 'Formulir pendaftaran siswa baru.'
    }
  },
  {
    path: 'administrasi/detail-pendaftaran',
    component: () => import('./pages/detailpendaftar.vue'),
    meta: {
      title: 'Detail Pendaftaran',
      parent: 'Administrasi',
      description: 'Detail informasi pendaftaran siswa.'
    }
  },
  {
    path: 'administrasi/edit-pendaftaran',
    component: () => import('./pages/editpendaftar.vue'),
    meta: {
      title: 'Edit Data Siswa',
      parent: 'Administrasi',
      description: 'Perbarui informasi registrasi calon peserta didik.'
    }
  }
]
