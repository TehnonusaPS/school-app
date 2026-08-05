<script setup>
import { Card, CardContent, CardHeader } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { getEventBadgeStyle } from '../../data/calendarConstants'

const props = defineProps({
  calendarDays: { type: Array, required: true },
  selectedDateStr: { type: String, default: '' },
  currentMonthLabel: { type: String, default: '' },
  activeMonthIdx: { type: Number, default: 0 },
  maxMonthIdx: { type: Number, default: 0 }
})

const emit = defineEmits(['select-cell', 'prev-month', 'next-month'])
</script>

<template>
  <Card class="border border-border dark:border-zinc-800 shadow-md h-full flex flex-col justify-between">
    <CardHeader class="flex flex-row items-center justify-between border-b dark:border-zinc-800 pb-4 shrink-0">
      <div class="flex items-center gap-3">
        <Button variant="outline" size="icon" @click="emit('prev-month')" :disabled="activeMonthIdx === 0">
          <ChevronLeft class="h-4 w-4" />
        </Button>
        <h2 class="text-base font-bold text-foreground dark:text-zinc-100 min-w-[160px] text-center">
          {{ currentMonthLabel }}
        </h2>
        <Button variant="outline" size="icon" @click="emit('next-month')" :disabled="activeMonthIdx === maxMonthIdx">
          <ChevronRight class="h-4 w-4" />
        </Button>
      </div>
    </CardHeader>

    <CardContent class="p-4 flex-1 flex flex-col justify-between">
      <div>
        <!-- Days of Week Header -->
        <div class="grid grid-cols-7 gap-1 text-center font-semibold text-xs text-muted-foreground dark:text-zinc-400 mb-2">
          <div class="py-2">Senin</div>
          <div class="py-2">Selasa</div>
          <div class="py-2">Rabu</div>
          <div class="py-2">Kamis</div>
          <div class="py-2">Jumat</div>
          <div class="py-2">Sabtu</div>
          <div class="py-2 text-rose-500">Minggu</div>
        </div>

        <!-- Calendar Grid Cells -->
        <div class="grid grid-cols-7 gap-1.5">
          <div
            v-for="(cell, idx) in calendarDays"
            :key="idx"
            @click="emit('select-cell', cell)"
            class="min-h-[90px] p-1.5 rounded-xl border transition-all relative flex flex-col justify-between cursor-pointer"
            :class="[
              cell.isCurrentMonth
                ? (cell.dateStr === selectedDateStr ? 'bg-primary/5 border-primary shadow-xs' : 'bg-card dark:bg-zinc-900/80 border-border dark:border-zinc-800/80 hover:border-primary/50')
                : 'bg-muted/30 dark:bg-zinc-950/40 border-transparent opacity-40',
              cell.isSunday ? 'bg-rose-500/5 dark:bg-rose-950/10' : ''
            ]"
          >
            <div class="flex items-center justify-between text-xs">
              <span
                class="font-semibold px-1.5 py-0.5 rounded-md"
                :class="cell.isSunday ? 'text-rose-500 font-bold' : 'text-foreground dark:text-zinc-200'"
              >
                {{ cell.dayNumber }}
              </span>
            </div>

            <!-- Agenda Indicators with Badge showDot and Pulse -->
            <div v-if="cell.isCurrentMonth && cell.events && cell.events.length > 0" class="space-y-1 mt-1">
              <Badge
                v-for="ev in cell.events.slice(0, 2)"
                :key="ev.id"
                :show-dot="true"
                :pulse="true"
                variant="outline"
                class="text-[9px] px-1.5 py-0.5 rounded-md font-medium truncate flex items-center gap-1 border w-full justify-start"
                :class="getEventBadgeStyle(ev.type)"
              >
                <span class="truncate">{{ ev.title }}</span>
              </Badge>
              <div v-if="cell.events.length > 2" class="text-[9px] text-muted-foreground px-1 font-semibold">
                +{{ cell.events.length - 2 }} agenda lagi
              </div>
            </div>
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
