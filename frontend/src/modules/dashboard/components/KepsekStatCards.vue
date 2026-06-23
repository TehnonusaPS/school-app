<script setup lang="ts">
import { Users, Wallet } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { formatNumber, formatDelta } from '@/utils/formatNumber'
import { kepsekStatsData } from '../data/kepsekStats'
</script>

<template>
  <StatCardGrid cols="4">
    <StatCard label="Total Siswa" :value="formatNumber(kepsekStatsData.siswa.total)"
      sub="siswa aktif" :trend="`${formatDelta(kepsekStatsData.siswa.growth)} bulan ini`"
      :trendDirection="kepsekStatsData.siswa.trendDirection" :icon="Users" illustration="graduation_cap" variant="blue" />

    <StatCard label="Total Guru & Staff" :value="formatNumber(kepsekStatsData.guru.total)"
      sub="tenaga pendidik" :trend="`${formatDelta(kepsekStatsData.guru.growth)} bulan ini`"
      :trendDirection="kepsekStatsData.guru.trendDirection" :icon="Users" illustration="textbook" variant="violet" />

    <StatCard label="Pendapatan" :value="`Rp ${formatNumber(kepsekStatsData.pendapatan.total)} Jt`"
      sub="semester ini" :trend="`${formatDelta(kepsekStatsData.pendapatan.growthPercent)}%`"
      :trendDirection="kepsekStatsData.pendapatan.trendDirection" :icon="Wallet" illustration="globe" variant="emerald" />

    <StatCard label="Pengeluaran" :value="`Rp ${formatNumber(kepsekStatsData.pengeluaran.total)} Jt`"
      sub="semester ini" :trend="`${formatDelta(kepsekStatsData.pengeluaran.growthPercent)}%`"
      :trendDirection="kepsekStatsData.pengeluaran.trendDirection" :icon="Wallet" illustration="ruler" variant="amber" />
  </StatCardGrid>
</template>
