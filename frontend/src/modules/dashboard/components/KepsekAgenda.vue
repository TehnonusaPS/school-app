<script setup lang="ts">
import { CalendarDays } from 'lucide-vue-next'
import WidgetList from '@/components/dashboard-widget/WidgetList.vue'
import { agendaData } from '../data/kepsekAgendaData'

const typeColor: Record<string, string> = {
  rapat:    'bg-blue-500',
  tamu:     'bg-violet-500',
  evaluasi: 'bg-amber-500',
  upacara:  'bg-emerald-500'
}

const props = defineProps({
  delay: { type: Number, default: 0 }
})

interface AgendaItem {
  id: number
  tanggal: string
  bulan: string
  judul: string
  waktu: string
  tempat: string
  type: string
}

const asAgendaItem = (item: unknown) => item as AgendaItem
</script>

<template>
  <WidgetList
    title="Agenda Sekolah"
    description="Jadwal kegiatan yang akan datang"
    :icon="CalendarDays"
    footerText="Lihat Kalender Lengkap"
    :items="agendaData"
    cardClass="lg:col-span-2"
    listClass="h-[320px] px-4"
    :delay="delay"
    illustration="school_bell"
  >
    <template #item="{ item }">
      <!-- Tanggal box -->
      <div :class="['flex flex-col items-center justify-center rounded-lg text-white min-w-[44px] py-1.5', typeColor[asAgendaItem(item).type] ?? 'bg-primary']">
        <span class="text-base font-bold leading-none">{{ asAgendaItem(item).tanggal }}</span>
        <span class="text-[10px] font-medium leading-none mt-0.5">{{ asAgendaItem(item).bulan }}</span>
      </div>
      <!-- Detail -->
      <div class="min-w-0 flex-1 ml-2">
        <p class="text-sm font-semibold leading-snug truncate">{{ asAgendaItem(item).judul }}</p>
        <p class="text-[11px] text-muted-foreground mt-0.5">
          {{ asAgendaItem(item).waktu }} &bull; {{ asAgendaItem(item).tempat }}
        </p>
      </div>
    </template>
  </WidgetList>
</template>
