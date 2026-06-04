<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import { Users } from 'lucide-vue-next'
</script>

<template>
  <StatCard
    label="Pendaftaran Baru"
    value="12"
    sub="siswa mendaftar"
    trend="-4% dari minggu lalu"
    :icon="Users"
    color="amber"
    variant="down"
  />
</template>
