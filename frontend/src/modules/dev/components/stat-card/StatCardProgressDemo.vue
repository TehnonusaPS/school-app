<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import { statCardDemosData } from './data/statCardDemos'
import { Wallet } from 'lucide-vue-next'
</script>

<template>
  <StatCard
    label="Total Anggaran"
    :value="statCardDemosData.anggaran.total"
    :sub="statCardDemosData.anggaran.sub"
    :progress="statCardDemosData.anggaran.progress"
    :icon="Wallet"
    illustration="open_book"
    variant="progress"
    color="violet"
  />
</template>
