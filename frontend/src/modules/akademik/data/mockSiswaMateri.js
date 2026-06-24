import { Play, FileText, Download } from 'lucide-vue-next'

export const mockMateriList = [
  {
    title: 'Bab 1 : Lorem Ipsum Lored De',
    description: 'Diunduh Kemarin · 2.4 MB',
    variant: 'green',
    leadingIcon: Play,
    trailingIcon: Download
  },
  {
    title: 'Ujian : "Bab 1 - Lorem Ipsum Lored De"',
    description: 'Dua hari yang lalu · Nilai : 80',
    variant: 'purple',
    leadingIcon: FileText,
    trailingIcon: null
  },
  {
    title: 'Tugas : "Bab 1 - Lorem Ipsum Lored De"',
    description: 'Dua hari yang lalu · Nilai : 80',
    variant: 'amber',
    leadingIcon: FileText,
    trailingIcon: null
  }
]
