<script setup lang="ts">
import { School, Users, Wallet, TrendingUp } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card'
import { Progress } from '@/components/ui/progress'

const stats = [
  {
    label: 'Total Sekolah',
    value: '10',
    sub: 'di bawah yayasan',
    trend: '+1 semester ini',
    icon: School,
    color: 'text-violet-500',
    bg: 'bg-violet-500/10'
  },
  {
    label: 'Total Guru & Staff',
    value: '102',
    sub: 'tenaga pendidik aktif',
    trend: '+8 bulan ini',
    icon: Users,
    color: 'text-blue-500',
    bg: 'bg-blue-500/10'
  }
]

const anggaran = {
  total: 'Rp 1,2M',
  realisasi: 78.5
}
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <!-- Stat Cards biasa -->
    <Card
      v-for="(stat, i) in stats"
      :key="i"
      class="transition-all duration-200 hover:shadow-md hover:-translate-y-0.5"
    >
      <CardHeader class="pb-2">
        <div class="flex items-center justify-between">
          <CardDescription class="text-sm font-medium">{{ stat.label }}</CardDescription>
          <div :class="['rounded-lg p-2', stat.bg]">
            <component :is="stat.icon" :class="['size-4', stat.color]" />
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <div class="text-3xl font-bold tracking-tight">{{ stat.value }}</div>
        <div class="mt-1 flex items-center gap-1.5">
          <span class="flex items-center gap-0.5 text-xs font-semibold text-emerald-500">
            <TrendingUp class="size-3" />
            {{ stat.trend }}
          </span>
          <span class="text-xs text-muted-foreground">{{ stat.sub }}</span>
        </div>
      </CardContent>
    </Card>

    <!-- Card Total Anggaran dengan Progress -->
    <Card class="transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
      <CardHeader class="pb-2">
        <div class="flex items-center justify-between">
          <CardDescription class="text-sm font-medium">Total Anggaran</CardDescription>
          <div class="rounded-lg p-2 bg-emerald-500/10">
            <Wallet class="size-4 text-emerald-500" />
          </div>
        </div>
      </CardHeader>
      <CardContent class="space-y-3">
        <div class="text-3xl font-bold tracking-tight">{{ anggaran.total }}</div>
        <div class="space-y-1.5">
          <div class="flex items-center justify-between text-xs">
            <span class="text-muted-foreground">Realisasi</span>
            <span class="font-semibold text-emerald-500">{{ anggaran.realisasi }}%</span>
          </div>
          <Progress :model-value="anggaran.realisasi" class="h-2" />
        </div>
      </CardContent>
    </Card>
  </div>
</template>
