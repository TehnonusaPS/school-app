<script setup lang="ts">
import { Users, LayoutGrid, ClipboardCheck, Wallet } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import { formatNumber, formatDelta } from '@/utils/formatNumber'
import { adminSekolahStatsData } from '../data/adminSekolahStats'
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <StatCard label="Total Siswa" :value="formatNumber(adminSekolahStatsData.siswa.total)"
      sub="siswa aktif" trend="94% hadir hari ini"
      :trendDirection="adminSekolahStatsData.siswa.trendDirection" :icon="Users" variant="blue" />

    <StatCard label="Total Kelas" :value="formatNumber(adminSekolahStatsData.kelas.total)"
      sub="rombel aktif" trend="Semester Ganjil"
      :trendDirection="adminSekolahStatsData.kelas.trendDirection" :icon="LayoutGrid" variant="violet" />

    <StatCard label="Absensi Hari Ini" :value="formatNumber(adminSekolahStatsData.absensi.total)"
      sub="94% hadir" :trend="`${formatDelta(adminSekolahStatsData.absensi.growthPercent)}% dari kemarin`"
      :trendDirection="adminSekolahStatsData.absensi.trendDirection" :icon="ClipboardCheck" variant="emerald" />

    <StatCard label="SPP Terkumpul" :value="`${adminSekolahStatsData.spp.total}%`"
      sub="Target Rp 2,4M" :trend="`${formatDelta(adminSekolahStatsData.spp.growthPercent)}% bulan ini`"
      :trendDirection="adminSekolahStatsData.spp.trendDirection" :icon="Wallet" variant="amber" />
  </div>
</template>
