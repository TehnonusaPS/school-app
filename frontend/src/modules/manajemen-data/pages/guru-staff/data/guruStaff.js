import {
  Plus,
  Download,
  GraduationCap,
  BookCheck,
  School,
  BookX
} from 'lucide-vue-next'
import { ref, computed } from 'vue'
import { toast } from 'vue-sonner'

// Data for StatsCard
export const stats = [
  {
    label: 'TOTAL PEGAWAI',
    value: '187',
    trend: '+8.4% bln ini',
    trendDirection: 'up',
    icon: School,
    variant: 'primary'
  },
  {
    label: 'PEGAWAI AKTIF',
    value: '180',
    trend: '+12 Baru',
    trendDirection: 'up',
    icon: BookCheck,
    variant: 'emerald'
  },
  {
    label: 'TOTAL GURU',
    value: '150',
    sub: 'Kehadiran',
    progress: 98,
    icon: GraduationCap,
    variant: 'amber'
  },
  {
    label: 'TOTAL STAFF',
    value: '30',
    sub: 'Kehadiran',
    progress: 90,
    icon: BookX,
    variant: 'violet'
  }
]

export const filters = [
  {
    type: 'search',
    key: 'search',
    placeholder: 'Cari nama guru/staff disini...'
  },
  {
    type: 'select',
    key: 'status',
    label: 'Filter Status:',
    placeholder: 'Semua',
    options: [
      {
        label: 'Tetap',
        value: 'Tetap'
      },
      {
        label: 'Kontrak',
        value: 'Kontrak'
      },
      {
        label: 'Honorer',
        value: 'Honorer'
      }
    ]
  }
]

const isExporting = ref(false)
export const actions = [
  {
    label: 'Tambah Guru/Staff',
    icon: Plus,
    to: '/manajemen-data/guru-staff/tambah'
  },
  {
    label: 'Export',
    icon: Download,
    variant: 'outline',
    loading: isExporting.value,
    click: () => {
      isExporting.value = true
      setTimeout(() => {
        isExporting.value = false
        toast.success('Export berhasil', {
          description: 'Data berhasil diexport ke format Excel/PDF.'
        })
      }, 1500)
    }
  }
]

export const columns = [
  { key: 'nama', label: 'Nama Lengkap', avatar: true, avatarKey: 'foto' },
  { key: 'nip', label: 'NIP' },
  { key: 'unitKerja', label: 'Unit Kerja' },
  { key: 'jabatan', label: 'Jabatan' },
  { key: 'masaKerja', label: 'Masa Kerja' },
  { key: 'status', label: 'Status Kepegawaian', badge: true,
    badgeVariant: {
      Tetap: 'green',
      Honorer: 'gray',
      Kontrak: 'amber',
    } },
  { key: 'actions', label: 'Aksi' }
]

export const allItems = computed(() => [
  {
    id: 1,
    nama: 'Angga Yunanda',
    nip: '1234567890',
    unitKerja: 'SD Nusantara Pintar Bekasi',
    unitId: 'S0001',
    jabatan: 'Kepala Sekolah',
    masaKerja: '2 tahun 6 bulan',
    status: 'Tetap'
  },
  {
    id: 2,
    nama: 'Michelle Zudith',
    nip: '1234567890',
    unitKerja: 'Yayasan Nusantara Pintar',
    unitId: 'Y0001',
    jabatan: 'Staff Yayasan',
    masaKerja: '2 tahun 6 bulan',
    status: 'Tetap'
  },
  {
    id: 3,
    nama: 'Luna Maya',
    nip: '1234567890',
    unitKerja: 'SD Nusantara Pintar Bekasi',
    unitId: 'S0001',
    jabatan: 'Guru',
    masaKerja: '2 tahun 6 bulan',
    status: 'Kontrak'
  },
  {
    id: 4,
    nama: 'Nadiem Makarim',
    nip: '1234567890',
    unitKerja: 'SD Nusantara Pintar Jakarta Timur',
    unitId: 'S0002',
    jabatan: 'Kepala Sekolah',
    masaKerja: '2 tahun 6 bulan',
    status: 'Tetap'
  },
  {
    id: 5,
    nama: 'Maudy Ayunda',
    nip: '1234567890',
    unitKerja: 'SD Nusantara Pintar Bekasi',
    unitId: 'S0001',
    jabatan: 'Guru',
    masaKerja: '3 tahun 8 bulan',
    status: 'Tetap'
  },
  {
    id: 6,
    nama: 'Tiara Andiri',
    nip: '1234567890',
    unitKerja: 'SD Nusantara Pintar Jakarta Selatan',
    unitId: 'S0004',
    jabatan: 'Guru',
    masaKerja: '2 tahun 6 bulan',
    status: 'Honorer'
  },
  {
    id: 7,
    nama: 'Reza Rahadian',
    nip: '1234567890',
    unitKerja: 'SD Nusantara Pintar Bekasi',
    unitId: 'S0001',
    jabatan: 'Guru',
    masaKerja: '2 tahun 6 bulan',
    status: 'Tetap'
  },
])

