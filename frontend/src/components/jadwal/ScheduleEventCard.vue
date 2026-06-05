<script setup lang="ts">
import { computed } from 'vue';
import { Clock, MapPin, User, AlertCircle, FileText, Calendar, BookOpen } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

const props = defineProps<{
  type: 'lesson' | 'holiday' | 'exam' | 'assignment';
  title: string;
  subtitle?: string;
  time?: string;
  location?: string;
  guru?: string;
  kelas?: string;
  description?: string;
}>();

const eventConfig = computed(() => {
  switch (props.type) {
    case 'holiday':
      return {
        badgeVariant: 'red' as const,
        badgeLabel: 'LIBUR NASIONAL',
        borderColor: 'border-l-red-500',
        bgColor: 'bg-red-500/5',
        icon: AlertCircle,
        iconColor: 'text-red-500'
      };
    case 'exam':
      return {
        badgeVariant: 'purple' as const,
        badgeLabel: 'UJIAN',
        borderColor: 'border-l-purple-500',
        bgColor: 'bg-purple-500/5',
        icon: Calendar,
        iconColor: 'text-purple-500'
      };
    case 'assignment':
      return {
        badgeVariant: 'green' as const,
        badgeLabel: 'TUGAS',
        borderColor: 'border-l-emerald-500',
        bgColor: 'bg-emerald-500/5',
        icon: FileText,
        iconColor: 'text-emerald-500'
      };
    case 'lesson':
    default:
      return {
        badgeVariant: 'blue' as const,
        badgeLabel: 'MATA PELAJARAN',
        borderColor: 'border-l-blue-500',
        bgColor: 'bg-blue-500/5',
        icon: BookOpen,
        iconColor: 'text-blue-500'
      };
  }
});
</script>

<template>
  <div :class="['rounded-lg border-l-[3px] p-3 space-y-1.5 transition-all hover:translate-x-0.5 border border-border/20', eventConfig.borderColor, eventConfig.bgColor]">
    <div class="flex items-start justify-between gap-2">
      <div class="space-y-1 min-w-0">
        <Badge :variant="eventConfig.badgeVariant" class="text-[8px] h-4 font-bold tracking-wider px-1.5">
          {{ eventConfig.badgeLabel }}
        </Badge>
        <h4 class="font-bold text-sm leading-snug text-foreground truncate">
          {{ title }}
        </h4>
      </div>
      <component :is="eventConfig.icon" :class="['size-4 shrink-0 mt-1', eventConfig.iconColor]" />
    </div>

    <div class="flex flex-col gap-1 text-[11px] text-muted-foreground">
      <div v-if="subtitle || kelas" class="flex items-center gap-1.5 min-w-0">
        <Calendar class="size-3 text-muted-foreground/50 shrink-0" />
        <span class="truncate">{{ subtitle || kelas }}</span>
      </div>
      <div v-if="time" class="flex items-center gap-1.5 min-w-0">
        <Clock class="size-3 text-muted-foreground/50 shrink-0" />
        <span class="truncate">{{ time }}</span>
      </div>
      <div v-if="location" class="flex items-center gap-1.5 min-w-0">
        <MapPin class="size-3 text-muted-foreground/50 shrink-0" />
        <span class="truncate">{{ location }}</span>
      </div>
      <div v-if="guru" class="flex items-center gap-1.5 min-w-0">
        <User class="size-3 text-muted-foreground/50 shrink-0" />
        <span class="truncate">Guru: {{ guru }}</span>
      </div>
    </div>

    <p v-if="description" class="text-[10px] text-muted-foreground bg-muted/30 p-2 rounded-md border border-border/10 leading-relaxed">
      {{ description }}
    </p>
  </div>
</template>
