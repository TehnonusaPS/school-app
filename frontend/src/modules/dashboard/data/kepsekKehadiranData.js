export const kehadiranProgressData = [
  { 
    id: 'siswa',
    label: 'Siswa (Hadir/Total)', 
    value: '400/402', 
    progress: 99, 
    progressClass: 'bg-emerald-500',
    stats: { hadir_pct: 99, alpa_pct: 0, izin_pct: 1 }
  },
  { 
    id: 'guru',
    label: 'Staff & Guru (Hadir/Total)', 
    value: '30/102', 
    progress: 30, 
    progressClass: 'bg-emerald-500',
    stats: { hadir_pct: 30, alpa_pct: 20, izin_pct: 50 }
  }
]

export const kehadiranQuickStats = [
  { label: 'Hadir Siswa', value: '400', valueClass: 'text-emerald-500' },
  { label: 'Tidak Hadir Siswa', value: '2', valueClass: 'text-rose-500' },
  { label: 'Hadir Guru & Staff', value: '30', valueClass: 'text-emerald-500' },
  { label: 'Tidak Hadir Guru & Staff', value: '72', valueClass: 'text-rose-500' }
]
