<script setup lang="ts">
import { UserCheck, FileText, UserX } from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import { formatNumber } from '@/utils/formatNumber'
import { siswaStatsData } from '../data/siswaStats'
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-3">
    <StatCard label="Hadir" :value="formatNumber(siswaStatsData.hadir.total)"
      sub="hari kehadiran" :trendDirection="siswaStatsData.hadir.trendDirection" 
      :icon="UserCheck" variant="emerald" />

    <StatCard label="Izin" :value="formatNumber(siswaStatsData.izin.total)"
      sub="hari izin" :trendDirection="siswaStatsData.izin.trendDirection" 
      :icon="FileText" variant="amber" />

    <StatCard label="Alpa" :value="siswaStatsData.alpa.total === 0 ? '—' : formatNumber(siswaStatsData.alpa.total)"
      sub="tidak hadir tanpa ket." :trendDirection="siswaStatsData.alpa.trendDirection" 
      :icon="UserX" variant="primary" />
  </div>
</template>
