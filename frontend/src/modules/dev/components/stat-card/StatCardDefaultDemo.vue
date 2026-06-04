<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import { Users } from 'lucide-vue-next'
</script>

<template>
  <StatCard
    label="Total Siswa"
    value="402"
    sub="siswa aktif"
    :icon="Users"
    variant="default"
    color="blue"
  />
</template>
