<script setup>
import { Users } from 'lucide-vue-next'
import BarChartCard from '@/components/chart-card/BarChartCard.vue'
import { attendanceData } from './data/chartCardDemos'

const attendanceConfig = {
  kelas10: { label: 'Kelas 10', color: 'var(--primary)' },
  kelas11: { label: 'Kelas 11', color: '#8b5cf6' }, // Violet
  kelas12: { label: 'Kelas 12', color: '#10b981' }  // Emerald
}
</script>

<template>
  <BarChartCard
    title="Kehadiran Siswa Mingguan"
    description="Persentase kehadiran berdasarkan tingkat kelas minggu ini"
    :data="attendanceData"
    index="day"
    :categories="['kelas10', 'kelas11', 'kelas12']"
    :config="attendanceConfig"
    :icon="Users"
    color="blue"
    illustration="basketball"
    cardClass="max-w-md mx-auto"
    trendValue="+3.2%"
    trendDirection="up"
    footerTitle="Kehadiran rata-rata kelas 12 paling tinggi"
    footerTrend="up"
    footerSubtext="Menampilkan data absensi harian mingguan"
  />
</template>
