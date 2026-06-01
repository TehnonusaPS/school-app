<script setup lang="ts">
import type { ChartConfig } from '@/components/ui/chart'
import { Donut } from '@unovis/ts'
import { VisDonut, VisSingleContainer } from '@unovis/vue'
import { Wallet } from 'lucide-vue-next'
import { computed } from 'vue'
import {
  ChartContainer,
  ChartTooltip,
  ChartTooltipContent,
  componentToString
} from '@/components/ui/chart'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import { sppData as chartData, sppSummary } from '../data/adminSekolahSPPData'

type Data = typeof chartData[number]

const chartConfig = {
  nilai: { label: 'Jumlah', color: undefined },
  sudah: { label: 'Sudah Bayar', color: 'var(--primary)' },
  belum: { label: 'Belum Bayar', color: 'var(--muted-foreground)' }
} satisfies ChartConfig

const total   = computed(() => chartData.reduce((a, c) => a + c.nilai, 0))
const persen  = computed(() => Math.round((chartData[0].nilai / total.value) * 100))
</script>

<template>
  <WidgetCard
    title="Ringkasan SPP Bulan Ini"
    description="Status pembayaran SPP seluruh siswa"
    :icon="Wallet"
    cardClass="lg:col-span-2"
    contentClass="pb-0"
    footerClass="flex-col gap-0 pt-2 border-t"
  >
    <ChartContainer
      :config="chartConfig"
      class="mx-auto aspect-square max-h-[220px]"
      :style="{
        '--vis-donut-central-label-font-size': 'var(--text-3xl)',
        '--vis-donut-central-label-font-weight': 'var(--font-weight-bold)',
        '--vis-donut-central-label-text-color': 'var(--foreground)',
        '--vis-donut-central-sub-label-text-color': 'var(--muted-foreground)'
      }"
    >
      <VisSingleContainer :data="chartData" :margin="{ top: 20, bottom: 20 }">
        <VisDonut
          :value="(d: Data) => d.nilai"
          :color="(d: Data) => chartConfig[d.status as keyof typeof chartConfig].color"
          :arc-width="32"
          :central-label-offset-y="10"
          :central-label="`${persen}%`"
          central-sub-label="Terkumpul"
        />
        <ChartTooltip
          :triggers="{
            [Donut.selectors.segment]: componentToString(chartConfig, ChartTooltipContent, { hideLabel: true })!
          }"
        />
      </VisSingleContainer>
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
