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
      title: 'Ujian',
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
  },
  {
    path: 'akademik/kalender',
    component: () => import('./pages/KalenderAkademik.vue'),
    meta: {
      title: 'Kalender Akademik',
      parent: 'Akademik',
      description: 'Kalender kegiatan pembelajaran dan hari libur sekolah.'
    }
  },
  {
    path: 'akademik/kegiatan',
    component: () => import('./pages/KegiatanAkademik.vue'),
    meta: {
      title: 'Kegiatan akademik sekolah',
      parent: 'Akademik',
      description: 'Daftar rincian kegiatan pembelajaran dan ujian sekolah.'
    }
  },
  {
    path: 'akademik/materi',
    component: () => import('./pages/KelolaMateri.vue'),
    meta: {
      title: 'Materi',
      parent: 'Akademik',
      description: 'Unggah, sunting, dan bagikan materi ajar interaktif.'
    }
  },
  {
    path: 'akademik/tugas',
    component: () => import('./pages/KelolaTugas.vue'),
    meta: {
      title: 'Tugas',
      parent: 'Akademik',
      description: 'Buat tugas baru dan pantau pengumpulan tugas siswa.'
    }
  },
  {
    path: 'akademik/input-nilai',
    component: () => import('./pages/InputNilai.vue'),
    meta: {
      title: 'Input Nilai',
      parent: 'Akademik',
      description: 'Lakukan penginputan nilai harian, UTS, UAS, dan rapor.'
    }
  }
]
