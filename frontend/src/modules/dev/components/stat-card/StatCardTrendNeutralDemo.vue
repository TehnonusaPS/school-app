<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import { statCardDemosData } from './data/statCardDemos'
import { Users } from 'lucide-vue-next'
</script>

<template>
  <StatCard
    label="Tingkat Kelulusan"
    :value="statCardDemosData.kelulusan.total"
    :sub="statCardDemosData.kelulusan.sub"
    :trend="statCardDemosData.kelulusan.trend"
    :trendDirection="statCardDemosData.kelulusan.trendDirection"
    :icon="Users"
    illustration="abc_board"
    color="primary"
    variant="neutral"
  />
</template>
