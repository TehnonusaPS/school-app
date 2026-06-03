<script setup lang="ts">
import type { ChartConfig } from '@/components/ui/chart'
import { TrendingUp } from 'lucide-vue-next'
import { ref, onMounted, computed } from 'vue'
import {
  ChartContainer,
  ChartCrosshair,
  ChartLegendContent,
  ChartTooltip,
  ChartTooltipContent,
  componentToString
} from '@/components/ui/chart'
import { VisAxis, VisLine, VisXYContainer } from '@unovis/vue'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
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
} satisfies ChartConfig
</script>

<template>
  <WidgetCard
    title="Perbandingan Pendapatan vs Pengeluaran"
    description="6 bulan terakhir — dalam juta rupiah"
    footerClass="flex-col items-start gap-2 text-sm"
    :delay="delay"
  >
    <ChartContainer :config="chartConfig" class="h-[260px] w-full">
      <VisXYContainer :data="activeData" :duration="1000">
        <VisLine
          :x="(_d: any, i: number) => i"
          :y="(d: any) => d.pendapatan"
          color="var(--color-pendapatan)"
        />
        <VisLine
          :x="(_d: any, i: number) => i"
          :y="(d: any) => d.pengeluaran"
          color="var(--color-pengeluaran)"
        />
        <VisAxis
          type="x"
          :tick-format="(i: number) => chartData[i]?.bulan"
          :tick-values="chartData.map((_d, i) => i)"
          :grid-line="false"
          :tick-line="false"
          :domain-line="false"
        />
        <VisAxis
          type="y"
          :num-ticks="4"
          :tick-line="false"
          :domain-line="false"
          :grid-line="true"
        />
        <ChartTooltip />
        <ChartCrosshair
          :template="componentToString(chartConfig, ChartTooltipContent, {
            labelFormatter: (x: number) => chartData[Math.round(x)]?.bulan || ''
          })"
          :color="['var(--color-pendapatan)', 'var(--color-pengeluaran)']"
          :hide-when-far-from-pointer="false"
          :skip-range-check="true"
        />
      </VisXYContainer>
      <ChartLegendContent />
    </ChartContainer>

    <template #footer>
      <div class="flex gap-2 font-medium leading-none">
        Surplus positif 6 bulan berturut-turut <TrendingUp class="h-4 w-4 text-emerald-500" />
      </div>
      <div class="leading-none text-muted-foreground text-xs mt-1">
        Nilai dalam satuan juta rupiah (Rp)
      </div>
    </template>
  </WidgetCard>
</template>
