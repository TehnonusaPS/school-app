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
    path: 'absensi/siswa/scan',
    component: () => import('./pages/PresensiSiswa.vue'),
    meta: {
      title: 'Mulai Presensi Siswa',
      parent: 'Absensi',
      description: 'Halaman pemindai absensi siswa (Wajah, Fingerprint, RFID).'
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
      title: 'Rekap Absensi',
      parent: 'Absensi',
      description: 'Rekap kehadiran bulanan seluruh civitas.'
    }
  },
  {
    path: 'absensi/input',
    component: () => import('./pages/InputKehadiran.vue'),
    meta: {
      title: 'Kehadiran',
      parent: 'Absensi',
      description: 'Isi lembar kehadiran siswa secara harian dan periodik.'
    }
  }
]
