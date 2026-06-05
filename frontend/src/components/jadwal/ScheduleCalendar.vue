<script setup lang="ts">
import { computed, ref, watch } from 'vue';
// CalendarRoot + CellTrigger from reka-ui directly (avoid shadcn button variant sizing)
import { CalendarRoot, CalendarCellTrigger } from 'reka-ui';
// Grid structure components from shadcn (thin wrappers)
import {
  CalendarGrid,
  CalendarGridHead,
  CalendarGridBody,
  CalendarGridRow,
  CalendarHeadCell,
  CalendarCell
} from '@/components/ui/calendar';
import { Badge } from '@/components/ui/badge';
import { CalendarDate, getLocalTimeZone } from '@internationalized/date';
import { formatDateISO } from '@/modules/akademik/composables/useJadwal';

const props = defineProps<{
  month: number; // 0-11
  year: number;
  selectedDate: Date;
  getDateMarkers: (dateStr: string) => { isHoliday: boolean; isSunday: boolean; isExam: boolean; isAssignment: boolean };
  getHolidayForDate: (dateStr: string) => any;
  getExamsForDate: (dateStr: string) => any[];
  getAssignmentsForDate: (dateStr: string) => any[];
}>();

const emit = defineEmits<{
  (e: 'select-date', date: Date): void;
}>();

const WEEKDAYS = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MING'];

// v-model bridge: JS Date ↔ CalendarDate
const modelValue = computed({
  get() {
    const d = props.selectedDate;
    return new CalendarDate(d.getFullYear(), d.getMonth() + 1, d.getDate());
  },
  set(val) {
    if (val) {
      emit('select-date', val.toDate(getLocalTimeZone()));
    }
  }
});

// Placeholder controls which month is displayed
const placeholder = ref(new CalendarDate(props.year, props.month + 1, 1));

watch([() => props.year, () => props.month], ([y, m]) => {
  placeholder.value = new CalendarDate(y, m + 1, 1);
});

const todayStr = formatDateISO(new Date());
const selectedDateStr = computed(() => formatDateISO(props.selectedDate));

// Build dynamic classes for a day cell
const getCellClasses = (weekDate: any, monthValue: any) => {
  const dateStr = weekDate.toString();
  const markers = props.getDateMarkers(dateStr);
  const isOutside = weekDate.month !== monthValue.month;
  const isToday = dateStr === todayStr;
  const isSelected = dateStr === selectedDateStr.value;

  return [
    'sched-cell',
    isOutside && 'sched-cell--outside',
    isToday && 'sched-cell--today',
    isSelected && 'sched-cell--selected',
    (markers.isHoliday || markers.isSunday) && 'sched-cell--holiday',
    markers.isExam && 'sched-cell--exam'
  ].filter(Boolean).join(' ');
};
</script>

<template>
  <div class="flex-1 min-h-0 flex flex-col px-3 pb-3">
    <CalendarRoot
      v-slot="{ grid }"
      v-model="modelValue"
      v-model:placeholder="placeholder"
      locale="id-ID"
      :week-starts-on="1"
      class="sched-calendar-root flex-1 min-h-0 flex flex-col"
    >
      <CalendarGrid
        v-for="month in grid"
        :key="month.value.toString()"
        class="sched-grid flex-1"
      >
        <!-- Weekday header row -->
        <CalendarGridHead>
          <CalendarGridRow class="sched-row">
            <CalendarHeadCell
              v-for="day in WEEKDAYS"
              :key="day"
              class="sched-head-cell"
            >
              {{ day }}
            </CalendarHeadCell>
          </CalendarGridRow>
        </CalendarGridHead>

        <!-- Day cells -->
        <CalendarGridBody class="sched-body flex-1 min-h-0">
          <CalendarGridRow
            v-for="(weekDates, index) in month.rows"
            :key="`weekDate-${index}`"
            class="sched-row sched-row--body"
          >
            <CalendarCell
              v-for="weekDate in weekDates"
              :key="weekDate.toString()"
              :date="weekDate"
              class="sched-td"
            >
              <!-- Raw reka-ui CellTrigger — no button variant sizing -->
              <CalendarCellTrigger
                :day="weekDate"
                :month="month.value"
                :class="getCellClasses(weekDate, month.value)"
              >
                <!-- Day number -->
                <span class="sched-day-num">{{ weekDate.day }}</span>

                <!-- Micro badges -->
                <div class="sched-badges">
                  <Badge
                    v-if="getDateMarkers(weekDate.toString()).isHoliday"
                    variant="red"
                    class="sched-micro-badge"
                    :title="getHolidayForDate(weekDate.toString())?.localName"
                  >Libur</Badge>
                  <Badge
                    v-if="getDateMarkers(weekDate.toString()).isExam"
                    variant="purple"
                    class="sched-micro-badge"
                    :title="getExamsForDate(weekDate.toString())[0]?.nama"
                  >Ujian</Badge>
                  <Badge
                    v-if="getDateMarkers(weekDate.toString()).isAssignment"
                    variant="green"
                    class="sched-micro-badge"
                    :title="getAssignmentsForDate(weekDate.toString())[0]?.nama"
                  >Tugas</Badge>
                </div>
              </CalendarCellTrigger>
            </CalendarCell>
          </CalendarGridRow>
        </CalendarGridBody>
      </CalendarGrid>
    </CalendarRoot>
  </div>
