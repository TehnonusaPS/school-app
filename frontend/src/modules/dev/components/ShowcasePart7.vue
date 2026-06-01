<script setup>
import { defineProps } from 'vue'
import { Wallet, Users, LayoutGrid, School, UserCheck } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'

const props = defineProps({
  searchQuery: {
    type: String,
    default: ''
  }
})

const match = keywords => {
  if (!props.searchQuery) return true
  return keywords.toLowerCase().includes(props.searchQuery.toLowerCase())
}
</script>

<template>
  <div class="space-y-12">
    <!-- 61. STAT CARD -->
    <section v-show="match('stat card statistik dashboard progress trend')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">61. Stat Card</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen kartu statistik responsif dengan desain Glassmorphism. Tersedia dalam berbagai varian warna dan mendukung indikator trend maupun progress bar.
        </p>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 p-6 border rounded-xl bg-card">
        <!-- Variant Blue -->
        <StatCard label="Total Siswa" value="402"
          sub="siswa aktif" trend="+12 bulan ini"
          trendDirection="up" :icon="Users" variant="blue" />

        <!-- Variant Violet -->
        <StatCard label="Total Kelas" value="18"
          sub="rombel aktif" trend="Semester Ganjil"
          trendDirection="neutral" :icon="LayoutGrid" variant="violet" />

        <!-- Variant Emerald -->
        <StatCard label="Absensi Hari Ini" value="378"
          sub="94% hadir" trend="+2% dari kemarin"
          trendDirection="up" :icon="UserCheck" variant="emerald" />

        <!-- Variant Amber -->
        <StatCard label="SPP Terkumpul" value="82.2%"
          sub="Target Rp 2,4M" trend="+5.1% bulan ini"
          trendDirection="up" :icon="Wallet" variant="amber" />

        <!-- Variant Primary -->
        <StatCard label="Alpa Hari Ini" value="0"
          sub="tidak hadir tanpa ket." trendDirection="neutral"
          variant="primary" />

        <!-- Variant Progress -->
        <StatCard label="Total Anggaran" value="Rp 1,2M"
          sub="Realisasi Anggaran" :progress="78.5"
          :icon="Wallet" variant="emerald" />
      </div>
    </section>
  </div>
</template>