export const agamaOptions = [
  {
    label: 'Islam',
    value: 'A01'
  },
  {
    label: 'Kristen',
    value: 'A02'
  },
  {
    label: 'Katolik',
    value: 'A03'
  },
  {
    label: 'Buddha',
    value: 'A04'
  },
  {
    label: 'Hindu',
    value: 'A05'
  },
  {
    label: 'Konghucu',
    value: 'A06'
  },
]

export const statusPernikahanOptions = [
  {
    label: 'Belum Menikah',
    value: 'SP01'
  },
  {
    label: 'Menikah',
    value: 'SP02'
  },
  {
    label: 'Janda',
    value: 'SP03'
  },
  {
    label: 'Duda',
    value: 'SP04'
  }
]

export const kelaminOptions = [
  {
    label: 'Laki-laki',
    value: 'JK01'
  },
  {
    label: 'Perempuan',
    value: 'JK02'
  },
]

export const pendidikanOptions = [
  {
    label: 'Sekolah Dasar (SD)',
    value: 'P01'
  },
  {
    label: 'Sekolah Menengah Pertama (SMP)',
    value: 'P02'
  },
  {
    label: 'Sekolah Menengah Awal/Kejuruan (SMA/SMK)',
    value: 'P03'
  },
  {
    label: 'Diploma I (D1)',
    value: 'P04'
  },
  {
    label: 'Diploma III (D3)',
    value: 'P05'
  },
  {
    label: 'Diploma IV (D4)',
    value: 'P06'
  },
  {
    label: 'Sarjana (S1)',
    value: 'P07'
  },
  {
    label: 'Magister (S2)',
    value: 'P08'
  },
  {
    label: 'Doktoral (S3)',
    value: 'P09'
  },
]

export const jabatanOptions = [
  {
    label: 'Kepala Yayasan',
    value: 'J001'
  },
  {
    label: 'Staff Yayasan',
    value: 'J002'
  },
  {
    label: 'Kepala Sekolah',
    value: 'J003'
  },
  {
    label: 'Guru',
    value: 'J004'
  },
  {
    label: 'Staff Sekolah',
    value: 'J005'
  },
]

export const statusKepegawaianOptions = [
  {
    label: 'Tetap',
    value: 'SK01'
  },
  {
    label: 'Kontrak',
    value: 'SK02'
  },
  {
    label: 'Honorer',
    value: 'SK03'
  },
]

export const unitKerjaOptions = [
  {
    label: 'Yayasan Nusantara Pintar',
    value: 'Y0001'
  },
  {
    label: 'SD Nusantara Pintar Jakarta Timur',
    value: 'S0002'
  },
  {
    label: 'SD Nusantara Pintar Jakarta Selatan',
    value: 'S0004'
  },
  {
    label: 'SD Nusantara Pintar Bekasi',
    value: 'S0001'
  },
  {
    label: 'TK Nusantara Pintar Jakarta Selatan',
    value: 'S0003'
  },
]


export const statusOptions = [
  {
    label: 'Aktif',
    value: 'Aktif'
  },
  {
    label: 'Nonaktif',
    value: 'Nonaktif'
  },
]