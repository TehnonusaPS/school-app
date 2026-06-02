<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { TrendingDown } from 'lucide-vue-next'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import { kelasPerformaData as kelas } from '../data/kepsekPerformaData'

const rataColor = (val: number) => {
  if (val < 7.0) return 'text-rose-500'
  if (val < 7.5) return 'text-amber-500'
  return 'text-emerald-500'
}

const kehadiranVariant = (val: number) => val < 80 ? 'destructive' : val < 90 ? 'secondary' : 'outline'

const props = defineProps({
  delay: { type: Number, default: 0 }
})
</script>

<template>
  <WidgetCard
    title="Kelas dengan Performa Menurun"
    description="Kelas dengan rata-rata nilai terendah semester ini"
    contentClass="p-0"
    :delay="delay"
  >
    <template #header-action>
      <TrendingDown class="size-4 text-rose-500" />
    </template>
    
    <!-- Fixed header -->
    <table class="w-full text-sm border-b">
      <colgroup>
        <col style="width: 5%" />
        <col style="width: 18%" />
        <col style="width: 15%" />
        <col style="width: 15%" />
        <col style="width: 47%" />
      </colgroup>
      <thead>
        <tr class="border-b bg-muted/50">
          <th class="pl-6 py-3 text-left text-xs font-medium text-muted-foreground">#</th>
          <th class="py-3 text-left text-xs font-medium text-muted-foreground">Kelas</th>
          <th class="py-3 text-center text-xs font-medium text-muted-foreground">Kehadiran %</th>
          <th class="py-3 text-right text-xs font-medium text-muted-foreground">Rata-Rata</th>
          <th class="pr-6 py-3 text-right text-xs font-medium text-muted-foreground">Wali Kelas</th>
        </tr>
      </thead>
    </table>

    <!-- Body -->
    <table class="w-full text-sm">
      <colgroup>
        <col style="width: 5%" />
        <col style="width: 18%" />
        <col style="width: 15%" />
        <col style="width: 15%" />
        <col style="width: 47%" />
      </colgroup>
      <tbody>
        <tr
          v-for="(k, i) in kelas"
          :key="i"
          class="border-b transition-colors hover:bg-muted/50 cursor-default"
        >
          <td class="pl-6 py-3 text-xs text-muted-foreground">{{ i + 1 }}</td>
          <td class="py-3 font-semibold text-sm">{{ k.nama }}</td>
          <td class="py-3 text-center">
            <Badge :variant="kehadiranVariant(k.kehadiran)" class="text-xs font-mono">
              {{ k.kehadiran }}%
            </Badge>
          </td>
          <td class="py-3 text-right">
            <span :class="['text-sm font-bold tabular-nums', rataColor(k.rata)]">
              {{ k.rata.toFixed(1) }}
            </span>
          </td>
          <td class="pr-6 py-3 text-right text-muted-foreground text-sm">{{ k.wali }}</td>
        </tr>
      </tbody>
    </table>
  </WidgetCard>
</template>
