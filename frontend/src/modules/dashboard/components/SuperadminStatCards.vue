<script setup lang="ts">
import { Building2, GraduationCap, School, Users } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { formatNumber, formatDelta } from '@/utils/formatNumber'
import { superadminStatsData } from '../data/superadminStats'
</script>

<template>
  <StatCardGrid cols="4">
    <StatCard
      label="Total Yayasan"
      :value="formatNumber(superadminStatsData.yayasan.total)"
      :sub="`${formatDelta(superadminStatsData.yayasan.newThisMonth)} bulan ini`"
      :trend="`${formatDelta(superadminStatsData.yayasan.growthPercent)}%`"
      :trendDirection="superadminStatsData.yayasan.trendDirection"
      :icon="Building2"
      illustration="globe"
      variant="blue"
    />

    <StatCard
      label="Total Sekolah"
      :value="formatNumber(superadminStatsData.sekolah.total)"
      :sub="`${formatDelta(superadminStatsData.sekolah.newThisMonth)} bulan ini`"
      :trend="`${formatDelta(superadminStatsData.sekolah.growthPercent)}%`"
      :trendDirection="superadminStatsData.sekolah.trendDirection"
      :icon="School"
      illustration="abc_board"
      variant="violet"
    />

    <StatCard
      label="Total Guru"
      :value="formatNumber(superadminStatsData.guru.total)"
      :sub="`${formatDelta(superadminStatsData.guru.newThisMonth)} bulan ini`"
      :trend="`${formatDelta(superadminStatsData.guru.growthPercent)}%`"
      :trendDirection="superadminStatsData.guru.trendDirection"
      :icon="GraduationCap"
      illustration="open_book"
      variant="emerald"
    />

    <StatCard
      label="Total Siswa"
      :value="formatNumber(superadminStatsData.siswa.total)"
      :sub="`${formatDelta(superadminStatsData.siswa.newThisMonth)} bulan ini`"
      :trend="`${formatDelta(superadminStatsData.siswa.growthPercent)}%`"
      :trendDirection="superadminStatsData.siswa.trendDirection"
      :icon="Users"
      illustration="graduation_cap"
      variant="amber"
    />
  </StatCardGrid>
</template>
