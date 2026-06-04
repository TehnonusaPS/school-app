<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import { UserCheck } from 'lucide-vue-next'
</script>

<template>
  <StatCard
    label="Absensi Hari Ini"
    value="378"
    sub="94% hadir"
    trend="+2% dari kemarin"
    :icon="UserCheck"
    color="emerald"
    variant="up"
  />
</template>
