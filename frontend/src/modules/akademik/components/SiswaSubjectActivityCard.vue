<script setup>
import { computed } from 'vue'
import { Sparkles, Eye } from 'lucide-vue-next'
import AppCard from '@/components/app-card/AppCard.vue'
import ActivityCard from '@/components/activity-card/ActivityCard.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'

const props = defineProps({
  subjectName: {
    type: String,
    default: ''
  },
  activities: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['open-sheet'])

// Strictly limit initial view to 3 items
const displayedActivities = computed(() => {
  return props.activities.slice(0, 3)
})
</script>

<template>
  <AppCard
    :title="`Aktivitas Pelajaran ${subjectName}`"
    description="Detail aktivitas atau kegiatan mata pelajaran dalam satu semester"
    header-class="pb-3"
    content-class="space-y-3"
  >
    <div v-if="displayedActivities.length > 0" class="space-y-2.5 min-h-[180px]">
      <ActivityCard
        v-for="(item, i) in displayedActivities"
        :key="i"
        :date="item.date"
        :month="item.month"
        :title="item.title"
        :description="item.description"
        :trailing-icon="item.icon"
        :variant="item.variant"
      />
    </div>

    <div v-else class="flex flex-col items-center justify-center py-12 text-center text-muted-foreground border border-dashed rounded-xl min-h-[180px]">
      <Sparkles class="size-8 mb-2 opacity-50 text-muted-foreground" />
      <p class="text-sm font-semibold">Belum Ada Aktivitas</p>
      <p class="text-xs">Belum ada aktivitas atau kegiatan terdaftar pada mata pelajaran ini.</p>
    </div>

    <!-- Button: Lihat Semua Kegiatan -->
    <div class="pt-3 border-t border-border/40 flex justify-center">
      <Button
        type="button"
        variant="outline"
        size="sm"
        class="rounded-xl text-xs font-bold gap-2 w-full sm:w-auto h-9 hover:bg-primary/10 border-primary/30 text-primary cursor-pointer transition-all"
        @click="emit('open-sheet')"
      >
        <Eye class="size-3.5" />
        <span>Lihat Semua Kegiatan</span>
        <Badge variant="secondary" class="text-[10px] px-1.5 py-0 rounded font-extrabold ml-1">
          {{ activities.length }}
        </Badge>
      </Button>
    </div>
  </AppCard>
</template>
