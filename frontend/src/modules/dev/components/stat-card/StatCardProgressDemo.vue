<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import { Wallet } from 'lucide-vue-next'
</script>

<template>
  <StatCard
    label="Total Anggaran"
    value="78.5"
    sub="Realisasi Anggaran"
    :progress="78.5"
    :icon="Wallet"
    variant="progress"
    color="violet"
  />
</template>
