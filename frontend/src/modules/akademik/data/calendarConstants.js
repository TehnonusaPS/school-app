export const academicMonths = [
  { name: 'Juli', val: 6, semester: 'Ganjil', yearOffset: 0 },
  { name: 'Agustus', val: 7, semester: 'Ganjil', yearOffset: 0 },
  { name: 'September', val: 8, semester: 'Ganjil', yearOffset: 0 },
  { name: 'Oktober', val: 9, semester: 'Ganjil', yearOffset: 0 },
  { name: 'November', val: 10, semester: 'Ganjil', yearOffset: 0 },
  { name: 'Desember', val: 11, semester: 'Ganjil', yearOffset: 0 },
  { name: 'Januari', val: 0, semester: 'Genap', yearOffset: 1 },
  { name: 'Februari', val: 1, semester: 'Genap', yearOffset: 1 },
  { name: 'Maret', val: 2, semester: 'Genap', yearOffset: 1 },
  { name: 'April', val: 3, semester: 'Genap', yearOffset: 1 },
  { name: 'Mei', val: 4, semester: 'Genap', yearOffset: 1 },
  { name: 'Juni', val: 5, semester: 'Genap', yearOffset: 1 }
]

export const eventCategories = [
  { value: 'holiday', label: 'Libur Sekolah (Meliburkan KBM)' },
  { value: 'exam', label: 'Ujian & Asesmen' },
  { value: 'academic', label: 'Akademik & Rapor' },
  { value: 'activity', label: 'Kegiatan & Proyek Sekolah' }
]

export const eventTypes = [
  // 1. Libur Sekolah
  { value: 'libur_nasional', label: 'Hari Libur Nasional & Cuti Bersama', category: 'holiday', color: 'rose', isHoliday: true },
  { value: 'libur_semester', label: 'Libur Akhir Semester', category: 'holiday', color: 'amber', isHoliday: true },
  { value: 'libur_khusus', label: 'Libur Khusus / Fakultatif', category: 'holiday', color: 'orange', isHoliday: true },
  
  // 2. Ujian & Asesmen
  { value: 'uts', label: 'Ujian Tengah Semester (UTS / PTS)', category: 'exam', color: 'blue', isHoliday: false },
  { value: 'uas', label: 'Ujian Akhir Semester (UAS / PAS)', category: 'exam', color: 'indigo', isHoliday: false },
  { value: 'us', label: 'Ujian Sekolah / Kelulusan', category: 'exam', color: 'purple', isHoliday: false },
  { value: 'anbk', label: 'Asesmen Nasional (ANBK)', category: 'exam', color: 'sky', isHoliday: false },

  // 3. Akademik & Pelaporan Rapor
  { value: 'mpls', label: 'Masa Orientasi / MPLS', category: 'academic', color: 'cyan', isHoliday: false },
  { value: 'rapor', label: 'Pembagian Rapor', category: 'academic', color: 'violet', isHoliday: false },
  { value: 'remedi', label: 'Remedial & Pengayaan', category: 'academic', color: 'teal', isHoliday: false },
  { value: 'rapat_guru', label: 'Rapat Dewan Guru / Pleno', category: 'academic', color: 'slate', isHoliday: false },

  // 4. Kegiatan & Proyek Sekolah
  { value: 'kegiatan', label: 'Kegiatan Sekolah / Pentas', category: 'activity', color: 'emerald', isHoliday: false },
  { value: 'p5', label: 'Proyek Profil Pelajar Pancasila (P5)', category: 'activity', color: 'lime', isHoliday: false },

  // Legacy fallback support
  { value: 'tanggal_merah', label: 'Hari Libur / Tanggal Merah', category: 'holiday', color: 'rose', isHoliday: true },
  { value: 'ujian', label: 'Ujian Akademik', category: 'exam', color: 'blue', isHoliday: false }
]

export const tahunList = [
  '2025/2026',
  '2026/2027',
  '2027/2028',
  '2028/2029',
  '2029/2030'
]

export function getEventTypeInfo(type) {
  const found = eventTypes.find(t => t.value === type)
  if (found) return found
  return { value: type, label: type, category: 'activity', color: 'slate', isHoliday: false }
}

export function getEventBadgeStyle(type) {
  const info = getEventTypeInfo(type)
  switch (info.color) {
    case 'rose':
    case 'red':
      return 'bg-rose-500/15 text-rose-700 dark:text-rose-300 border-rose-500/30'
    case 'amber':
    case 'yellow':
      return 'bg-amber-500/15 text-amber-700 dark:text-amber-300 border-amber-500/30'
    case 'orange':
      return 'bg-orange-500/15 text-orange-700 dark:text-orange-300 border-orange-500/30'
    case 'blue':
      return 'bg-blue-500/15 text-blue-700 dark:text-blue-300 border-blue-500/30'
    case 'indigo':
      return 'bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border-indigo-500/30'
    case 'purple':
      return 'bg-purple-500/15 text-purple-700 dark:text-purple-300 border-purple-500/30'
    case 'sky':
      return 'bg-sky-500/15 text-sky-700 dark:text-sky-300 border-sky-500/30'
    case 'cyan':
      return 'bg-cyan-500/15 text-cyan-700 dark:text-cyan-300 border-cyan-500/30'
    case 'violet':
      return 'bg-violet-500/15 text-violet-700 dark:text-violet-300 border-violet-500/30'
    case 'teal':
      return 'bg-teal-500/15 text-teal-700 dark:text-teal-300 border-teal-500/30'
    case 'slate':
      return 'bg-slate-500/15 text-slate-700 dark:text-slate-300 border-slate-500/30'
    case 'lime':
      return 'bg-lime-500/15 text-lime-700 dark:text-lime-300 border-lime-500/30'
    case 'emerald':
    default:
      return 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/30'
  }
}
