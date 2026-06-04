<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import { Users } from 'lucide-vue-next'
</script>

<template>
  <StatCard
    label="Tingkat Kelulusan"
    value="100%"
    sub="stabil dibanding tahun lalu"
    trend="0% perubahan"
    :icon="Users"
    color="primary"
    variant="neutral"
  />
</template>
