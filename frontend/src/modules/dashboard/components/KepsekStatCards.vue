<script setup lang="ts">
import { Users, Wallet, TrendingDown, TrendingUp } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card'

const stats = [
  {
    label: 'Total Siswa',
    value: '402',
    sub: 'siswa aktif',
    trend: '+12 bulan ini',
    trendUp: true,
    icon: Users,
    color: 'text-blue-500',
    bg: 'bg-blue-500/10'
  },
  {
    label: 'Total Guru & Staff',
    value: '102',
    sub: 'tenaga pendidik',
    trend: '+3 bulan ini',
    trendUp: true,
    icon: Users,
    color: 'text-violet-500',
    bg: 'bg-violet-500/10'
  },
  {
    label: 'Pendapatan',
    value: 'Rp 480 Jt',
    sub: 'semester ini',
    trend: '+8.2%',
    trendUp: true,
    icon: Wallet,
    color: 'text-emerald-500',
    bg: 'bg-emerald-500/10'
  },
  {
    label: 'Pengeluaran',
    value: 'Rp 312 Jt',
    sub: 'semester ini',
    trend: '+4.1%',
    trendUp: false,
    icon: Wallet,
    color: 'text-amber-500',
    bg: 'bg-amber-500/10'
  }
]
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
        <div class="text-2xl font-bold tracking-tight">{{ stat.value }}</div>
        <div class="mt-1 flex items-center gap-1.5">
          <span
            :class="['flex items-center gap-0.5 text-xs font-semibold',
              stat.trendUp ? 'text-emerald-500' : 'text-rose-500']"
          >
            <component :is="stat.trendUp ? TrendingUp : TrendingDown" class="size-3" />
            {{ stat.trend }}
          </span>
          <span class="text-xs text-muted-foreground">{{ stat.sub }}</span>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
