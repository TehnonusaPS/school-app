<script setup lang="ts">
import { computed } from 'vue';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  month: number; // 0-11
  year: number;
}>();

const emit = defineEmits<{
  (e: 'update:month', val: number): void;
  (e: 'update:year', val: number): void;
}>();

const INDO_MONTHS = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const yearRange = computed(() => {
  const current = new Date().getFullYear();
  const range = [];
  for (let i = current - 10; i <= current + 10; i++) {
    range.push(i);
  }
  return range;
});

const activeLabel = computed(() => {
  return `${INDO_MONTHS[props.month]} ${props.year}`;
});

const prevMonth = () => {
  if (props.month === 0) {
    emit('update:month', 11);
    emit('update:year', props.year - 1);
  } else {
    emit('update:month', props.month - 1);
  }
};

const nextMonth = () => {
  if (props.month === 11) {
    emit('update:month', 0);
    emit('update:year', props.year + 1);
  } else {
    emit('update:month', props.month + 1);
  }
};

const onMonthChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  emit('update:month', Number(target.value));
};

const onYearChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  emit('update:year', Number(target.value));
};
</script>

<template>
  <div class="p-4 pb-3 space-y-3 shrink-0">
    <!-- Row 1: Active Month/Year Label + Navigation -->
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-bold text-foreground tracking-tight">
        {{ activeLabel }}
      </h2>

      <div class="flex items-center gap-2">
        <select
          :value="month"
          @change="onMonthChange"
          class="h-8 rounded-lg border border-border bg-background px-2.5 pr-7 text-xs font-semibold text-foreground appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
        >
          <option v-for="(name, index) in INDO_MONTHS" :key="index" :value="index">
            {{ name }}
          </option>
        </select>

        <select
          :value="year"
          @change="onYearChange"
          class="h-8 rounded-lg border border-border bg-background px-2.5 pr-7 text-xs font-semibold text-foreground appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
        >
          <option v-for="yr in yearRange" :key="yr" :value="yr">
            {{ yr }}
          </option>
        </select>

        <div class="flex items-center border border-border rounded-lg overflow-hidden bg-background/50">
          <Button variant="ghost" size="icon" class="size-8 rounded-none border-r border-border hover:bg-accent/50" @click="prevMonth">
            <ChevronLeft class="size-4" />
          </Button>
          <Button variant="ghost" size="icon" class="size-8 rounded-none hover:bg-accent/50" @click="nextMonth">
            <ChevronRight class="size-4" />
          </Button>
        </div>
      </div>
    </div>

    <!-- Row 2: Dot Legends -->
    <div class="flex items-center gap-4 text-xs font-medium text-muted-foreground flex-wrap">
      <div class="flex items-center gap-1.5">
        <span class="size-2.5 rounded-full bg-blue-500 shrink-0"></span>
        <span>Mata Pelajaran</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="size-2.5 rounded-full bg-purple-500 shrink-0"></span>
        <span>Ujian</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="size-2.5 rounded-full bg-red-500 shrink-0"></span>
        <span>Libur</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="size-2.5 rounded-full bg-emerald-500 shrink-0"></span>
        <span>Tugas</span>
      </div>
    </div>
  </div>
</template>
