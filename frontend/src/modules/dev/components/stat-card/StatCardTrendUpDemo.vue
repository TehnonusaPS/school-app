<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import { statCardDemosData } from './data/statCardDemos'
import { UserCheck } from 'lucide-vue-next'
</script>

<template>
  <StatCard
    label="Absensi Hari Ini"
    :value="statCardDemosData.absensi.total"
    :sub="statCardDemosData.absensi.sub"
    :trend="statCardDemosData.absensi.trend"
    :trendDirection="statCardDemosData.absensi.trendDirection"
    :icon="UserCheck"
    color="emerald"
    variant="up"
  />
</template>
