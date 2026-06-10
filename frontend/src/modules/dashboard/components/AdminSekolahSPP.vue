<script setup lang="ts">
import type { ChartConfig } from '@/components/ui/chart'
import { Donut } from '@unovis/ts'
import { VisDonut, VisSingleContainer } from '@unovis/vue'
import { Wallet } from 'lucide-vue-next'
import { ref, onMounted, computed } from 'vue'
import {
  ChartContainer,
  ChartTooltip,
  ChartTooltipContent,
  componentToString
} from '@/components/ui/chart'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import RollingNumber from '@/components/ui/rolling-number/RollingNumber.vue'
import { useAuthStore } from '@/stores/authStore'
import { sppData as chartData, sppSummary } from '../data/adminSekolahSPPData'

type Data = typeof chartData[number]

const chartConfig = {
  nilai: { label: 'Jumlah', color: undefined },
  sudah: { label: 'Sudah Bayar', color: 'var(--primary)' },
  belum: { label: 'Belum Bayar', color: 'var(--muted-foreground)' }
} satisfies ChartConfig

const props = defineProps({
  delay: { type: Number, default: 0 }
})

const auth = useAuthStore()
const computedDelay = computed(() => (auth.isJustLoggedIn ? 1400 : 0) + props.delay)

const total = computed(() => chartData.reduce((a, c) => a + c.nilai, 0))

const activeData = ref<any[]>([
  { status: 'sudah', nilai: 0 },
  { status: 'belum', nilai: total.value }
])

const activePersen = computed(() => {
  const sudah = activeData.value.find(d => d.status === 'sudah')?.nilai || 0
  const tot = activeData.value.reduce((a, c) => a + c.nilai, 0)
  return tot > 0 ? Math.round((sudah / tot) * 100) : 0
})

onMounted(() => {
  setTimeout(() => {
    activeData.value = chartData
  }, computedDelay.value + 800)
})
</script>


<template>
  <WidgetCard
    title="Ringkasan SPP Bulan Ini"
    description="Status pembayaran SPP seluruh siswa"
    :icon="Wallet"
    cardClass="lg:col-span-2"
    contentClass="pb-0"
    footerClass="flex-col gap-0 pt-2 border-t"
    :delay="delay"
    illustration="bag"
  >
    <ChartContainer
      :config="chartConfig"
      class="mx-auto aspect-square max-h-[220px] relative"
    >
      <VisSingleContainer
        v-motion
        :initial="{ opacity: 0, scale: 0.3 }"
        :visible-once="{ opacity: 1, scale: 1, transition: { type: 'spring', stiffness: 180, damping: 15, delay: computedDelay + 100 } }"
        :data="activeData"
        :margin="{ top: 20, bottom: 20 }"
        :duration="1200"
      >
        <VisDonut
          :id="(d: Data) => d.status"
          :value="(d: Data) => d.nilai"
          :color="(d: Data) => chartConfig[d.status as keyof typeof chartConfig].color"
          :arc-width="32"
          :duration="1200"
        />
        <ChartTooltip
          :triggers="{
            [Donut.selectors.segment]: componentToString(chartConfig, ChartTooltipContent, { hideLabel: true })!
          }"
        />
      </VisSingleContainer>

      <!-- Central Label Overlay -->
      <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-1">
        <span class="text-3xl font-bold text-foreground">
          <RollingNumber :value="activePersen + '%'" :delay="0" :duration="1200" />
        </span>
        <span class="text-xs text-muted-foreground mt-0.5">Terkumpul</span>
      </div>
    </ChartContainer>

    <template #footer>
      <div class="w-full space-y-2 py-3">
        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2">
            <span class="inline-block size-3 rounded-sm bg-primary"></span>
            <span class="text-muted-foreground">Sudah Bayar</span>
          </div>
          <span class="font-semibold text-emerald-500">{{ sppSummary.sudah }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2">
            <span class="inline-block size-3 rounded-sm bg-muted-foreground"></span>
            <span class="text-muted-foreground">Belum Bayar</span>
          </div>
          <span class="font-semibold text-rose-500">{{ sppSummary.belum }}</span>
        </div>
        <div class="flex items-center justify-between text-sm border-t pt-2">
          <span class="font-medium">Total Target</span>
          <span class="font-bold">{{ sppSummary.total }}</span>
        </div>
      </div>
    </template>
  </WidgetCard>
</template>
