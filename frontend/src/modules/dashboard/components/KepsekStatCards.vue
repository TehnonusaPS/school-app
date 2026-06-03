<script setup lang="ts">
import { Users, Wallet } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import { formatNumber, formatDelta } from '@/utils/formatNumber'
import { kepsekStatsData } from '../data/kepsekStats'
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <StatCard :delay="100" label="Total Siswa" :value="formatNumber(kepsekStatsData.siswa.total)"
      sub="siswa aktif" :trend="`${formatDelta(kepsekStatsData.siswa.growth)} bulan ini`"
      :trendDirection="kepsekStatsData.siswa.trendDirection" :icon="Users" variant="blue" />

    <StatCard :delay="250" label="Total Guru & Staff" :value="formatNumber(kepsekStatsData.guru.total)"
      sub="tenaga pendidik" :trend="`${formatDelta(kepsekStatsData.guru.growth)} bulan ini`"
      :trendDirection="kepsekStatsData.guru.trendDirection" :icon="Users" variant="violet" />

    <StatCard :delay="400" label="Pendapatan" :value="`Rp ${formatNumber(kepsekStatsData.pendapatan.total)} Jt`"
      sub="semester ini" :trend="`${formatDelta(kepsekStatsData.pendapatan.growthPercent)}%`"
      :trendDirection="kepsekStatsData.pendapatan.trendDirection" :icon="Wallet" variant="emerald" />

    <StatCard :delay="550" label="Pengeluaran" :value="`Rp ${formatNumber(kepsekStatsData.pengeluaran.total)} Jt`"
      sub="semester ini" :trend="`${formatDelta(kepsekStatsData.pengeluaran.growthPercent)}%`"
      :trendDirection="kepsekStatsData.pengeluaran.trendDirection" :icon="Wallet" variant="amber" />
  </div>
</template>
