<script setup lang="ts">
import { ref } from 'vue';
import { Plus } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { useJadwal } from '../composables/useJadwal';
import ScheduleMonthYearPicker from '@/components/jadwal/ScheduleMonthYearPicker.vue';
import ScheduleCalendar from '@/components/jadwal/ScheduleCalendar.vue';
import ScheduleDetailPanel from '@/components/jadwal/ScheduleDetailPanel.vue';
import ScheduleUpcomingSheet from '@/components/jadwal/ScheduleUpcomingSheet.vue';

const {
  role,
  selectedDate,
  visibleMonth,
  visibleYear,
  selectedDateDetails,
  upcomingEvents,
  getDateMarkers,
  getHolidayForDate,
  getExamsForDate,
  getAssignmentsForDate
} = useJadwal();

const isSheetOpen = ref(false);

const onSelectDate = (date: Date) => {
  selectedDate.value = date;
  const clickedMonth = date.getMonth();
  const clickedYear = date.getFullYear();
  if (clickedMonth !== visibleMonth.value || clickedYear !== visibleYear.value) {
    visibleMonth.value = clickedMonth;
    visibleYear.value = clickedYear;
  }
};

const onTambahAgenda = () => {
  toast.success('Fitur Tambah Agenda segera hadir!', {
    description: 'Anda akan dapat membuat agenda tugas, ujian, atau hari libur kelas.',
  });
};
</script>

<template>
  <!-- Full height wrapper — no page scroll -->
  <div class="flex flex-col lg:h-[calc(100dvh-7rem)] lg:overflow-hidden h-auto overflow-visible">

    <!-- Main Two-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-[5fr_3fr] gap-5 lg:gap-6 flex-1 min-h-0">

      <!-- LEFT: Calendar Card (fixed height, no internal scroll) -->
      <Card class="relative flex flex-col overflow-hidden min-h-0">
        <!-- Month/Year Picker + Legend -->
        <ScheduleMonthYearPicker
          v-model:month="visibleMonth"
          v-model:year="visibleYear"
        />

        <!-- Separator -->
        <div class="h-px bg-border/30 mx-4 shrink-0" />

        <!-- Full-size Calendar Grid -->
        <ScheduleCalendar
          :month="visibleMonth"
          :year="visibleYear"
          :selectedDate="selectedDate"
          :getDateMarkers="getDateMarkers"
          :getHolidayForDate="getHolidayForDate"
          :getExamsForDate="getExamsForDate"
          :getAssignmentsForDate="getAssignmentsForDate"
          @select-date="onSelectDate"
        />

        <!-- Tambah Agenda button — pojok kanan bawah kalender (guru only) -->
        <Button
          v-if="role === 'guru'"
          @click="onTambahAgenda"
          size="sm"
          class="absolute bottom-4 right-4 z-10 flex items-center gap-1.5 shadow-lg"
        >
          <Plus class="size-4" />
          Tambah Agenda
        </Button>
      </Card>

      <!-- RIGHT: Detail Panel (only this scrolls) -->
      <ScheduleDetailPanel
        :selectedDate="selectedDate"
        :selectedDateDetails="selectedDateDetails"
        :upcomingEvents="upcomingEvents"
        @view-all="isSheetOpen = true"
      />

    </div>

    <!-- Sheet: Lihat Semua Mendatang -->
    <ScheduleUpcomingSheet
      v-model:open="isSheetOpen"
      :upcomingEvents="upcomingEvents"
    />
  </div>
</template>
