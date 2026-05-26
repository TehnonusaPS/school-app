<script setup>
import { computed } from 'vue'
import { TrendingUp, TrendingDown } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card'

const props = defineProps({
  stats: {
    type: Array,
    required: true
  }
})

const gridClass = computed(() => {
  const colMap = {
    1: 'grid-cols-1',
    2: 'grid-cols-1 sm:grid-cols-2',
    3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    4: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    5: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-5',
    6: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6'
  }
  return colMap[props.stats.length] || 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'
})

function getTrendIcon(direction) {
  return direction === 'down' ? TrendingDown : TrendingUp
}

function getTrendColor(direction) {
  return direction === 'down' ? 'text-red-500' : 'text-emerald-500'
}
</script>

<template>
  <div :class="['grid gap-4', gridClass]">
    <Card
      v-for="(stat, i) in stats"
      :key="i"
      class="transition-all duration-200 hover:shadow-md hover:-translate-y-0.5"
    >
      <CardHeader class="pb-2">
        <div class="flex items-center justify-between">
          <CardDescription class="text-sm font-medium">{{ stat.label }}</CardDescription>
          <div v-if="stat.icon" :class="['rounded-lg p-2', stat.bg ?? 'bg-blue-500/10']">
            <component :is="stat.icon" :class="['size-4', stat.color ?? 'text-blue-500']" />
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <div class="text-3xl font-bold tracking-tight">{{ stat.value }}</div>
        <div v-if="stat.trend || stat.sub" class="mt-1 flex items-center gap-1.5">
          <span
            v-if="stat.trend"
            :class="[
              'flex items-center gap-0.5 text-xs font-semibold',
              getTrendColor(stat.trendDirection)
            ]"
          >
            <component :is="getTrendIcon(stat.trendDirection)" class="size-3" />
            {{ stat.trend }}
          </span>
          <span v-if="stat.sub" class="text-xs text-muted-foreground">{{ stat.sub }}</span>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
