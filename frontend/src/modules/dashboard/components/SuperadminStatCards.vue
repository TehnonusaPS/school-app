<script setup lang="ts">
import { Building2, GraduationCap, School, Users } from 'lucide-vue-next'
import StatCard from '@/components/stats-card/StatCard.vue'

const stats = [
  {
    label: 'Total Yayasan',
    value: '120',
    sub: '+5 bulan ini',
    trend: '+4.3%',
    trendDirection: 'up',
    icon: Building2,
    color: 'text-blue-500',
    bg: 'bg-blue-500/10'
  },
  {
    label: 'Total Sekolah',
    value: '270',
    sub: '+12 bulan ini',
    trend: '+4.6%',
    trendDirection: 'up',
    icon: School,
    color: 'text-violet-500',
    bg: 'bg-violet-500/10'
  },
  {
    label: 'Total Guru',
    value: '2.930',
    sub: '+48 bulan ini',
    trend: '+1.7%',
    trendDirection: 'up',
    icon: GraduationCap,
    color: 'text-emerald-500',
    bg: 'bg-emerald-500/10'
  },
  {
    label: 'Total Siswa',
    value: '1.239K',
    sub: '+320 bulan ini',
    trend: '+2.1%',
    trendDirection: 'up',
    icon: Users,
    color: 'text-amber-500',
    bg: 'bg-amber-500/10'
  }
]
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <StatCard
      v-for="(stat, i) in stats"
      :key="i"
      :label="stat.label"
      :value="stat.value"
      :sub="stat.sub"
      :trend="stat.trend"
      :trendDirection="stat.trendDirection"
      :icon="stat.icon"
      :color="stat.color"
      :bg="stat.bg"
    />
  </div>
</template>
