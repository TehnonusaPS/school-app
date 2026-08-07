<script setup lang="ts">
import { computed } from 'vue'
import { Clock, MapPin, User, AlertCircle, FileText, Calendar, BookOpen, Coffee, Trash2, Edit3, Sparkles } from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'

const props = defineProps<{
  type: 'lesson' | 'break' | 'holiday' | 'exam' | 'assignment' | 'tugas' | 'ujian_harian' | 'kegiatan'
  title: string
  subtitle?: string
  subject?: string
  time?: string
  location?: string
  guru?: string
  kelas?: string
  description?: string
  canManage?: boolean
}>()

const emit = defineEmits<{
  (e: 'edit'): void
  (e: 'delete'): void
}>()

const eventConfig = computed(() => {
  switch (props.type) {
    case 'break':
      return {
        badgeVariant: 'amber' as const,
        badgeLabel: 'JAM ISTIRAHAT',
        borderColor: 'border-l-amber-500',
        bgColor: 'bg-amber-500/5',
        icon: Coffee,
        iconColor: 'text-amber-500'
      }
    case 'holiday':
      return {
        badgeVariant: 'red' as const,
        badgeLabel: 'LIBUR NASIONAL',
        borderColor: 'border-l-red-500',
        bgColor: 'bg-red-500/5',
        icon: AlertCircle,
        iconColor: 'text-red-500'
      }
    case 'exam':
      return {
        badgeVariant: 'purple' as const,
        badgeLabel: 'UJIAN',
        borderColor: 'border-l-purple-500',
        bgColor: 'bg-purple-500/5',
        icon: Calendar,
        iconColor: 'text-purple-500'
      }
    case 'ujian_harian':
      return {
        badgeVariant: 'indigo' as const,
        badgeLabel: 'UJIAN HARIAN',
        borderColor: 'border-l-indigo-500',
        bgColor: 'bg-indigo-500/5',
        icon: Calendar,
        iconColor: 'text-indigo-500'
      }
    case 'kegiatan':
      return {
        badgeVariant: 'amber' as const,
        badgeLabel: 'KEGIATAN KELAS',
        borderColor: 'border-l-amber-500',
        bgColor: 'bg-amber-500/5',
        icon: Sparkles,
        iconColor: 'text-amber-500'
      }
    case 'tugas':
    case 'assignment':
      return {
        badgeVariant: 'green' as const,
        badgeLabel: 'TUGAS',
        borderColor: 'border-l-emerald-500',
        bgColor: 'bg-emerald-500/5',
        icon: FileText,
        iconColor: 'text-emerald-500'
      }
    case 'lesson':
    default:
      return {
        badgeVariant: 'blue' as const,
        badgeLabel: 'MATA PELAJARAN',
        borderColor: 'border-l-blue-500',
        bgColor: 'bg-blue-500/5',
        icon: BookOpen,
        iconColor: 'text-blue-500'
      }
  }
})
</script>

<template>
  <div
    :class="[
      'group relative rounded-lg border-l-[3px] p-3 space-y-1.5 transition-all hover:translate-x-0.5 border border-border/20',
      eventConfig.borderColor,
      eventConfig.bgColor
    ]"
  >
    <div class="flex items-start justify-between gap-2">
      <div class="space-y-1 min-w-0 flex-1">
        <div class="flex items-center gap-1.5">
          <Badge
            :variant="eventConfig.badgeVariant"
            class="text-[8px] h-4 font-bold tracking-wider px-1.5"
          >
            {{ eventConfig.badgeLabel }}
          </Badge>
        </div>
        <h4 class="font-bold text-sm leading-snug text-foreground truncate">
          {{ title }}
        </h4>
      </div>

      <div class="flex items-center gap-1 shrink-0">
        <div v-if="canManage" class="flex items-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
          <button
            type="button"
            @click.stop="emit('edit')"
            class="p-1 rounded text-muted-foreground hover:text-primary hover:bg-primary/10 transition-colors"
            title="Edit Agenda"
          >
            <Edit3 class="size-3.5" />
          </button>
          <button
            type="button"
            @click.stop="emit('delete')"
            class="p-1 rounded text-muted-foreground hover:text-red-500 hover:bg-red-500/10 transition-colors"
            title="Hapus Agenda"
          >
            <Trash2 class="size-3.5" />
          </button>
        </div>
        <component
          :is="eventConfig.icon"
          :class="['size-4 shrink-0', eventConfig.iconColor]"
        />
      </div>
    </div>

    <div class="flex flex-col gap-1 text-[11px] text-muted-foreground">
      <div
        v-if="subtitle || kelas"
        class="flex items-center gap-1.5 min-w-0"
      >
        <Calendar class="size-3 text-muted-foreground/50 shrink-0" />
        <span class="truncate">{{ subtitle || kelas }}</span>
      </div>
      <div
        v-if="subject"
        class="flex items-center gap-1.5 min-w-0 font-medium text-foreground/90"
      >
        <BookOpen class="size-3 text-primary/70 shrink-0" />
        <span class="truncate">Mata Pelajaran : {{ subject }}</span>
      </div>
      <div
        v-if="time"
        class="flex items-center gap-1.5 min-w-0"
      >
        <Clock class="size-3 text-muted-foreground/50 shrink-0" />
        <span class="truncate">{{ time }}</span>
      </div>
      <div
        v-if="location"
        class="flex items-center gap-1.5 min-w-0"
      >
        <MapPin class="size-3 text-muted-foreground/50 shrink-0" />
        <span class="truncate">{{ location }}</span>
      </div>
      <div
        v-if="guru"
        class="flex items-center gap-1.5 min-w-0"
      >
        <User class="size-3 text-muted-foreground/50 shrink-0" />
        <span class="truncate">Guru: {{ guru }}</span>
      </div>
    </div>

    <p
      v-if="description"
      class="text-[10px] text-muted-foreground bg-muted/30 p-2 rounded-md border border-border/10 leading-relaxed"
    >
      {{ description }}
    </p>
  </div>
</template>
