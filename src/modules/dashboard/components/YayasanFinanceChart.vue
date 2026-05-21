<script setup lang="ts">
import type { ChartConfig } from '@/components/ui/chart'
import { TrendingUp } from 'lucide-vue-next'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle
} from '@/components/ui/card'
import {
  ChartContainer,
  ChartCrosshair,
  ChartLegendContent,
  ChartTooltip,
  ChartTooltipContent,
  componentToString
} from '@/components/ui/chart'
import { VisAxis, VisLine, VisXYContainer } from '@unovis/vue'

const chartData = [
  { bulan: 'Jan', pendapatan: 180, pengeluaran: 120 },
  { bulan: 'Feb', pendapatan: 210, pengeluaran: 145 },
  { bulan: 'Mar', pendapatan: 195, pengeluaran: 160 },
  { bulan: 'Apr', pendapatan: 240, pengeluaran: 170 },
  { bulan: 'Mei', pendapatan: 275, pengeluaran: 190 },
  { bulan: 'Jun', pendapatan: 310, pengeluaran: 210 }
]

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
  <Card>
    <CardHeader>
      <div class="flex items-start justify-between">
        <div>
          <CardTitle class="text-base font-semibold">
            Perbandingan Pendapatan vs Pengeluaran
          </CardTitle>
          <CardDescription class="mt-0.5 text-xs">
            6 bulan terakhir — dalam juta rupiah
          </CardDescription>
        </div>
      </div>
    </CardHeader>

    <CardContent>
      <ChartContainer :config="chartConfig" class="h-[260px] w-full">
        <VisXYContainer :data="chartData">
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
    </CardContent>

    <CardFooter class="flex-col items-start gap-2 text-sm border-t pt-4">
      <div class="flex gap-2 font-medium leading-none">
        Surplus positif 6 bulan berturut-turut <TrendingUp class="h-4 w-4 text-emerald-500" />
      </div>
      <div class="leading-none text-muted-foreground text-xs">
        Nilai dalam satuan juta rupiah (Rp)
      </div>
    </CardFooter>
  </Card>
</template>
