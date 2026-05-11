export default [
  {
    path: 'manajemen-data/siswa',
    component: () => import('./pages/DataSiswa.vue'),
    meta: {
      title: 'Data Siswa',
      parent: 'Manajemen Data',
      description: 'Kelola data seluruh siswa aktif.'
    }
  },
  {
    path: 'manajemen-data/siswa/tambah',
    component: () => import('./pages/TambahSiswa.vue'),
    meta: {
      title: 'Tambah Siswa',
      parent: 'Data Siswa'
    }
  },
  {
    path: 'manajemen-data/guru-staff',
    component: () => import('./pages/GuruStaff.vue'),
    meta: {
      title: 'Data Guru & Staff',
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
      title: 'Data Mata Pelajaran',
      parent: 'Manajemen Data',
      description: 'Kelola daftar mata pelajaran yang tersedia.'
    }
  },
  {
    path: 'manajemen-data/tahun-ajaran',
    component: () => import('./pages/TahunAjaran.vue'),
    meta: {
      title: 'Tahun Ajaran',
      parent: 'Manajemen Data',
      description: 'Pengaturan periode tahun ajaran aktif.'
    }
  }
]
