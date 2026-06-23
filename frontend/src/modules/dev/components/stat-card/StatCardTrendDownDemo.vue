<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import { statCardDemosData } from './data/statCardDemos'
import { Users } from 'lucide-vue-next'
</script>

<template>
  <StatCard
    label="Pendaftaran Baru"
    :value="statCardDemosData.pendaftaran.total"
    :sub="statCardDemosData.pendaftaran.sub"
    :trend="statCardDemosData.pendaftaran.trend"
    :trendDirection="statCardDemosData.pendaftaran.trendDirection"
    :icon="Users"
    illustration="star"
    color="amber"
    variant="down"
  />
</template>
