import {
  Building2,
  ShieldCheck,
  ShieldAlert,
  ShieldX,
  Plus,
  Download,
} from 'lucide-vue-next'
import { computed } from 'vue'

// Data for StatsCard
export const stats = [
  {
    label: 'TOTAL YAYASAN',
    value: '123',
    trend: '+8.4% bln ini',
    trendDirection: 'up',
    icon: Building2,
    variant: 'primary'
  },
  {
    label: 'YAYASAN AKTIF',
    value: '112',
    trend: '+12 Baru',
    trendDirection: 'up',
    icon: ShieldCheck,
    variant: 'emerald'
  },
  {
    label: 'YAYASAN SEDANG TRIAL',
    value: '8',
    trend: '-2 Yayasan',
    trendDirection: 'down',
    icon: ShieldAlert,
    variant: 'amber'
  },
  {
    label: 'YAYASAN TIDAK AKTIF',
    value: '3',
    trend: '-1 Yayasan',
    trendDirection: 'up',
    icon: ShieldX,
    variant: 'violet'
  }
]

export const columns = [
  { key: 'no', label: 'No.' },
  { key: 'yayasan', label: 'Nama Yayasan' },
  { key: 'jmlSekolah', label: 'Jumlah Sekolah' },
  { key: 'alamat', label: 'Alamat Yayasan' },
  { key: 'jmlSiswa', label: 'Jumlah Siswa' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Aksi' }
]

export const filters = [
  {
    type: 'search',
    key: 'search',
    placeholder: 'Cari nama yayasan disini...'
  },
  {
    type: 'select',
    key: 'status',
    label: 'Filter Status:',
    placeholder: 'Semua Status',
    options: [
      {
        label: 'Aktif',
        value: 'active'
      },
      {
        label: 'Non Aktif',
        value: 'inactive'
      },
      {
        label: 'Trial',
        value: 'trial'
      }
    ]
  }
]

export const actions = [
  {
    label: 'Tambah Yayasan Baru',
    icon: Plus,
    to: '/manajemen-data/yayasan/tambah'
  },
  {
    label: 'Export',
    icon: Download,
    variant: 'outline'
  }
]

export const items = computed(() => [
  {
    id: 1,
    initial: 'NP',
    yayasan: 'Yayasan Nusantara Pintar',
    jmlSekolah: '2',
    alamat: 'Jl. Lorem Ipsum Dolor Sit No. 123, Kel. Lorem, Kec. Lorem Ipsum, Lorem Ipsum 123456',
    jmlSiswa: '612',
    status: 'Aktif',
  },
  {
    id: 2,
    initial: 'HB',
    yayasan: 'Yayasan Harapan Bangsa',
    jmlSekolah: '1',
    alamat: 'Jl. Lorem Ipsum Dolor Sit No. 123, Kel. Lorem, Kec. Lorem Ipsum, Lorem Ipsum 123456',
    jmlSiswa: '248',
    status: 'Non Aktif',
  },
  {
    id: 3,
    initial: 'SI',
    yayasan: 'Yayasan Sinar Ilmu',
    jmlSekolah: '3',
    alamat: 'Jl. Lorem Ipsum Dolor Sit No. 123, Kel. Lorem, Kec. Lorem Ipsum, Lorem Ipsum 123456',
    jmlSiswa: '937',
    status: 'Trial',
  },
])