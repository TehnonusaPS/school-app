<script setup>
import { TrendingUp } from 'lucide-vue-next'
import DonutChartCard from '@/components/chart-card/DonutChartCard.vue'
import { studentStatusData } from './data/chartCardDemos'

const studentStatusConfig = {
  aktif: { label: 'Siswa Aktif', color: 'var(--primary)' },
  lulus: { label: 'Lulus', color: '#10b981' },     // Emerald
  keluar: { label: 'Pindah/Keluar', color: '#ef4444' } // Red
}
</script>

<template>
  <DonutChartCard
    title="Distribusi Status Siswa"
    description="Rincian status pendaftaran siswa di sekolah saat ini"
    :data="studentStatusData"
    category="count"
    index="status"
    :config="studentStatusConfig"
    :icon="TrendingUp"
    color="violet"
    illustration="graduation_cap"
    centerLabelText="Total Siswa"
    centerLabelValue="1.000"
    cardClass="max-w-md mx-auto"
    trendValue="+8.5%"
    trendDirection="up"
    footerTitle="Rasio siswa aktif sangat mendominasi"
    footerTrend="up"
    footerSubtext="Data dihimpun dari tahun ajaran 2025/2026"
  />
</template>
