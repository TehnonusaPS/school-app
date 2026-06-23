<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import BarChartCard from '@/components/chart-card/BarChartCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { growthChartData as chartData } from '../data/superadminGrowthChartData'

const props = defineProps({
  delay: { type: Number, default: 0 }
})

const auth = useAuthStore()
const computedDelay = computed(() => (auth.isJustLoggedIn ? 1400 : 0) + props.delay)

const activeData = ref(chartData.map(d => ({ ...d, sekolah: 0 })))

onMounted(() => {
  setTimeout(() => {
    activeData.value = chartData
  }, computedDelay.value + 350)
})

const chartConfig = {
  sekolah: {
    label: 'Sekolah Baru',
    color: 'var(--primary)'
  }
}
</script>

<template>
  <BarChartCard
    title="Tren Pertumbuhan Sekolah Baru"
    description="Jumlah sekolah terdaftar per bulan — 2025"
    cardClass="lg:col-span-3"
    footerClass="flex-col items-start gap-2 text-sm"
    :delay="delay"
    illustration="planet"
    :data="activeData"
    index="bulan"
    :categories="['sekolah']"
    :config="chartConfig"
    :height="320"
    trendValue="+18.4%"
    trendDirection="up"
    footerTitle="Naik 18.4% dibanding bulan lalu"
    footerTrend="up"
    footerSubtext="Menampilkan total sekolah baru selama 12 bulan terakhir"
  >
    <template #extra>
      <div class="absolute -inset-x-20 -bottom-20 h-64 bg-primary/5 blur-[80px] rounded-full pointer-events-none transition-opacity group-hover:opacity-100 opacity-0"></div>
    </template>
  </BarChartCard>
</template>
