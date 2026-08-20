<script setup>
import { Calendar, CalendarDays, Clock, ChevronDown, ChevronUp } from 'lucide-vue-next'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'

defineProps({
  exam: {
    type: Object,
    required: true
  },
  isExpanded: {
    type: Boolean,
    default: false
  },
  formatDateIndo: {
    type: Function,
    required: true
  }
})

const emit = defineEmits(['toggle-expand'])
</script>

<template>
  <Card class="border-border/70 shadow-2xs overflow-hidden transition-all hover:border-border bg-card/60 backdrop-blur-md">
    <CardContent class="p-3.5 sm:p-4 space-y-3">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-0.5">
          <div class="flex items-center gap-2 flex-wrap">
            <Badge variant="purple" class="font-bold text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wider">
              UJIAN RESMI
            </Badge>
            <span class="text-xs font-bold text-muted-foreground flex items-center gap-1">
              <CalendarDays class="size-3.5 text-violet-500" />
              {{ formatDateIndo(exam.start_date) }} — {{ formatDateIndo(exam.end_date) }}
            </span>
          </div>
          <h3 class="text-sm font-extrabold text-foreground pt-1">
            {{ exam.event_title }}
          </h3>
        </div>

        <Button
          v-if="exam.sessions && exam.sessions.length > 0"
          type="button"
          variant="outline"
          size="sm"
          class="rounded-lg text-xs font-bold gap-1.5 h-8 shrink-0 border-violet-500/30 hover:bg-violet-500/10 text-violet-700 dark:text-violet-300 cursor-pointer"
          @click="emit('toggle-expand')"
        >
          <span>{{ isExpanded ? 'Sembunyikan Sesi' : 'Rincian Sesi' }}</span>
          <Badge variant="secondary" class="text-[10px] px-1 py-0 rounded font-extrabold">
            {{ exam.sessions.length }}
          </Badge>
          <component :is="isExpanded ? ChevronUp : ChevronDown" class="size-3.5" />
        </Button>
      </div>

      <!-- Collapsible Session List -->
      <div v-if="isExpanded" class="pt-3 border-t border-border/60 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="s in exam.sessions"
          :key="s.id"
          class="p-3 rounded-xl bg-violet-500/5 border border-violet-500/20 space-y-1.5 flex flex-col justify-between"
        >
          <div class="flex items-center justify-between text-xs font-extrabold">
            <span class="text-violet-700 dark:text-violet-300 flex items-center gap-1">
              <Calendar class="size-3" />
              {{ formatDateIndo(s.exam_date) }}
            </span>
            <Badge variant="outline" class="text-[10px] bg-background/80 font-bold px-1.5 py-0">
              Sesi {{ s.session_number }}
            </Badge>
          </div>

          <div>
            <h5 class="text-xs font-extrabold text-foreground">
              {{ s.subject_name }}
            </h5>
            <div class="flex items-center justify-between gap-2 mt-0.5 text-[11px] text-muted-foreground">
              <span class="flex items-center gap-1 font-semibold text-foreground/80">
                <Clock class="size-3" />
                {{ s.start_time }} - {{ s.end_time }} WIB
              </span>
              <span v-if="s.notes" class="italic text-[10px] text-muted-foreground">
                {{ s.notes }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
