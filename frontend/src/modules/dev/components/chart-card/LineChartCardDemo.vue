<script setup>
import { DollarSign } from 'lucide-vue-next'
import LineChartCard from '@/components/chart-card/LineChartCard.vue'
import { financeData } from './data/chartCardDemos'

const financeConfig = {
  pendapatan: { label: 'Pendapatan (Juta)', color: '#10b981' }, // Emerald
  pengeluaran: { label: 'Pengeluaran (Juta)', color: '#f59e0b' }  // Amber
}
</script>

<template>
  <LineChartCard
    title="Arus Kas Keuangan"
    description="Perbandingan pendapatan dan pengeluaran operasional (dalam juta Rp)"
    :data="financeData"
    index="month"
    :categories="['pendapatan', 'pengeluaran']"
    :config="financeConfig"
    :icon="DollarSign"
    color="emerald"
    illustration="graded_paper"
    cardClass="max-w-md mx-auto"
    trendValue="-2.1%"
    trendDirection="down"
    footerTitle="Pengeluaran operasional mengalami penurunan"
    footerTrend="down"
    footerSubtext="Nilai dalam satuan juta rupiah (Rp)"
  />
</template>
