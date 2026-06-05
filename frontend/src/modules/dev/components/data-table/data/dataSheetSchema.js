import {
  User,
  Fingerprint,
  BookOpen,
  Mail,
  Award,
  Activity,
  Calendar,
  MapPin
} from 'lucide-vue-next'

export const studentSections = [
  {
    id: 'informasi-utama',
    title: 'Informasi Akademik',
    fields: [
      { label: 'Nama Lengkap', key: 'nama', icon: User },
      { label: 'NISN', key: 'nisn', icon: Fingerprint },
      { label: 'Kelas', key: 'kelas', icon: BookOpen }
    ]
  },
  {
    id: 'kontak-status',
    title: 'Kontak & Status',
    fields: [
      { label: 'Email', key: 'email', icon: Mail },
      { label: 'Nilai Rata-rata', key: 'nilai', icon: Award },
      { label: 'Status Keaktifan', key: 'status', icon: Activity }
    ]
  },
  {
    id: 'registrasi-alamat',
    title: 'Registrasi & Alamat',
    fields: [
      { label: 'Tanggal Masuk', key: 'tanggal_masuk', icon: Calendar },
      { label: 'Alamat Tinggal', key: 'alamat', icon: MapPin, textarea: true }
    ]
  }
]
