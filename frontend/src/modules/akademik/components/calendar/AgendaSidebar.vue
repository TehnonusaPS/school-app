<script setup>
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Edit2, Trash2, ArrowRight, CalendarDays } from 'lucide-vue-next'
import { getEventBadgeStyle, getEventTypeInfo } from '../../data/calendarConstants'

const props = defineProps({
  events: { type: Array, default: () => [] },
  filteredEvents: { type: Array, default: () => [] },
  selectedFilter: { type: String, default: 'all' },
  readonly: { type: Boolean, default: false }
})

const emit = defineEmits(['filter-change', 'edit', 'delete'])

function getEventTypeLabel(type) {
  return getEventTypeInfo(type).label
}
</script>

<template>
  <Card class="rounded-2xl border border-border dark:border-zinc-800 bg-card shadow-sm text-left flex flex-col h-full max-h-[660px] overflow-hidden">
    <CardHeader class="pb-3 bg-muted/20 dark:bg-zinc-900/40 border-b border-border/60 flex flex-row items-center justify-between shrink-0">
      <div>
        <CardTitle class="text-xs font-bold text-foreground">Daftar Agenda Terbuat</CardTitle>
        <CardDescription class="text-[10px]">Total {{ events.length }} agenda terdaftar</CardDescription>
      </div>
      <Badge :show-dot="true" :pulse="true" variant="outline" class="text-[9px] font-mono uppercase bg-amber-500/10 text-amber-600 border-amber-500/30">
        {{ readonly ? 'Tinjauan' : 'Edit' }}
      </Badge>
    </CardHeader>

    <CardContent class="p-3.5 flex-1 flex flex-col min-h-0 space-y-3 overflow-hidden">
      <!-- Filter Category Chips -->
      <div class="flex items-center gap-1 overflow-x-auto pb-1 shrink-0 no-scrollbar text-[10px]">
        <button
          type="button"
          @click="emit('filter-change', 'all')"
          class="px-2.5 py-1 rounded-lg font-bold border transition-all cursor-pointer whitespace-nowrap"
          :class="selectedFilter === 'all' ? 'bg-primary text-primary-foreground border-primary' : 'bg-muted/40 text-muted-foreground hover:bg-muted'"
        >
          Semua ({{ events.length }})
        </button>
        <button
          type="button"
          @click="emit('filter-change', 'holiday')"
          class="px-2.5 py-1 rounded-lg font-bold border transition-all cursor-pointer whitespace-nowrap"
          :class="selectedFilter === 'holiday' ? 'bg-rose-500 text-white border-rose-500' : 'bg-rose-500/10 text-rose-600 border-rose-500/20 hover:bg-rose-500/20'"
        >
          Libur
        </button>
        <button
          type="button"
          @click="emit('filter-change', 'exam')"
          class="px-2.5 py-1 rounded-lg font-bold border transition-all cursor-pointer whitespace-nowrap"
          :class="selectedFilter === 'exam' ? 'bg-blue-500 text-white border-blue-500' : 'bg-blue-500/10 text-blue-600 border-blue-500/20 hover:bg-blue-500/20'"
        >
          Ujian
        </button>
        <button
          type="button"
          @click="emit('filter-change', 'academic')"
          class="px-2.5 py-1 rounded-lg font-bold border transition-all cursor-pointer whitespace-nowrap"
          :class="selectedFilter === 'academic' ? 'bg-cyan-500 text-white border-cyan-500' : 'bg-cyan-500/10 text-cyan-600 border-cyan-500/20 hover:bg-cyan-500/20'"
        >
          Akademik
        </button>
        <button
          type="button"
          @click="emit('filter-change', 'activity')"
          class="px-2.5 py-1 rounded-lg font-bold border transition-all cursor-pointer whitespace-nowrap"
          :class="selectedFilter === 'activity' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20 hover:bg-emerald-500/20'"
        >
          Kegiatan
        </button>
      </div>

      <!-- Scrollable Agenda List Filling Remaining Vertical Space -->
      <div class="flex-1 overflow-y-auto space-y-2.5 pr-1 no-scrollbar min-h-0 max-h-[540px]">
        <div
          v-for="ev in filteredEvents"
          :key="ev.id"
          class="p-3 rounded-xl border border-border/80 dark:border-zinc-800 bg-background dark:bg-zinc-950 space-y-2 text-left"
        >
          <div class="flex items-center justify-between gap-2">
            <Badge :show-dot="true" :pulse="true" variant="outline" class="text-[9px] font-bold uppercase gap-1" :class="getEventBadgeStyle(ev.type)">
              {{ getEventTypeLabel(ev.type) }}
            </Badge>
            <div v-if="!readonly" class="flex items-center gap-1.5">
              <button
                type="button"
                @click="emit('edit', ev)"
                class="text-muted-foreground hover:text-foreground transition-colors p-1 rounded-md hover:bg-muted cursor-pointer"
                title="Edit Agenda"
              >
                <Edit2 class="h-3.5 w-3.5" />
              </button>
              <button
                type="button"
                @click="emit('delete', ev.id)"
                class="text-rose-500 hover:text-rose-600 transition-colors p-1 rounded-md hover:bg-rose-500/10 cursor-pointer"
                title="Hapus Agenda"
              >
                <Trash2 class="h-3.5 w-3.5" />
              </button>
            </div>
          </div>

          <h5 class="text-xs font-bold text-foreground leading-snug">
            {{ ev.title }}
          </h5>

          <div class="flex items-center gap-2 text-[10px] font-mono text-muted-foreground">
            <span>{{ ev.startDate }}</span>
            <ArrowRight class="h-3 w-3 opacity-60" />
            <span>{{ ev.endDate }}</span>
          </div>

          <p v-if="ev.description" class="text-[11px] text-muted-foreground line-clamp-2 leading-relaxed">
            {{ ev.description }}
          </p>
        </div>

        <div v-if="filteredEvents.length === 0" class="py-16 text-center space-y-2">
          <CalendarDays class="h-8 w-8 text-muted-foreground/30 mx-auto" />
          <div class="text-xs font-bold text-muted-foreground">Belum ada agenda terdaftar</div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
