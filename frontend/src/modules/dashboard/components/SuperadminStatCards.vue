<script setup lang="ts">
import { Building2, GraduationCap, School, Users } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import { formatNumber, formatDelta } from '@/utils/formatNumber'
import { superadminStatsData } from '../data/superadminStats'
</script>

<template>
  <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-4">
    <StatCard :delay="100" label="Total Yayasan" :value="formatNumber(superadminStatsData.yayasan.total)"
      :sub="`${formatDelta(superadminStatsData.yayasan.newThisMonth)} bulan ini`"
      :trend="`${formatDelta(superadminStatsData.yayasan.growthPercent)}%`"
      :trendDirection="superadminStatsData.yayasan.trendDirection" :icon="Building2" variant="blue" />

    <StatCard :delay="250" label="Total Sekolah" :value="formatNumber(superadminStatsData.sekolah.total)"
      :sub="`${formatDelta(superadminStatsData.sekolah.newThisMonth)} bulan ini`"
      :trend="`${formatDelta(superadminStatsData.sekolah.growthPercent)}%`"
      :trendDirection="superadminStatsData.sekolah.trendDirection" :icon="School" variant="violet" />

    <StatCard :delay="400" label="Total Guru" :value="formatNumber(superadminStatsData.guru.total)"
      :sub="`${formatDelta(superadminStatsData.guru.newThisMonth)} bulan ini`"
      :trend="`${formatDelta(superadminStatsData.guru.growthPercent)}%`"
      :trendDirection="superadminStatsData.guru.trendDirection" :icon="GraduationCap" variant="emerald" />

    <StatCard :delay="550" label="Total Siswa" :value="formatNumber(superadminStatsData.siswa.total)"
      :sub="`${formatDelta(superadminStatsData.siswa.newThisMonth)} bulan ini`"
      :trend="`${formatDelta(superadminStatsData.siswa.growthPercent)}%`"
      :trendDirection="superadminStatsData.siswa.trendDirection" :icon="Users" variant="amber" />
  </div>
</template>
