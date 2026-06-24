<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import LineChartCard from '@/components/chart-card/LineChartCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { financeChartData as chartData } from '../data/yayasanFinanceChartData'

const props = defineProps({
  delay: { type: Number, default: 0 }
})

const auth = useAuthStore()
const computedDelay = computed(() => (auth.isJustLoggedIn ? 1400 : 0) + props.delay)

const activeData = ref(chartData.map(d => ({ ...d, pendapatan: 0, pengeluaran: 0 })))

onMounted(() => {
  setTimeout(() => {
    activeData.value = chartData
  }, computedDelay.value + 350)
})

const chartConfig = {
  pendapatan: {
    label: 'Pendapatan',
    color: 'var(--primary)'
  },
  pengeluaran: {
    label: 'Pengeluaran',
    color: 'var(--muted-foreground)'
  }
}
</script>

<template>
  <LineChartCard
    title="Perbandingan Pendapatan vs Pengeluaran"
    description="6 bulan terakhir — dalam juta rupiah"
    footerClass="flex-col items-start gap-2 text-sm"
    :delay="delay"
    illustration="bag"
    :data="activeData"
    index="bulan"
    :categories="['pendapatan', 'pengeluaran']"
    :config="chartConfig"
    :height="260"
    footerTitle="Surplus positif 6 bulan berturut-turut"
    footerTrend="up"
    footerSubtext="Nilai dalam satuan juta rupiah (Rp)"
  />
</template>
