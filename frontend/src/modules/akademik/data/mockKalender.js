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

// Current school year is 2025/2026, and we support up to 3 years in the future:
// 2025/2026, 2026/2027, 2027/2028, 2028/2029, 2029/2030
export const tahunList = [
  '2025/2026',
  '2026/2027',
  '2027/2028',
  '2028/2029',
  '2029/2030'
]

export const eventTypes = [
  { value: 'libur_nasional', label: 'Hari Libur Nasional' },
  { value: 'tanggal_merah', label: 'Tanggal Merah' },
  { value: 'ujian', label: 'Ujian' },
  { value: 'kegiatan', label: 'Kegiatan Sekolah' }
]

// Default status per tahun ajaran
export const initialYearStatuses = {
  '2025/2026': { status: 'approved', rejectedReason: '' },
  '2026/2027': { status: 'pending', rejectedReason: '' },
  '2027/2028': { status: 'draft', rejectedReason: '' },
  '2028/2029': { status: 'draft', rejectedReason: '' },
  '2029/2030': { status: 'draft', rejectedReason: '' }
}

export const initialEvents = [
  // 2025/2026 Academic Year (June 2026 events)
  { id: '1', startDate: '2026-06-01', endDate: '2026-06-01', title: 'Hari Lahir Pancasila', type: 'libur_nasional', description: 'Libur nasional memperingati hari lahir Pancasila.' },
  { id: '2', startDate: '2026-06-08', endDate: '2026-06-10', title: 'Penilaian Akhir Semester (PAS) Genap', type: 'ujian', description: 'Ujian akhir semester genap seluruh kelas.' },
  { id: '3', startDate: '2026-06-15', endDate: '2026-06-15', title: 'Rapat Pleno Kenaikan Kelas', type: 'kegiatan', description: 'Rapat guru dan komite sekolah.' },
  { id: '4', startDate: '2026-06-22', endDate: '2026-06-26', title: 'Libur Akhir Semester Genap', type: 'tanggal_merah', description: 'Libur panjang akhir tahun ajaran.' },
  { id: '5', startDate: '2026-06-13', endDate: '2026-06-13', title: 'Try Out Mandiri Kelas 6', type: 'ujian', description: 'Simulasi ujian khusus di hari Sabtu.' },
  // 2026/2027 Academic Year (July 2026 - June 2027 events)
  { id: '6', startDate: '2026-07-15', endDate: '2026-07-17', title: 'Masa Pengenalan Lingkungan Sekolah (MPLS)', type: 'kegiatan', description: 'Kegiatan orientasi siswa baru kelas 1.' },
  { id: '7', startDate: '2026-12-14', endDate: '2026-12-18', title: 'Penilaian Akhir Semester (PAS) Ganjil', type: 'ujian', description: 'Ujian PAS Semester Ganjil.' },
  { id: '8', startDate: '2027-05-24', endDate: '2027-05-28', title: 'Ujian Sekolah Kelas 6', type: 'ujian', description: 'Ujian akhir sekolah utama bagi siswa kelas 6.' }
]

const EVENTS_KEY = 'academic_calendar_events_db_v2'
const STATUSES_KEY = 'academic_calendar_statuses_db_v2'

export function getEvents() {
  const data = localStorage.getItem(EVENTS_KEY)
  if (!data) {
    localStorage.setItem(EVENTS_KEY, JSON.stringify(initialEvents))
    return [...initialEvents]
  }
  try {
    return JSON.parse(data)
  } catch (e) {
    console.error('Error parsing calendar events from localStorage', e)
    return [...initialEvents]
  }
}

export function saveEvents(events) {
  localStorage.setItem(EVENTS_KEY, JSON.stringify(events))
}

export function getYearStatuses() {
  let data = localStorage.getItem(STATUSES_KEY)
  if (data) {
    try {
      const parsed = JSON.parse(data)
      // Auto-migrate if 2025/2026 is still pending (old setup)
      if (parsed['2025/2026']?.status === 'pending' && parsed['2026/2027']?.status === 'approved') {
        localStorage.removeItem(STATUSES_KEY)
        data = null
      }
    } catch (e) { }
  }

  if (!data) {
    localStorage.setItem(STATUSES_KEY, JSON.stringify(initialYearStatuses))
    return { ...initialYearStatuses }
  }
  try {
    return JSON.parse(data)
  } catch (e) {
    console.error('Error parsing year statuses from localStorage', e)
    return { ...initialYearStatuses }
  }
}

export function saveYearStatuses(statuses) {
  localStorage.setItem(STATUSES_KEY, JSON.stringify(statuses))
}
