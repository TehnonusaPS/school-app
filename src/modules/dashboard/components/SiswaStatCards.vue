<script setup lang="ts">
import { UserCheck, FileText, UserX } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card'

const stats = [
  {
    label: 'Hadir',
    value: '120',
    sub: 'hari kehadiran',
    icon: UserCheck,
    color: 'text-emerald-500',
    bg: 'bg-emerald-500/10'
  },
  {
    label: 'Izin',
    value: '10',
    sub: 'hari izin',
    icon: FileText,
    color: 'text-amber-500',
    bg: 'bg-amber-500/10'
  },
  {
    label: 'Alpa',
    value: '—',
    sub: 'tidak hadir tanpa ket.',
    icon: UserX,
    color: 'text-rose-500',
    bg: 'bg-rose-500/10'
  }
]
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-3">
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
        <p class="text-xs text-muted-foreground mt-1">{{ stat.sub }}</p>
      </CardContent>
    </Card>
  </div>
</template>
