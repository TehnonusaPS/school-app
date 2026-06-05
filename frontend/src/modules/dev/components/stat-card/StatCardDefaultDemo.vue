<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import { statCardDemosData } from './data/statCardDemos'
import { Users } from 'lucide-vue-next'
</script>

<template>
  <StatCard
    label="Total Siswa"
    :value="statCardDemosData.siswa.total"
    :sub="statCardDemosData.siswa.sub"
    :icon="Users"
    variant="default"
    color="blue"
  />
</template>
