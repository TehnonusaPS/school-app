<script setup>
import { TrendingUp, TrendingDown, Minus } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card'

const props = defineProps({
  label: { type: String, required: true },
  value: { type: [String, Number], required: true },
  sub: { type: String, default: '' },
  trend: { type: [String, Number], default: '' },
  trendDirection: { 
    type: String, 
    default: 'up',
    validator: (v) => ['up', 'down', 'neutral'].includes(v)
  },
  icon: { type: [Object, Function], required: true },
  color: { type: String, default: 'text-primary' },
  bg: { type: String, default: 'bg-primary/10' }
})

function getTrendConfig(direction) {
  switch (direction) {
    case 'down':
      return { icon: TrendingDown, colorClass: 'text-red-500', bgClass: 'bg-red-500/10' }
    case 'neutral':
      return { icon: Minus, colorClass: 'text-slate-500', bgClass: 'bg-slate-500/10' }
    case 'up':
    default:
      return { icon: TrendingUp, colorClass: 'text-emerald-500', bgClass: 'bg-emerald-500/10' }
  }
}
</script>

<template>
  <Card
    class="relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-white/40 group glass-ui"
    style="contain: paint layout;"
  >
    <!-- Watermark Icon Background -->
    <component 
      v-if="icon"
      :is="icon" 
      :class="['absolute -right-4 -bottom-4 size-24 opacity-[0.04] rotate-12 transition-transform duration-300 group-hover:scale-110', color]" 
    />

    <CardHeader class="pb-2 relative z-10">
      <div class="flex items-center justify-between">
        <CardDescription class="text-sm font-medium">{{ label }}</CardDescription>
        <div :class="['rounded-xl p-2.5 backdrop-blur-md shadow-lg border border-white/10 transition-colors', bg]">
          <component :is="icon" :class="['size-4 drop-shadow-md', color]" />
        </div>
      </div>
    </CardHeader>
    
    <CardContent class="relative z-10">
      <div class="text-3xl font-bold tracking-tight drop-shadow-sm">{{ value }}</div>
      <div v-if="trend || sub" class="mt-1 flex items-center gap-1.5">
        <span 
          v-if="trend"
          :class="[
            'flex items-center gap-0.5 text-xs font-bold px-1.5 py-0.5 rounded-md backdrop-blur-sm', 
            getTrendConfig(trendDirection).colorClass,
            getTrendConfig(trendDirection).bgClass
          ]"
        >
          <component :is="getTrendConfig(trendDirection).icon" class="size-3" />
          {{ trend }}
        </span>
        <span v-if="sub" class="text-xs text-muted-foreground">{{ sub }}</span>
      </div>
    </CardContent>
  </Card>
</template>
