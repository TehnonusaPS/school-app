<script setup>
import { Sparkles } from 'lucide-vue-next'
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetDescription
} from '@/components/ui/sheet'
import ActivityCard from '@/components/activity-card/ActivityCard.vue'

defineProps({
  open: {
    type: Boolean,
    default: false
  },
  subjectName: {
    type: String,
    default: ''
  },
  activities: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:open'])
</script>

<template>
  <Sheet :open="open" @update:open="val => emit('update:open', val)">
    <SheetContent class="w-full sm:max-w-md md:max-w-lg flex flex-col p-0 bg-card border-l border-border/80 shadow-2xl">
      <SheetHeader class="p-5 border-b border-border/60 bg-muted/30">
        <SheetTitle class="text-base font-extrabold text-foreground flex items-center gap-2">
          <Sparkles class="size-4.5 text-primary" />
          <span>Aktivitas Pelajaran {{ subjectName }}</span>
        </SheetTitle>
        <SheetDescription class="text-xs text-muted-foreground pt-0.5">
          Seluruh daftar aktivitas, tugas, ujian harian, dan materi untuk mata pelajaran {{ subjectName }} semester ini.
        </SheetDescription>
      </SheetHeader>

      <div class="p-5 space-y-3 overflow-y-auto flex-1">
        <div v-if="activities.length === 0" class="py-16 text-center text-muted-foreground space-y-2">
          <Sparkles class="size-8 mx-auto opacity-50 text-muted-foreground" />
          <p class="text-sm font-bold">Belum Ada Aktivitas</p>
          <p class="text-xs">Belum ada kegiatan atau agenda yang tercatat untuk mata pelajaran ini.</p>
        </div>

        <div v-else class="space-y-2.5">
          <ActivityCard
            v-for="(item, i) in activities"
            :key="`sheet-${i}`"
            :date="item.date"
            :month="item.month"
            :title="item.title"
            :description="item.description"
            :trailing-icon="item.icon"
            :variant="item.variant"
          />
        </div>
      </div>
    </SheetContent>
  </Sheet>
</template>
