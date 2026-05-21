<script setup lang="ts">
import { Building2, GraduationCap, School, TrendingUp, Users } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card'

const stats = [
  {
    label: 'Total Yayasan',
    value: '120',
    sub: '+5 bulan ini',
    trend: '+4.3%',
    icon: Building2,
    color: 'text-blue-500',
    bg: 'bg-blue-500/10'
  },
  {
    label: 'Total Sekolah',
    value: '270',
    sub: '+12 bulan ini',
    trend: '+4.6%',
    icon: School,
    color: 'text-violet-500',
    bg: 'bg-violet-500/10'
  },
  {
    label: 'Total Guru',
    value: '2.930',
    sub: '+48 bulan ini',
    trend: '+1.7%',
    icon: GraduationCap,
    color: 'text-emerald-500',
    bg: 'bg-emerald-500/10'
  },
  {
    label: 'Total Siswa',
    value: '1.239K',
    sub: '+320 bulan ini',
    trend: '+2.1%',
    icon: Users,
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
  </div>
</template>
