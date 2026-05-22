<script setup lang="ts">
import type { ChartConfig } from '@/components/ui/chart'
import { Donut } from '@unovis/ts'
import { VisDonut, VisSingleContainer } from '@unovis/vue'
import { Wallet } from 'lucide-vue-next'
import { computed } from 'vue'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import {
  ChartContainer,
  ChartTooltip,
  ChartTooltipContent,
  componentToString
} from '@/components/ui/chart'

const chartData = [
  { status: 'sudah', nilai: 120, fill: 'var(--color-sudah)' },
  { status: 'belum', nilai: 300, fill: 'var(--color-belum)' }
]
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
  <Card class="lg:col-span-2 flex flex-col">
    <CardHeader class="items-center pb-0">
      <div class="flex items-center gap-2">
        <Wallet class="size-4 text-primary" />
        <CardTitle class="text-base font-semibold">Ringkasan SPP Bulan Ini</CardTitle>
      </div>
      <CardDescription class="text-xs">Status pembayaran SPP seluruh siswa</CardDescription>
    </CardHeader>

    <CardContent class="flex-1 pb-0">
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
    </CardContent>

    <CardFooter class="flex-col gap-0 pt-2 border-t">
      <div class="w-full space-y-2 py-3">
        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2">
            <span class="inline-block size-3 rounded-sm bg-primary"></span>
            <span class="text-muted-foreground">Sudah Bayar</span>
          </div>
          <span class="font-semibold text-emerald-500">Rp 120 Jt</span>
        </div>
        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2">
            <span class="inline-block size-3 rounded-sm bg-muted-foreground"></span>
            <span class="text-muted-foreground">Belum Bayar</span>
          </div>
          <span class="font-semibold text-rose-500">Rp 300 Jt</span>
        </div>
        <div class="flex items-center justify-between text-sm border-t pt-2">
          <span class="font-medium">Total Target</span>
          <span class="font-bold">Rp 420 Jt</span>
        </div>
      </div>
    </CardFooter>
  </Card>
</template>
