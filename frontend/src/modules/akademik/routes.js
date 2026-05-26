export default [
  {
    path: 'akademik/jadwal',
    component: () => import('./pages/Jadwal.vue'),
    meta: {
      title: 'Jadwal Pelajaran',
      parent: 'Akademik',
      description: 'Jadwal pelajaran per kelas dan per guru.'
    }
  },
  {
    path: 'akademik/nilai',
    component: () => import('./pages/Nilai.vue'),
    meta: {
      title: 'Nilai & Rapor',
      parent: 'Akademik',
      description: 'Input dan rekap nilai siswa serta pencetakan rapor.'
    }
  },
  {
    path: 'akademik/ujian',
    component: () => import('./pages/Ujian.vue'),
    meta: {
      title: 'Ujian & Penilaian',
      parent: 'Akademik',
      description: 'Manajemen ujian, kuis, dan penilaian harian.'
    }
  },
  {
    path: 'akademik/kurikulum',
    component: () => import('./pages/Kurikulum.vue'),
    meta: {
      title: 'Kurikulum',
      parent: 'Akademik',
      description: 'Pengaturan kurikulum dan silabus pembelajaran.'
    }
  },
  {
    path: 'akademik/mapel',
    component: () => import('./pages/MataPelajaran.vue'),
    meta: {
      title: 'Mata Pelajaran',
      parent: 'Akademik',
      description: 'Lihat Detail Mata Pelajaran.'
    }
  },
  {
    path: 'akademik/ekskul',
    component: () => import('./pages/Ekskul.vue'),
    meta: {
      title: 'Ekstrakurikuler',
      parent: 'Akademik',
      description: 'Manajemen kegiatan ekstrakurikuler siswa.'
    }
  }
]
