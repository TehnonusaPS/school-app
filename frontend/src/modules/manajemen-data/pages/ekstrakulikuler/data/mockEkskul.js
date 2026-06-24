export const initialEkskul = [
  {
    id: '1',
    nama: 'Pramuka',
    deskripsi: 'Kegiatan kepramukaan untuk mengembangkan karakter, kemandirian, dan jiwa kepemimpinan siswa.',
    status: 'aktif'
  },
  {
    id: '2',
    nama: 'Paduan Suara',
    deskripsi: 'Ekstrakulikuler paduan suara untuk mengembangkan bakat seni musik dan vokal siswa.',
    status: 'aktif'
  },
  {
    id: '3',
    nama: 'Futsal',
    deskripsi: 'Kegiatan olahraga futsal untuk melatih kerjasama tim dan kebugaran fisik siswa.',
    status: 'aktif'
  },
  {
    id: '4',
    nama: 'Seni Tari',
    deskripsi: 'Ekstrakulikuler tari tradisional dan modern untuk mengembangkan apresiasi seni budaya Indonesia.',
    status: 'nonaktif'
  },
  {
    id: '5',
    nama: 'Robotika',
    deskripsi: 'Kegiatan belajar pemrograman dan perakitan robot sederhana untuk melatih berpikir logis dan kreatif.',
    status: 'aktif'
  },
  {
    id: '6',
    nama: 'Jurnalistik',
    deskripsi: 'Ekstrakulikuler penulisan berita dan fotografi untuk mengembangkan kemampuan komunikasi dan literasi siswa.',
    status: 'nonaktif'
  }
]

const EKSKUL_KEY = 'ekskul_db_v1'

export function getEkskul() {
  const data = localStorage.getItem(EKSKUL_KEY)
  if (!data) {
    localStorage.setItem(EKSKUL_KEY, JSON.stringify(initialEkskul))
    return [...initialEkskul]
  }
  try {
    return JSON.parse(data)
  } catch (e) {
    console.error('Error parsing ekskul database from localStorage', e)
    return [...initialEkskul]
  }
}

export function saveEkskul(data) {
  localStorage.setItem(EKSKUL_KEY, JSON.stringify(data))
}

export const columns = [
  { key: 'nama', label: 'Nama Ekskul' },
  { key: 'deskripsi', label: 'Deskripsi', truncate: true },
  {
    key: 'status',
    label: 'Status',
    badge: true,
    badgeVariant: {
      aktif: 'green',
      nonaktif: 'gray'
    }
  },
  { key: 'actions', label: 'Aksi' }
]

export const filters = [
  {
    type: 'search',
    key: 'search',
    placeholder: 'Cari ekstrakulikuler...'
  },
  {
    type: 'select',
    key: 'status',
    label: 'Status:',
    placeholder: 'Semua Status',
    options: [
      { label: 'Aktif', value: 'aktif' },
      { label: 'Nonaktif', value: 'nonaktif' }
    ]
  }
]
