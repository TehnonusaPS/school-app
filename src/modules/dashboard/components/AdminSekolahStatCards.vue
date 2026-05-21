<script setup lang="ts">
import { Users, LayoutGrid, ClipboardCheck, Wallet, TrendingUp } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card'

const stats = [
  {
    label: 'Total Siswa',
    value: '402',
    sub: 'siswa aktif',
    trend: '94% hadir hari ini',
    icon: Users,
    color: 'text-blue-500',
    bg: 'bg-blue-500/10'
  },
  {
    label: 'Total Kelas',
    value: '18',
    sub: 'rombel aktif',
    trend: 'Semester Ganjil',
    icon: LayoutGrid,
    color: 'text-violet-500',
    bg: 'bg-violet-500/10'
  },
  {
    label: 'Absensi Hari Ini',
    value: '378',
    sub: '94% hadir',
    trend: '+2% dari kemarin',
    icon: ClipboardCheck,
    color: 'text-emerald-500',
    bg: 'bg-emerald-500/10'
  },
  {
    label: 'SPP Terkumpul',
    value: '82.2%',
    sub: 'Target Rp 2,4M',
    trend: '+5.1% bulan ini',
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
          <span class="flex items-center gap-0.5 text-xs font-semibold text-emerald-500">
            <TrendingUp class="size-3" />
            {{ stat.trend }}
          </span>
          <span class="text-xs text-muted-foreground">{{ stat.sub }}</span>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
