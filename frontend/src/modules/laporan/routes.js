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
  },
  {
    path: 'laporan/konsolidasi',
    component: () => import('./pages/Konsolidasi.vue'),
    meta: {
      title: 'Laporan Konsolidasi Seluruh Yayasan',
      parent: 'Laporan',
      description: 'Akses integrasi seluruh laporan keuangan & akademik sekolah di bawah yayasan.'
    }
  },
  {
    path: 'laporan/siswa-yayasan',
    component: () => import('./pages/SiswaYayasan.vue'),
    meta: {
      title: 'Data siswa',
      parent: 'Laporan',
      description: 'Laporan statistik data siswa di seluruh unit sekolah di bawah yayasan.'
    }
  },
  {
    path: 'laporan/keuangan-yayasan',
    component: () => import('./pages/KeuanganYayasan.vue'),
    meta: {
      title: 'Keuangan',
      parent: 'Laporan',
      description: 'Laporan komprehensif pemasukan dan transaksi keuangan unit sekolah.'
    }
  },
  {
    path: 'laporan/akademik-yayasan',
    component: () => import('./pages/AkademikYayasan.vue'),
    meta: {
      title: 'Akademik',
      parent: 'Laporan',
      description: 'Rekapitulasi kurikulum dan prestasi belajar per unit sekolah.'
    }
  },
  {
    path: 'laporan/sdm-yayasan',
    component: () => import('./pages/SdmYayasan.vue'),
    meta: {
      title: 'SDM',
      parent: 'Laporan',
      description: 'Data dan laporan kinerja pendidik dan tenaga kependidikan di bawah yayasan.'
    }
  },
  {
    path: 'laporan/infrastruktur-yayasan',
    component: () => import('./pages/InfrastrukturYayasan.vue'),
    meta: {
      title: 'Infrastruktur',
      parent: 'Laporan',
      description: 'Rekapitulasi inventaris sarana dan prasarana per unit sekolah.'
    }
  },
  {
    path: 'laporan/kepegawaian',
    component: () => import('./pages/KepegawaianLaporan.vue'),
    meta: {
      title: 'Kepegawaian',
      parent: 'Laporan',
      description: 'Laporan rekapitulasi data kehadiran dan status kepegawaian staf.'
    }
  },
  {
    path: 'laporan/pertanggung-jawaban',
    component: () => import('./pages/PertanggungJawabanLaporan.vue'),
    meta: {
      title: 'Laporan pertanggung Jawaban',
      parent: 'Laporan',
      description: 'Dokumen pelaporan pertanggungjawaban kegiatan operasional sekolah.'
    }
  },
  {
    path: 'laporan/nilai',
    component: () => import('./pages/LaporanNilai.vue'),
    meta: {
      title: 'Laporan Nilai',
      parent: 'Laporan',
      description: 'Rekapitulasi nilai akademis seluruh siswa per mata pelajaran.'
    }
  },
  {
    path: 'laporan/kehadiran-siswa',
    component: () => import('./pages/LaporanKehadiranSiswa.vue'),
    meta: {
      title: 'Laporan Kehadiran Siswa',
      parent: 'Laporan',
      description: 'Rekapitulasi persentase dan daftar ketidakhadiran siswa.'
    }
  },
  {
    path: 'laporan/raport',
    component: () => import('./pages/LaporanRaportSiswa.vue'),
    meta: {
      title: 'Laporan Raport Siswa',
      parent: 'Laporan',
      description: 'Rekapitulasi nilai akhir semester dan pencetakan rapor siswa perwalian.'
    }
  },
  {
    path: 'laporan/perkembangan',
    component: () => import('./pages/PerkembanganAnak.vue'),
    meta: {
      title: 'Perkembangan Anak',
      parent: 'Laporan',
      description: 'Pantau grafik perkembangan nilai, kehadiran, dan kedisiplinan anak.'
    }
  }
]
