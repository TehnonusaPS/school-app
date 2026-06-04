export default [
  {
    path: 'manajemen-data/siswa',
    component: () => import('./pages/DataSiswa.vue'),
    meta: {
      title: 'Data Siswa',
      parent: 'Manajemen Data',
      description: 'Kelola data seluruh siswa aktif.'
      // roles: ['superadmin', 'admin_sekolah'] <-- Contoh cara membatasi rute
    }
  },
  {
    path: 'manajemen-data/siswa/tambah',
    component: () => import('./pages/create/TambahSiswa.vue'),
    meta: {
      title: 'Tambah Siswa',
      parent: 'Data Siswa'
    }
  },
  {
    path: 'manajemen-data/guru-staff',
    component: () => import('./pages/GuruStaff.vue'),
    meta: {
      title: 'Data Guru dan Staff',
      parent: 'Manajemen Data',
      description: 'Kelola data guru dan tenaga kependidikan.'
    }
  },
  {
    path: 'manajemen-data/kelas',
    component: () => import('./pages/Kelas.vue'),
    meta: {
      title: 'Data Kelas',
      parent: 'Manajemen Data',
      description: 'Kelola pembagian dan struktur kelas.'
    }
  },
  {
    path: 'manajemen-data/mata-pelajaran',
    component: () => import('./pages/MataPelajaran.vue'),
    meta: {
      title: 'Mata Pelajaran',
      parent: 'Manajemen Data',
      description: 'Kelola daftar mata pelajaran yang tersedia.'
    }
  },
  {
    path: 'manajemen-data/tahun-ajaran',
    component: () => import('./pages/TahunAjaran.vue'),
    meta: {
      title: 'Tahun Ajaran Bedasarkan Siswa/Kelas',
      parent: 'Manajemen Data',
      description: 'Data Siswa dan Kelas Bedasarkan Tahun Ajaran.'
    }
  },
  {
    path: 'manajemen-data/yayasan',
    component: () => import('./pages/Yayasan.vue'),
    meta: {
      title: 'Yayasan',
      parent: 'Manajemen Data',
      description: 'Daftar dan kelola informasi yayasan pendidikan terdaftar.'
    }
  },
    {
    path: 'manajemen-data/yayasan/tambah',
    component: () => import('./pages/create/TambahYayasan.vue'),
    meta: {
      title: 'Tambah Yayasan',
      parent: 'Yayasan'
    }
  },
  {
    path: 'manajemen-data/sekolah',
    component: () => import('./pages/Sekolah.vue'),
    meta: {
      title: 'Kelola Data Sekolah dibawah Yayasan',
      parent: 'Manajemen Data',
      description: 'Manajemen daftar unit sekolah di bawah naungan yayasan.'
    }
  },
  {
    path: 'manajemen-data/hak-akses',
    component: () => import('./pages/HakAkses.vue'),
    meta: {
      title: 'Kelola Hak Akses Pengguna',
      parent: 'Manajemen Data',
      description: 'Kelola kewenangan, lisensi, dan hak akses pengguna sistem. mmmmmm'
    }
  },
  {
    path: 'manajemen-data/sekolah-yayasan',
    component: () => import('./pages/SekolahYayasan.vue'),
    meta: {
      title: 'Mengelola Data Sekolah dalam Yayasan',
      parent: 'Manajemen Data',
      description: 'Pengelolaan daftar dan profil unit sekolah di bawah yayasan.'
    }
  },
  {
    path: 'manajemen-data/guru-staff-yayasan',
    component: () => import('./pages/GuruStaffYayasan.vue'),
    meta: {
      title: 'Mengelola Data Guru dan Staff Yayasan',
      parent: 'Manajemen Data',
      description: 'Pengelolaan data tenaga pendidik dan kependidikan di seluruh yayasan.'
    }
  },
  {
    path: 'manajemen-data/monitoring-kelas',
    component: () => import('./pages/MonitoringKelas.vue'),
    meta: {
      title: 'Monitoring Kelas',
      parent: 'Manajemen Data',
      description: 'Pantau status dan kondisi ruang kelas secara berkala.'
    }
  },
  {
    path: 'manajemen-data/ekskul',
    component: () => import('./pages/EkskulData.vue'),
    meta: {
      title: 'Ekstrakulikuler',
      parent: 'Manajemen Data',
      description: 'Daftar data dan pembina kegiatan ekstrakurikuler sekolah.'
    }
  },
  {
    path: 'manajemen-data/jadwal',
    component: () => import('./pages/JadwalData.vue'),
    meta: {
      title: 'Jadwal Pelajaran',
      parent: 'Manajemen Data',
      description: 'Daftar data jadwal pelajaran kelas dan guru pengajar.'
    }
  },
  {
    path: 'manajemen-data/manajemen-kelas',
    component: () => import('./pages/ManajemenKelas.vue'),
    meta: {
      title: 'Manajemen Kelas',
      parent: 'Manajemen Data',
      description: 'Kelola alokasi siswa, wali kelas, dan sarana kelas.'
    }
  }
]
