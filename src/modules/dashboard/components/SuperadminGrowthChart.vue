<script setup lang="ts">
import type { ChartConfig } from '@/components/ui/chart'
import { ArrowUpRight, TrendingUp } from 'lucide-vue-next'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle
} from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import {
  ChartContainer,
  ChartCrosshair,
  ChartTooltip,
  ChartTooltipContent,
  componentToString
} from '@/components/ui/chart'
import { VisAxis, VisGroupedBar, VisXYContainer } from '@unovis/vue'

const chartData = [
  { bulan: 'Jan', sekolah: 12 },
  { bulan: 'Feb', sekolah: 18 },
  { bulan: 'Mar', sekolah: 14 },
  { bulan: 'Apr', sekolah: 22 },
  { bulan: 'Mei', sekolah: 28 },
  { bulan: 'Jun', sekolah: 24 },
  { bulan: 'Jul', sekolah: 32 },
  { bulan: 'Agt', sekolah: 38 },
  { bulan: 'Sep', sekolah: 35 },
  { bulan: 'Okt', sekolah: 45 },
  { bulan: 'Nov', sekolah: 42 },
  { bulan: 'Des', sekolah: 52 }
]

const chartConfig = {
  sekolah: {
    label: 'Sekolah Baru',
    color: 'var(--primary)'
  }
} satisfies ChartConfig
</script>

<template>
  <Card class="lg:col-span-3">
    <CardHeader>
      <div class="flex items-start justify-between">
        <div>
          <CardTitle class="text-base font-semibold">Tren Pertumbuhan Sekolah Baru</CardTitle>
          <CardDescription class="mt-0.5 text-xs">
            Jumlah sekolah terdaftar per bulan — 2025
          </CardDescription>
        </div>
        <Badge variant="secondary" class="gap-1 text-xs">
          <ArrowUpRight class="size-3 text-emerald-500" />
          +18.4%
        </Badge>
      </div>
    </CardHeader>

    <CardContent>
      <ChartContainer :config="chartConfig" class="h-[320px] w-full bar-chart-container">
        <VisXYContainer
          :data="chartData"
          :x-domain="[-0.5, chartData.length - 0.5]"
          :margin="{ left: -24 }"
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
    </CardContent>

    <CardFooter class="flex-col items-start gap-2 text-sm border-t pt-4">
      <div class="flex gap-2 font-medium leading-none">
        Naik 18.4% dibanding bulan lalu <TrendingUp class="h-4 w-4 text-emerald-500" />
      </div>
      <div class="leading-none text-muted-foreground text-xs">
        Menampilkan total sekolah baru selama 12 bulan terakhir
      </div>
    </CardFooter>
  </Card>
</template>
