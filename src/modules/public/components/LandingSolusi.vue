<script setup>
import { Users, Building2, School } from 'lucide-vue-next'
import adminImg from '@/assets/images/landing-admin.png'

const audiences = [
  {
    icon: Building2,
    title: 'Yayasan Pendidikan',
    desc: 'Awasi seluruh unit sekolah di bawah naungan yayasan dari satu dasbor terpusat.'
  },
  {
    icon: School,
    title: 'Kepala Sekolah',
    desc: 'Monitor kinerja guru, kehadiran siswa, dan kondisi akademik secara langsung.'
  },
  {
    icon: Users,
    title: 'Guru & Staf TU',
    desc: 'Input nilai, absensi, dan administrasi kelas menjadi lebih cepat dan tanpa kertas.'
  },
]
</script>

<template>
  <section id="solusi" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid lg:grid-cols-2 gap-14 items-center">

        <!-- Text Side -->
        <div>
          <span class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold mb-4">Solusi Kami</span>
          <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-5 leading-tight">
            Dirancang untuk Semua<br>
            <span class="text-blue-600">Pemangku Kepentingan Sekolah</span>
          </h2>
          <p class="text-gray-500 text-sm mb-8 leading-relaxed">
            Baik Anda mengelola 1 sekolah maupun 20 unit di bawah satu yayasan, CerdasBangsa memberikan visibilitas dan kontrol penuh atas seluruh operasional pendidikan Anda.
          </p>

          <div class="space-y-4">
            <div v-for="a in audiences" :key="a.title"
              class="flex items-start gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition-colors">
              <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                <component :is="a.icon" class="w-5 h-5 text-blue-600" />
              </div>
              <div>
                <div class="font-bold text-gray-900 text-sm">{{ a.title }}</div>
                <div class="text-gray-500 text-sm mt-0.5">{{ a.desc }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Image Side -->
        <div class="relative">
          <div class="rounded-3xl overflow-hidden shadow-2xl shadow-blue-100">
            <img :src="adminImg" alt="Kepala Sekolah menggunakan platform" class="w-full h-96 object-cover object-top" />
          </div>
          <div class="absolute -bottom-4 -right-4 bg-blue-600 rounded-2xl px-5 py-4 shadow-xl text-white text-center">
            <div class="font-extrabold text-2xl">Multi</div>
            <div class="text-blue-200 text-xs font-medium">Sekolah & Yayasan</div>
          </div>
        </div>

      </div>
    </div>
  </section>
</template>
