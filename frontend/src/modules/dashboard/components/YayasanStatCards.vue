<script setup lang="ts">
import { School, Users, Wallet } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { formatNumber, formatDelta } from '@/utils/formatNumber'
import { yayasanStatsData } from '../data/yayasanStats'
</script>

<template>
  <StatCardGrid cols="3">
    <!-- Stat Cards biasa -->
    <StatCard label="Total Sekolah" :value="formatNumber(yayasanStatsData.sekolah.total)"
      sub="di bawah yayasan" :trend="`${formatDelta(yayasanStatsData.sekolah.growth)} semester ini`"
      :trendDirection="yayasanStatsData.sekolah.trendDirection" :icon="School" illustration="globe" variant="violet" />

    <StatCard label="Total Guru & Staff" :value="formatNumber(yayasanStatsData.guru.total)"
      sub="tenaga pendidik aktif" :trend="`${formatDelta(yayasanStatsData.guru.growth)} bulan ini`"
      :trendDirection="yayasanStatsData.guru.trendDirection" :icon="Users" illustration="closed_book" variant="blue" />

    <StatCard label="Total Anggaran" :value="`Rp ${yayasanStatsData.anggaran.total}M`" sub="Realisasi"
      :progress="yayasanStatsData.anggaran.realisasi" :icon="Wallet" illustration="atom" variant="emerald" />
  </StatCardGrid>
</template>
