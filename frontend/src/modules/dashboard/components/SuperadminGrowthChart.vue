<script setup lang="ts">
import type { ChartConfig } from '@/components/ui/chart'
import { ArrowUpRight, TrendingUp } from 'lucide-vue-next'
import { ref, onMounted, computed } from 'vue'
import { Badge } from '@/components/ui/badge'
import {
  ChartContainer,
  ChartCrosshair,
  ChartTooltip,
  ChartTooltipContent,
  componentToString
} from '@/components/ui/chart'
import { VisAxis, VisGroupedBar, VisXYContainer } from '@unovis/vue'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
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
} satisfies ChartConfig
</script>

<template>
  <WidgetCard
    title="Tren Pertumbuhan Sekolah Baru"
    description="Jumlah sekolah terdaftar per bulan — 2025"
    cardClass="lg:col-span-3"
    footerClass="flex-col items-start gap-2 text-sm"
    :delay="delay"
    illustration="globe"
  >
    <template #header-action>
      <Badge variant="secondary" class="gap-1 text-xs bg-white/10 dark:bg-white/10 backdrop-blur-md border border-white/20 shadow-sm text-foreground">
        <ArrowUpRight class="size-3 text-emerald-500 drop-shadow-sm" />
        +18.4%
      </Badge>
    </template>

    <div class="absolute -inset-x-20 -bottom-20 h-64 bg-primary/5 blur-[80px] rounded-full pointer-events-none transition-opacity group-hover:opacity-100 opacity-0"></div>

    <ChartContainer :config="chartConfig" class="h-[320px] w-full bar-chart-container opacity-90 drop-shadow-sm">
      <VisXYContainer
        :data="activeData"
        :x-domain="[-0.5, activeData.length - 0.5]"
        :margin="{ left: -24 }"
        :duration="1000"
      >
        <VisGroupedBar
          :x="(_d: any, i: number) => i"
          :y="[(d: any) => d.sekolah]"
          :color="['var(--color-sekolah)']"
          :rounded-corners="4"
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
          :color="['var(--color-sekolah)']"
          :hide-when-far-from-pointer="false"
          :skip-range-check="true"
        />
      </VisXYContainer>
    </ChartContainer>

    <template #footer>
      <div class="flex gap-2 font-medium leading-none drop-shadow-sm">
        Naik 18.4% dibanding bulan lalu <TrendingUp class="h-4 w-4 text-emerald-500" />
      </div>
      <div class="leading-none text-muted-foreground text-xs mt-1">
        Menampilkan total sekolah baru selama 12 bulan terakhir
      </div>
    </template>
  </WidgetCard>
</template>
