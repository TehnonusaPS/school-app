import { Briefcase, GraduationCap, Calendar, Mail, Phone, MapPin } from 'lucide-vue-next'

export const guruSheetSections = [
  {
    id: 'akademik',
    title: 'Detail Akademik',
    fields: [
      { label: 'Jabatan', key: 'jabatan', icon: Briefcase },
      { label: 'Pendidikan Terakhir', key: 'pendidikan', icon: GraduationCap },
      { label: 'Tahun Mulai Mengajar', key: 'tahun_mengajar', icon: Calendar }
    ]
  },
  {
    id: 'kontak',
    title: 'Informasi Kontak',
    fields: [
      { label: 'Email Resmi', key: 'email', icon: Mail },
      { label: 'Nomor Telepon', key: 'telp', icon: Phone },
      { label: 'Alamat Rumah', key: 'alamat', icon: MapPin, textarea: true }
    ]
  }
]
