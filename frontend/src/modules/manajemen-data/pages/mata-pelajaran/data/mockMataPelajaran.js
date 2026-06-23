import { ref, computed } from 'vue'

export const initialSubjects = [
  {
    id: '1',
    kode: 'MAT',
    nama: 'Matematika',
    deskripsi: 'Mata pelajaran matematika dasar mencakup berhitung, logika, geometri, dan pemecahan masalah sederhana.',
    status: 'approved',
    rejectedReason: ''
  },
  {
    id: '2',
    kode: 'IND',
    nama: 'Bahasa Indonesia',
    deskripsi: 'Pengembangan keterampilan berbahasa (membaca, menulis, mendengarkan, berbicara) dan apresiasi sastra Indonesia sejak dini.',
    status: 'approved',
    rejectedReason: ''
  },
  {
    id: '3',
    kode: 'IPA',
    nama: 'Ilmu Pengetahuan Alam (IPA)',
    deskripsi: 'Pembelajaran dasar tentang makhluk hidup, lingkungan sekitar, energi, dan gejala-gejala alam sederhana.',
    status: 'pending',
    rejectedReason: ''
  },
  {
    id: '4',
    kode: 'IPS',
    nama: 'Ilmu Pengetahuan Sosial (IPS)',
    deskripsi: 'Pengenalan lingkungan sosial, sejarah perjuangan bangsa, geografi dasar, dan hubungan kemasyarakatan.',
    status: 'draft',
    rejectedReason: ''
  },
  {
    id: '5',
    kode: 'PJOK',
    nama: 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
    deskripsi: 'Pembinaan kebugaran jasmani, keterampilan motorik, dan pemahaman tentang gaya hidup sehat.',
    status: 'rejected',
    rejectedReason: 'Harap deskripsi dilengkapi dengan cakupan materi senam dan atletik dasar.'
  },
  {
    id: '6',
    kode: 'PANC',
    nama: 'Pendidikan Pancasila dan Kewarganegaraan',
    deskripsi: 'Penanaman nilai-nilai karakter bangsa, pemahaman Pancasila, UUD 1945, dan norma bermasyarakat.',
    status: 'draft',
    rejectedReason: ''
  }
]

const SUBJECTS_KEY = 'academic_subjects_db_v1'

export function getSubjects() {
  const data = localStorage.getItem(SUBJECTS_KEY)
  if (!data) {
    localStorage.setItem(SUBJECTS_KEY, JSON.stringify(initialSubjects))
    return [...initialSubjects]
  }
  try {
    const parsed = JSON.parse(data)
    let migrated = false
    const updated = parsed.map(sub => {
      if (sub.kode && sub.kode.endsWith('-SD')) {
        migrated = true
        return {
          ...sub,
          kode: sub.kode.replace(/-SD$/, '')
        }
      }
      return sub
    })
    if (migrated) {
      localStorage.setItem(SUBJECTS_KEY, JSON.stringify(updated))
      return updated
    }
    return parsed
  } catch (e) {
    console.error('Error parsing subjects database from localStorage', e)
    return [...initialSubjects]
  }
}

export function saveSubjects(subjects) {
  localStorage.setItem(SUBJECTS_KEY, JSON.stringify(subjects))
}

export const columns = [
  { key: 'kode', label: 'Kode Mapel' },
  { key: 'nama', label: 'Nama Mapel' },
  { key: 'deskripsi', label: 'Keterangan', truncate: true },
  { 
    key: 'status', 
    label: 'Status', 
    badge: true,
    badgeVariant: {
      approved: 'green',
      pending: 'amber',
      rejected: 'red',
      draft: 'gray'
    } 
  },
  { key: 'actions', label: 'Aksi' }
]

export const filters = [
  {
    type: 'search',
    key: 'search',
    placeholder: 'Cari mata pelajaran...'
  },
  {
    type: 'select',
    key: 'status',
    label: 'Status:',
    placeholder: 'Semua Status',
    options: [
      { label: 'Disetujui', value: 'approved' },
      { label: 'Menunggu Persetujuan', value: 'pending' },
      { label: 'Ditolak', value: 'rejected' },
      { label: 'Draft', value: 'draft' }
    ]
  }
]
