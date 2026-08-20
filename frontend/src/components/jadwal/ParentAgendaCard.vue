<script setup>
import { computed } from 'vue'
import { Calendar, CalendarDays, BookOpen } from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'

const props = defineProps({
  type: {
    type: String,
    required: true // 'tugas' | 'ujian_harian' | 'kegiatan' | 'libur' | 'agenda_sekolah'
  },
  title: {
    type: String,
    required: true
  },
  date: {
    type: String,
    default: ''
  },
  endDate: {
    type: String,
    default: ''
  },
  classroomName: {
    type: String,
    default: ''
  },
  subjectName: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  }
})

const badgeConfig = computed(() => {
  switch (props.type) {
    case 'tugas':
      return {
        label: 'TUGAS KELAS',
        variant: 'green',
        dateLabel: 'Deadline',
        borderClass: 'border-emerald-500/30 hover:border-emerald-500/60',
        bgClass: 'bg-emerald-500/5',
        iconColor: 'text-emerald-500'
      }
    case 'ujian_harian':
      return {
        label: 'UJIAN HARIAN',
        variant: 'indigo',
        dateLabel: 'Tanggal',
        borderClass: 'border-indigo-500/30 hover:border-indigo-500/60',
        bgClass: 'bg-indigo-500/5',
        iconColor: 'text-indigo-500'
      }
    case 'kegiatan':
      return {
        label: 'KEGIATAN KELAS',
        variant: 'amber',
        dateLabel: 'Tanggal',
        borderClass: 'border-amber-500/30 hover:border-amber-500/60',
        bgClass: 'bg-amber-500/5',
        iconColor: 'text-amber-500'
      }
    case 'libur':
      return {
        label: 'HARI LIBUR',
        variant: 'red',
        dateLabel: 'Tanggal',
        borderClass: 'border-red-500/30 hover:border-red-500/60',
        bgClass: 'bg-red-500/5',
        iconColor: 'text-red-500'
      }
    case 'agenda_sekolah':
    default:
      return {
        label: 'AGENDA SEKOLAH',
        variant: 'green',
        dateLabel: 'Tanggal',
        borderClass: 'border-emerald-500/30 hover:border-emerald-500/60',
        bgClass: 'bg-emerald-500/5',
        iconColor: 'text-emerald-500'
      }
  }
})

const formatDateIndo = (dateStr) => {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    if (isNaN(d.getTime())) return dateStr
    return d.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    })
  } catch {
    return dateStr
  }
}
</script>

<template>
  <div
    class="p-3.5 rounded-2xl border transition-all shadow-2xs backdrop-blur-md bg-card/60 flex flex-col justify-between space-y-2.5 overflow-hidden"
    :class="[badgeConfig.borderClass, badgeConfig.bgClass]"
  >
    <div class="space-y-2">
      <!-- Header Row: Badge & Date -->
      <div class="flex items-center justify-between gap-2 flex-wrap">
        <Badge :variant="badgeConfig.variant" class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-md">
          {{ badgeConfig.label }}
        </Badge>
        <span class="text-xs font-bold text-muted-foreground flex items-center gap-1">
          <CalendarDays class="size-3.5 shrink-0" :class="badgeConfig.iconColor" />
          <span>{{ badgeConfig.dateLabel }}: {{ formatDateIndo(date) }}</span>
          <span v-if="endDate && endDate !== date"> s/d {{ formatDateIndo(endDate) }}</span>
        </span>
      </div>

      <!-- Content: Title & Details -->
      <div>
        <h4 class="text-sm font-extrabold text-foreground leading-snug">
          {{ title }}
        </h4>

        <div v-if="classroomName || subjectName" class="flex flex-col gap-1 mt-1.5 text-xs text-muted-foreground font-medium">
          <div v-if="classroomName" class="flex items-center gap-1.5">
            <Calendar class="size-3 text-muted-foreground/60 shrink-0" />
            <span>{{ classroomName }}</span>
          </div>
          <div v-if="subjectName" class="flex items-center gap-1.5 font-semibold text-foreground/90">
            <BookOpen class="size-3 text-primary/70 shrink-0" />
            <span>Mata Pelajaran : {{ subjectName }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Description (Compact) -->
    <p v-if="description" class="text-xs text-muted-foreground bg-background/50 p-2.5 rounded-xl border border-border/20 leading-relaxed">
      {{ description }}
    </p>
  </div>
</template>
