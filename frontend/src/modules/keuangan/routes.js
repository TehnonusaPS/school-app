export default [
  {
    path: 'keuangan/spp',
    component: () => import('./pages/Spp.vue'),
    meta: {
      title: 'SPP Siswa',
      parent: 'Keuangan',
      description: 'Kelola pembayaran SPP bulanan siswa.'
    }
  },
  {
    path: 'keuangan/tagihan',
    component: () => import('./pages/Tagihan.vue'),
    meta: {
      title: 'Tagihan & Pembayaran',
      parent: 'Keuangan',
      description: 'Riwayat dan status tagihan pembayaran.'
    }
  },
  {
    path: 'keuangan/pengeluaran',
    component: () => import('./pages/Pengeluaran.vue'),
    meta: {
      title: 'Pengeluaran',
      parent: 'Keuangan',
      description: 'Catatan pengeluaran operasional sekolah.'
    }
  },
  {
    path: 'keuangan/laporan',
    component: () => import('./pages/Laporan.vue'),
    meta: {
      title: 'Laporan Keuangan',
      parent: 'Keuangan',
      description: 'Laporan keuangan bulanan dan tahunan.'
    }
  },
  {
    path: 'keuangan/cetak-kwitansi',
    component: () => import('./pages/CetakKwitansi.vue'),
    meta: {
      title: 'Cetak Kwitansi',
      parent: 'Keuangan',
      description: 'Cetak kwitansi pembayaran SPP dan pengeluaran.'
  }
}
]
