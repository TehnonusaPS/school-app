<script setup lang="ts">
import { School, Users, Wallet } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import { formatNumber, formatDelta } from '@/utils/formatNumber'
import { yayasanStatsData } from '../data/yayasanStats'
</script>

<template>
  <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-3 sm:gap-4">
    <!-- Stat Cards biasa -->
    <StatCard :delay="100" label="Total Sekolah" :value="formatNumber(yayasanStatsData.sekolah.total)"
      sub="di bawah yayasan" :trend="`${formatDelta(yayasanStatsData.sekolah.growth)} semester ini`"
      :trendDirection="yayasanStatsData.sekolah.trendDirection" :icon="School" variant="violet" />

    <StatCard :delay="250" label="Total Guru & Staff" :value="formatNumber(yayasanStatsData.guru.total)"
      sub="tenaga pendidik aktif" :trend="`${formatDelta(yayasanStatsData.guru.growth)} bulan ini`"
      :trendDirection="yayasanStatsData.guru.trendDirection" :icon="Users" variant="blue" />

    <StatCard :delay="400" label="Total Anggaran" :value="`Rp ${yayasanStatsData.anggaran.total}M`" sub="Realisasi"
      :progress="yayasanStatsData.anggaran.realisasi" :icon="Wallet" variant="emerald" class="col-span-2 sm:col-span-1" />
  </div>
</template>
