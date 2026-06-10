<script setup lang="ts">
import { Users, LayoutGrid, ClipboardCheck, Wallet } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { formatNumber, formatDelta } from '@/utils/formatNumber'
import { adminSekolahStatsData } from '../data/adminSekolahStats'
</script>

<template>
  <StatCardGrid cols="4">
    <StatCard label="Total Siswa" :value="formatNumber(adminSekolahStatsData.siswa.total)"
      sub="siswa aktif" trend="94% hadir hari ini"
      :trendDirection="adminSekolahStatsData.siswa.trendDirection" :icon="Users" illustration="graduation_cap" variant="blue" />

    <StatCard label="Total Kelas" :value="formatNumber(adminSekolahStatsData.kelas.total)"
      sub="rombel aktif" trend="Semester Ganjil"
      :trendDirection="adminSekolahStatsData.kelas.trendDirection" :icon="LayoutGrid" illustration="abc_board" variant="violet" />

    <StatCard label="Absensi Hari Ini" :value="formatNumber(adminSekolahStatsData.absensi.total)"
      sub="94% hadir" :trend="`${formatDelta(adminSekolahStatsData.absensi.growthPercent)}% dari kemarin`"
      :trendDirection="adminSekolahStatsData.absensi.trendDirection" :icon="ClipboardCheck" illustration="pencil" variant="emerald" />

    <StatCard label="SPP Terkumpul" :value="`${adminSekolahStatsData.spp.total}%`"
      sub="Target Rp 2,4M" :trend="`${formatDelta(adminSekolahStatsData.spp.growthPercent)}% bulan ini`"
      :trendDirection="adminSekolahStatsData.spp.trendDirection" :icon="Wallet" illustration="bag" variant="amber" />
  </StatCardGrid>
</template>