</template>

<style scoped>
/* ── CalendarRoot Override ── */
.sched-calendar-root {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  backdrop-filter: none !important;
  -webkit-backdrop-filter: none !important;
  border-radius: 0 !important;
  padding: 0 !important;
}

.sched-calendar-root::after {
  display: none !important;
}

/* ── Calendar Grid — CSS Grid based ── */
.sched-grid {
  display: flex !important;
  flex-direction: column !important;
  border-collapse: unset !important;
  width: 100% !important;
}

/* ── Row: 7 equal columns ── */
.sched-row {
  display: grid !important;
  grid-template-columns: repeat(7, 1fr) !important;
  gap: 4px !important;
  width: 100% !important;
  margin: 0 !important;
}

.sched-row--body {
  flex: 1;
  min-height: 0;
}

/* ── Body: flex-col to distribute rows evenly ── */
.sched-body {
  display: flex !important;
  flex-direction: column !important;
  gap: 4px !important;
}

/* ── Head Cell ── */
.sched-head-cell {
  width: auto !important;
  text-align: center !important;
  font-weight: 700 !important;
  font-size: 0.65rem !important;
  text-transform: uppercase !important;
  letter-spacing: 0.08em !important;
  padding: 0.4rem 0 !important;
}

/* ── Table Cell (CalendarCell wrapper) ── */
.sched-td {
  padding: 0 !important;
  position: relative !important;
  text-align: left !important;
  background: transparent !important;
}

.sched-td:has([data-selected]) {
  background: transparent !important;
}

/* ── Day Cell (CalendarCellTrigger) ── */
.sched-cell {
  width: 100%;
  height: 100%;
  min-height: 68px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  padding: 5px 6px;
  border: 1px solid var(--border);
  border-radius: 4px; /* LESS ROUNDED, MORE PROPORTIONAL */
  background: transparent;
  cursor: pointer;
  text-align: left;
  font-weight: 500;
  transition: all 0.15s ease;
  position: relative;
  gap: 3px;
  box-sizing: border-box;
  outline: none;
  font-family: inherit;
  font-size: inherit;
  color: inherit;
  line-height: 1;
}

.sched-cell:hover:not(.sched-cell--outside) {
  border-color: var(--primary);
  background: color-mix(in oklch, var(--accent) 50%, transparent);
  transform: translateY(-1px);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

/* ── State: Outside Month ── */
.sched-cell--outside {
  opacity: 0.15;
  background: transparent !important;
  border-color: transparent !important;
  cursor: default;
  pointer-events: none;
}

/* ── State: Today ── */
.sched-cell--today {
  border-color: var(--primary);
  font-weight: 700;
  background: color-mix(in oklch, var(--primary) 8%, transparent);
}

/* ── State: Selected ── */
.sched-cell--selected {
  background: color-mix(in oklch, var(--primary) 14%, transparent);
  border-color: var(--primary);
  box-shadow: 0 0 0 1.5px var(--primary);
}

/* ── State: Holiday/Sunday (red tint) ── */
.sched-cell--holiday {
  background: color-mix(in oklch, oklch(0.577 0.245 27) 7%, transparent);
  border-color: color-mix(in oklch, oklch(0.577 0.245 27) 25%, transparent);
}

.sched-cell--holiday .sched-day-num {
  color: oklch(0.577 0.245 27);
}

.dark .sched-cell--holiday {
  background: color-mix(in oklch, oklch(0.577 0.245 27) 12%, transparent);
}

/* ── State: Exam (purple tint) ── */
.sched-cell--exam {
  background: color-mix(in oklch, oklch(0.541 0.281 293) 7%, transparent);
  border-color: color-mix(in oklch, oklch(0.541 0.281 293) 25%, transparent);
}

.dark .sched-cell--exam {
  background: color-mix(in oklch, oklch(0.541 0.281 293) 12%, transparent);
}

/* ── Day Number ── */
.sched-day-num {
  font-size: 0.8rem;
  font-weight: 600;
  line-height: 1;
}

/* ── Micro Badges Container ── */
.sched-badges {
  display: flex;
  flex-direction: column;
  gap: 2px;
  width: 100%;
  margin-top: auto;
}

.sched-micro-badge {
  font-size: 7px !important;
  height: 13px !important;
  padding: 0 4px !important;
  border-radius: 3px !important;
  line-height: 13px !important;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: block;
  width: 100%;
  text-align: center;
  font-weight: 700;
  letter-spacing: 0.04em;
}

/* ══ Responsive: Mobile stack ══ */
@media (max-width: 1023px) {
  .sched-cell {
    min-height: 52px;
    padding: 3px 4px;
  }

  .sched-day-num {
    font-size: 0.7rem;
  }

  .sched-micro-badge {
    font-size: 6px !important;
    height: 11px !important;
    line-height: 11px !important;
  }
}
</style>
