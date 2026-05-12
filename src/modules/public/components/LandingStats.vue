<script setup>
import { Users, Award, Clock, School, Building2 } from 'lucide-vue-next'

const stats = [
  { number: '50+', label: 'Yayasan Terdaftar', icon: Building2 },
  { number: '200+', label: 'Unit Sekolah Aktif', icon: School },
  { number: '60.000+', label: 'Siswa Terkelola', icon: Users },
  { number: '99.9%', label: 'Uptime Sistem', icon: Award },
]
</script>

<template>
  <section class="bg-blue-700 py-12">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="s in stats" :key="s.label" class="flex flex-col items-center text-center text-white">
        <component :is="s.icon" class="w-8 h-8 text-yellow-400 mb-2" />
        <div class="text-3xl font-extrabold">{{ s.number }}</div>
        <div class="text-sm text-blue-200 mt-1">{{ s.label }}</div>
      </div>
    </div>
  </section>
</template>
