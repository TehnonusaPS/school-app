<script setup>
import { Users, CheckCircle, BookOpen, CreditCard, Bell, BarChart3 } from 'lucide-vue-next'

const features = [
  {
    icon: Users,
    title: 'Manajemen Data Terpusat',
    desc: 'Kelola data siswa, guru, staf, dan orang tua seluruh unit sekolah Anda dalam satu platform terintegrasi.',
    color: 'bg-blue-50', iconColor: 'text-blue-600', border: 'border-blue-100'
  },
  {
    icon: CheckCircle,
    title: 'Absensi Digital',
    desc: 'Rekap kehadiran siswa dan pengajar secara real-time. Laporan otomatis langsung ke orang tua dan pimpinan.',
    color: 'bg-sky-50', iconColor: 'text-sky-600', border: 'border-sky-100'
  },
  {
    icon: BookOpen,
    title: 'Manajemen Akademik',
    desc: 'Nilai, jadwal pelajaran, kurikulum, hingga raport digital — semua terkelola rapi dan mudah diakses.',
    color: 'bg-indigo-50', iconColor: 'text-indigo-600', border: 'border-indigo-100'
  },
  {
    icon: CreditCard,
    title: 'Keuangan Sekolah',
    desc: 'Pantau SPP, pengeluaran, dan laporan keuangan yayasan secara transparan dan akuntabel.',
    color: 'bg-green-50', iconColor: 'text-green-600', border: 'border-green-100'
  },
  {
    icon: Bell,
    title: 'Komunikasi Internal',
    desc: 'Pengumuman, pesan antara guru dan orang tua, serta notifikasi penting tersampai tepat waktu.',
    color: 'bg-orange-50', iconColor: 'text-orange-600', border: 'border-orange-100'
  },
  {
    icon: BarChart3,
    title: 'Laporan & Analitik',
    desc: 'Dasbor laporan komprehensif untuk kepala sekolah dan pengurus yayasan dalam mengambil keputusan.',
    color: 'bg-purple-50', iconColor: 'text-purple-600', border: 'border-purple-100'
  },
]
</script>

<template>
  <section id="fitur" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
      <div class="text-center mb-14">
        <span class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold mb-3">Fitur Platform</span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Semua yang Sekolah Anda Butuhkan</h2>
        <p class="text-gray-500 max-w-xl mx-auto text-sm">
          Dari absensi harian hingga laporan keuangan yayasan — semua tersedia dalam satu sistem yang terintegrasi dan mudah digunakan.
        </p>
      </div>

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="f in features" :key="f.title"
          :class="['rounded-2xl border p-6 hover:-translate-y-1 transition-all duration-300 hover:shadow-lg', f.color, f.border]">
          <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center mb-4 shadow-sm">
            <component :is="f.icon" :class="['w-5 h-5', f.iconColor]" />
          </div>
          <h3 class="text-base font-bold text-gray-900 mb-2">{{ f.title }}</h3>
          <p class="text-gray-500 text-sm leading-relaxed">{{ f.desc }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
