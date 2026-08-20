<script setup>
import { Calendar, Layers } from 'lucide-vue-next'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'

defineProps({
  selectedEvent: { type: Object, required: true },
  eventDates: { type: Array, required: true },
  totalAssignedSubjects: { type: Number, default: 0 },
  getDateStatus: { type: Function, required: true },
  formatDateIndo: { type: Function, required: true }
})

const selectedDateTab = defineModel('selectedDateTab')
</script>

<template>
  <Card class="border-border/60 shadow-xs">
    <CardContent class="p-5 space-y-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-border/50">
        <div>
          <h2 class="text-lg font-extrabold text-foreground flex items-center gap-2">
            <Calendar class="size-5 text-indigo-600 dark:text-indigo-400" />
            {{ selectedEvent.title }}
          </h2>
          <p class="text-xs text-muted-foreground mt-0.5">
            Rentang Tanggal: <span class="font-semibold text-foreground">{{ formatDateIndo(selectedEvent.start_date) }}</span> s.d <span class="font-semibold text-foreground">{{ formatDateIndo(selectedEvent.end_date) }}</span>
          </p>
        </div>
        
        <div class="flex items-center gap-2 text-xs">
          <Badge variant="secondary" class="bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-semibold px-3 py-1">
            {{ eventDates.length }} Hari Ujian
          </Badge>
          <Badge variant="secondary" class="bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-semibold px-3 py-1">
            {{ totalAssignedSubjects }} Mapel Terisi
          </Badge>
        </div>
      </div>

      <!-- Date Navigation Tabs with Pulse Dot Status -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <label class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Pilih Tanggal Ujian</label>
          <span class="text-[11px] text-muted-foreground flex items-center gap-3">
            <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-rose-500 animate-pulse" /> Belum Lengkap</span>
            <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-emerald-500 animate-pulse" /> Lengkap / Bebas Ujian</span>
          </span>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
          <Button 
            @click="selectedDateTab = 'all'"
            :variant="selectedDateTab === 'all' ? 'default' : 'outline'"
            size="sm"
            class="rounded-xl text-xs font-bold whitespace-nowrap"
          >
            <Layers class="size-3.5 mr-1.5" />
            Semua Tanggal ({{ eventDates.length }})
          </Button>

          <Button 
            v-for="dateStr in eventDates" 
            :key="dateStr"
            @click="selectedDateTab = dateStr"
            :variant="selectedDateTab === dateStr ? 'default' : 'outline'"
            size="sm"
            :class="[
              'rounded-xl text-xs font-semibold whitespace-nowrap flex items-center gap-1.5',
              selectedDateTab === dateStr ? 'bg-indigo-600 text-white' : ''
            ]"
          >
            <Calendar class="size-3.5" />
            <span>{{ formatDateIndo(dateStr, true) }}</span>

            <!-- GREEN PULSE DOT if free/complete, RED PULSE DOT if empty -->
            <span 
              v-if="getDateStatus(dateStr) === 'empty'"
              class="size-2 rounded-full bg-rose-500 animate-pulse" 
              title="Konfigurasi mapel belum lengkap"
            />
            <span 
              v-else
              class="size-2 rounded-full bg-emerald-500 animate-pulse" 
              title="Terkonfigurasi lengkap / Bebas Ujian"
            />
          </Button>
        </div>
      </div>
    </CardContent>
  </Card>
</template>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
  display: none;
}
.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
