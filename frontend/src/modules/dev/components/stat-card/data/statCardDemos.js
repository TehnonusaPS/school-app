// Contoh data API (Backend murni hanya mengirim angka/data mentah)
export const statCardDemosData = {
  siswa: {
    total: 402,
    sub: 'siswa aktif'
  },
  absensi: {
    total: 378,
    sub: '94% hadir',
    trend: '+2% dari kemarin',
    trendDirection: 'up'
  },
  pendaftaran: {
    total: 12,
    sub: 'siswa mendaftar',
    trend: '-4% dari minggu lalu',
    trendDirection: 'down'
  },
  kelulusan: {
    total: '100%',
    sub: 'stabil dibanding tahun lalu',
    trend: '0% perubahan',
    trendDirection: 'neutral'
  },
  anggaran: {
    total: 78.5,
    sub: 'Realisasi Anggaran',
    progress: 78.5
  }
}
