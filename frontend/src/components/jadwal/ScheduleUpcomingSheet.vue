<script setup lang="ts">
import { computed } from 'vue';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription } from '@/components/ui/sheet';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Calendar } from 'lucide-vue-next';
import ScheduleEventCard from './ScheduleEventCard.vue';

const props = defineProps<{
  open: boolean;
  upcomingEvents: any[];
}>();

const emit = defineEmits<{
  (e: 'update:open', val: boolean): void;
}>();

const formatIndoDate = (dateStr: string) => {
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};

const groupedUpcomingEvents = computed(() => {
  const groups: { dateStr: string; events: any[] }[] = [];
  props.upcomingEvents.forEach(event => {
    let group = groups.find(g => g.dateStr === event.tanggal);
    if (!group) {
      group = { dateStr: event.tanggal, events: [] };
      groups.push(group);
    }
    group.events.push(event);
  });
  return groups;
});
</script>

<template>
  <Sheet :open="open" @update:open="emit('update:open', $event)">
    <SheetContent side="right" class="w-full sm:max-w-md flex flex-col h-full p-0 overflow-hidden">
      <!-- Header -->
      <SheetHeader class="px-6 py-5 border-b border-border/30 shrink-0">
        <SheetTitle class="text-base font-bold">Agenda Mendatang</SheetTitle>
        <SheetDescription class="text-xs text-muted-foreground mt-1">
          Berikut daftar lengkap kegiatan, ujian, dan libur nasional mendatang.
        </SheetDescription>
      </SheetHeader>

      <!-- Scrollable content -->
      <ScrollArea class="flex-1 min-h-0">
        <div class="px-6 py-5 space-y-6 pb-10">
          <div v-if="groupedUpcomingEvents.length === 0" class="text-center py-12 text-sm text-muted-foreground">
            Tidak ada agenda mendatang.
          </div>

          <div
            v-else
            v-for="group in groupedUpcomingEvents"
            :key="group.dateStr"
            class="space-y-3"
          >
            <!-- Date group header -->
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[4px] bg-primary/10 text-primary text-[11px] font-bold border border-primary/20 shadow-sm">
              <Calendar class="size-3.5" />
              {{ formatIndoDate(group.dateStr) }}
            </span>

            <div class="space-y-3">
              <ScheduleEventCard
                v-for="event in group.events"
                :key="event.id"
                :type="event.type"
                :title="event.title"
                :subtitle="event.subtitle"
                :time="event.time"
                :location="event.location"
                :description="event.type === 'assignment' ? event.location : undefined"
              />
            </div>
          </div>
        </div>
      </ScrollArea>
    </SheetContent>
  </Sheet>
</template>
