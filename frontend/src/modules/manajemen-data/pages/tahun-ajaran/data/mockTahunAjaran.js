export const TAHUN_AJARAN_OPTIONS = [
  { label: '2025/2026', value: '2025/2026' },
  { label: '2024/2025', value: '2024/2025' },
  { label: '2026/2027', value: '2026/2027' }
]

export const KELAS_OPTIONS = [
  { label: 'V A', value: 'V A' },
  { label: 'V B', value: 'V B' },
  { label: 'VI A', value: 'VI A' }
]

export const classMetadata = {
  '2025/2026': {
    'V A': { waliKelas: 'Bambang Sutoyo S.pd', siswaCount: 28 },
    'V B': { waliKelas: 'Maudy Ayunda S.pd', siswaCount: 25 },
    'VI A': { waliKelas: 'Reza Rahadian S.pd', siswaCount: 30 }
  },
  '2024/2025': {
    'V A': { waliKelas: 'Bambang Sutoyo S.pd', siswaCount: 24 },
    'V B': { waliKelas: 'Luna Maya S.pd', siswaCount: 22 },
    'VI A': { waliKelas: 'Reza Rahadian S.pd', siswaCount: 26 }
  },
  '2026/2027': {
    'V A': { waliKelas: 'Bambang Sutoyo S.pd', siswaCount: 29 },
    'V B': { waliKelas: 'Maudy Ayunda S.pd', siswaCount: 27 },
    'VI A': { waliKelas: 'Reza Rahadian S.pd', siswaCount: 31 }
  }
}

// Generate some sample students for each class
const namesL = [
  'Ahmad Wibowo', 'Budi Santoso', 'Candra Wijaya', 'Dedi Kurniawan', 'Eko Prasetyo',
  'Fajar Nugroho', 'Gilang Ramadhan', 'Hendra Saputra', 'Indra Lesmana', 'Joko Susilo',
  'Kurniawan Dwi', 'Luthfi Hakim', 'Muhammad Rizky', 'Noval Ardiansyah', 'Oki Setiawan',
  'Pratama Putra', 'Rian Hidayat', 'Setyo Budiman', 'Taufik Hidayat', 'Utomo Putra'
]

const namesP = [
  'Adelia Putri', 'Bella Safira', 'Citra Lestari', 'Dian Sastrowardoyo', 'Elsa Mayori',
  'Fitri Handayani', 'Gita Gutawa', 'Hesti Purwadinata', 'Indah Permatasari', 'Julia Perez',
  'Kartika Sari', 'Larasati Putri', 'Megawati Sukarno', 'Nabila Syakieb', 'Olivia Zalianty',
  'Putri Marino', 'Ririn Dwi', 'Siti Aminah', 'Tia Ariestya', 'Wulan Guritno'
]

function generateStudents() {
  const list = []
  let globalId = 1

  const years = ['2024/2025', '2025/2026', '2026/2027']
  const classes = ['V A', 'V B', 'VI A']

  years.forEach(yr => {
    classes.forEach(cls => {
      const meta = classMetadata[yr][cls]
      const count = meta.siswaCount
      
      for (let i = 0; i < count; i++) {
        const isL = i % 2 === 0
        const namePool = isL ? namesL : namesP
        const baseName = namePool[i % namePool.length]
        const nama = baseName
        
        const nis = String(10000 + globalId)
        const nisn = '00' + String(12345600 + globalId)
        const gender = isL ? 'L' : 'P'
        
        list.push({
          id: String(globalId++),
          nama,
          nis,
          nisn,
          gender,
          kelas: cls,
          tahunAjaran: yr
        })
      }
    })
  })

  return list
}

export const initialSiswaTahunAjaran = generateStudents()

export const initialTahunAjaranList = [
  { id: '1', tahun: '2024/2025', tanggalMulai: '2024-07-15', tanggalSelesai: '2025-06-20', status: 'nonaktif', keterangan: 'Tahun ajaran lalu' },
  { id: '2', tahun: '2025/2026', tanggalMulai: '2025-07-15', tanggalSelesai: '2026-06-20', status: 'aktif', keterangan: 'Tahun ajaran berjalan' },
  { id: '3', tahun: '2026/2027', tanggalMulai: '2026-07-15', tanggalSelesai: '2027-06-20', status: 'nonaktif', keterangan: 'Tahun ajaran mendatang' }
]

const TAHUN_AJARAN_LIST_KEY = 'crud_tahun_ajaran_db_v1'

export function getTahunAjaranList() {
  const data = localStorage.getItem(TAHUN_AJARAN_LIST_KEY)
  if (!data) {
    localStorage.setItem(TAHUN_AJARAN_LIST_KEY, JSON.stringify(initialTahunAjaranList))
    return [...initialTahunAjaranList]
  }
  try {
    return JSON.parse(data)
  } catch (e) {
    console.error('Error parsing tahun ajaran list from localStorage', e)
    return [...initialTahunAjaranList]
  }
}

export function saveTahunAjaranList(list, activeId = null) {
  let targetActiveId = activeId
  if (!targetActiveId) {
    const activeItem = list.find(item => item.status === 'aktif')
    if (activeItem) {
      targetActiveId = activeItem.id
    }
  }
  const processed = list.map(item => ({
    ...item,
    status: targetActiveId && String(item.id) === String(targetActiveId) ? 'aktif' : 'nonaktif'
  }))
  localStorage.setItem(TAHUN_AJARAN_LIST_KEY, JSON.stringify(processed))
  return processed
}
