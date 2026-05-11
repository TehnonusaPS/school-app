export default [
  {
    path: 'laporan/akademik',
    component: () => import('./pages/LaporanAkademik.vue'),
    meta: {
      title: 'Laporan Akademik',
      parent: 'Laporan',
      description: 'Laporan nilai, kelulusan, dan pencapaian akademik.'
    }
  },
  {
    path: 'laporan/keuangan',
    component: () => import('./pages/LaporanKeuangan.vue'),
    meta: {
      title: 'Laporan Keuangan',
      parent: 'Laporan',
      description: 'Laporan pemasukan, pengeluaran, dan neraca keuangan.'
    }
  },
  {
    path: 'laporan/absensi',
    component: () => import('./pages/LaporanAbsensi.vue'),
    meta: {
      title: 'Laporan Absensi',
      parent: 'Laporan',
      description: 'Laporan rekap kehadiran periode tertentu.'
    }
  },
  {
    path: 'laporan/ekspor',
    component: () => import('./pages/Ekspor.vue'),
    meta: {
      title: 'Ekspor Data',
      parent: 'Laporan',
      description: 'Ekspor data ke format Excel, PDF, atau CSV.'
    }
  }
]
