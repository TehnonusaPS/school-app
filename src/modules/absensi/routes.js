export default [
  {
    path: 'absensi/siswa',
    component: () => import('./pages/AbsensiSiswa.vue'),
    meta: {
      title: 'Absensi Siswa',
      parent: 'Absensi',
      description: 'Rekam dan pantau kehadiran siswa.'
    }
  },
  {
    path: 'absensi/guru-staff',
    component: () => import('./pages/AbsensiGuru.vue'),
    meta: {
      title: 'Absensi Guru & Staff',
      parent: 'Absensi',
      description: 'Rekam dan pantau kehadiran guru dan staff.'
    }
  },
  {
    path: 'absensi/rekap',
    component: () => import('./pages/Rekap.vue'),
    meta: {
      title: 'Rekap Bulanan',
      parent: 'Absensi',
      description: 'Rekap kehadiran bulanan seluruh civitas.'
    }
  }
]
