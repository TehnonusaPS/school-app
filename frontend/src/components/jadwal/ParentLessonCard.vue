<script setup>
import { Clock, User, MapPin } from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'

defineProps({
  lesson: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div class="p-3.5 rounded-xl border border-border/60 bg-card/60 hover:border-border transition-all flex items-center justify-between gap-3 shadow-2xs backdrop-blur-xs">
    <div class="space-y-1 min-w-0">
      <div class="flex items-center gap-2 flex-wrap">
        <h4 class="text-sm font-extrabold text-foreground truncate">
          {{ lesson.mapel }}
        </h4>
        <Badge v-if="lesson.code" variant="outline" class="text-[10px] font-bold px-1.5 py-0 rounded">
          {{ lesson.code }}
        </Badge>
      </div>

      <p v-if="lesson.guru" class="text-xs text-muted-foreground flex items-center gap-1 truncate font-medium">
        <User class="size-3 text-primary/70 shrink-0" />
        <span>{{ lesson.guru }}</span>
      </p>
    </div>

    <div class="text-right shrink-0 space-y-1">
      <div class="px-2 py-0.5 rounded-md bg-primary/10 text-primary text-[11px] font-extrabold inline-flex items-center gap-1">
        <Clock class="size-3" />
        {{ lesson.mulai }} - {{ lesson.selesai }}
      </div>
      <div v-if="lesson.ruang" class="text-[11px] font-semibold text-muted-foreground flex items-center justify-end gap-1">
        <MapPin class="size-3 text-muted-foreground/70" />
        {{ lesson.ruang }}
      </div>
    </div>
  </div>
</template>
