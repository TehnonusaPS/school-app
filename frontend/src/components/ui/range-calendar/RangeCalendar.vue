<script lang="ts" setup>
import type { RangeCalendarRootEmits, RangeCalendarRootProps, DateValue } from 'reka-ui'
import type { HTMLAttributes } from 'vue'
import { getLocalTimeZone, today } from '@internationalized/date'
import { reactiveOmit, useVModel } from '@vueuse/core'
import { RangeCalendarRoot, useDateFormatter } from 'reka-ui'
import { createYear, createYearRange } from 'reka-ui/date'
import { computed, ref, watch } from 'vue'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { cn } from '@/lib/utils'
import CalendarSelect from '@/components/ui/calendar/CalendarSelect.vue'
import {
  RangeCalendarCell,
  RangeCalendarCellTrigger,
  RangeCalendarGrid,
  RangeCalendarGridBody,
  RangeCalendarGridHead,
  RangeCalendarGridRow,
  RangeCalendarHeadCell,
} from '.'

const props = withDefaults(defineProps<RangeCalendarRootProps & { class?: HTMLAttributes['class'], yearRange?: DateValue[] }>(), {
  placeholder: undefined,
})

const emits = defineEmits<RangeCalendarRootEmits>()

// Strip props handled manually so each root doesn't double-process them
const sharedProps = reactiveOmit(props, 'class', 'placeholder', 'defaultPlaceholder', 'numberOfMonths', 'yearRange')

const formatter = useDateFormatter(props.locale ?? 'id-ID')

const todayDate = today(getLocalTimeZone())

const toDate = (date: any): Date => {
  if (date instanceof Date) return date
  return date.toDate(getLocalTimeZone())
}

// Get initial displays from modelValue or fallback to today
const initialLeft = (() => {
  if (props.modelValue && typeof props.modelValue === 'object' && 'start' in props.modelValue && props.modelValue.start) {
    return props.modelValue.start
  }
  return props.defaultPlaceholder ?? todayDate
})()

const initialRight = (() => {
  if (props.modelValue && typeof props.modelValue === 'object' && 'end' in props.modelValue && props.modelValue.end) {
    const endVal = props.modelValue.end
    // Ensure right month is strictly greater than left month
    if (endVal.year > initialLeft.year || (endVal.year === initialLeft.year && endVal.month > initialLeft.month)) {
      return endVal
    }
  }
  return initialLeft.add({ months: 1 })
})()

// ── Independent display month for each card ──────────────────────
const leftDisplay = ref<DateValue>(initialLeft)
const rightDisplay = ref<DateValue>(initialRight)

// Reset display months when modelValue is cleared/reset from outside
watch(() => props.modelValue, (newVal) => {
  if (!newVal || (typeof newVal === 'object' && !('start' in newVal && newVal.start))) {
    leftDisplay.value = props.defaultPlaceholder ?? todayDate
    rightDisplay.value = (props.defaultPlaceholder ?? todayDate).add({ months: 1 })
  }
}, { deep: true })

// ── Shared year range ────────────────────────────────────────────
const yearRange = computed(() =>
  props.yearRange ?? createYearRange({
    start: props?.minValue ?? leftDisplay.value.cycle('year', -100),
    end: props?.maxValue ?? rightDisplay.value.cycle('year', 10),
  })
)

// ── Navigation constraints ───────────────────────────────────────
const canLeftNext = computed(() =>
  toDate(leftDisplay.value.add({ months: 1 })) < toDate(rightDisplay.value)
)
const canRightPrev = computed(() =>
  toDate(rightDisplay.value.subtract({ months: 1 })) > toDate(leftDisplay.value)
)

function leftPrev() { leftDisplay.value = leftDisplay.value.subtract({ months: 1 }) }
function leftNext() { if (canLeftNext.value) leftDisplay.value = leftDisplay.value.add({ months: 1 }) }
function rightPrev() { if (canRightPrev.value) rightDisplay.value = rightDisplay.value.subtract({ months: 1 }) }
function rightNext() { rightDisplay.value = rightDisplay.value.add({ months: 1 }) }

// ── Month/year options — only valid options shown ────────────────
function monthOptions(display: any, isLeft: boolean) {
  return createYear({ dateObj: display })
    .filter(m => {
      // Always include the current display month to avoid UI display bugs (numbers showing instead of names)
      if (m.month === display.month) return true

      const candidate = display.set({ month: m.month })
      return isLeft
        ? toDate(candidate) < toDate(rightDisplay.value)
        : toDate(candidate) > toDate(leftDisplay.value)
    })
    .map(m => ({
      value: String(m.month),
      label: formatter.custom(toDate(m), { month: 'short' }),
    }))
}

const leftYearOptions = computed(() =>
  yearRange.value
    .filter(y => {
      if (y.year === leftDisplay.value.year) return true
      return toDate(leftDisplay.value.set({ year: y.year })) < toDate(rightDisplay.value)
    })
    .map(y => ({ value: String(y.year), label: formatter.custom(toDate(y), { year: 'numeric' }) }))
)

const rightYearOptions = computed(() =>
  yearRange.value
    .filter(y => {
      if (y.year === rightDisplay.value.year) return true
      return toDate(rightDisplay.value.set({ year: y.year })) > toDate(leftDisplay.value)
    })
    .map(y => ({ value: String(y.year), label: formatter.custom(toDate(y), { year: 'numeric' }) }))
)

function updateLeftMonth(val: string) {
  const candidate = leftDisplay.value.set({ month: Number(val) })
  if (toDate(candidate) < toDate(rightDisplay.value)) leftDisplay.value = candidate
}
function updateLeftYear(val: string) {
  const candidate = leftDisplay.value.set({ year: Number(val) })
  if (toDate(candidate) < toDate(rightDisplay.value)) leftDisplay.value = candidate
}
function updateRightMonth(val: string) {
  const candidate = rightDisplay.value.set({ month: Number(val) })
  if (toDate(candidate) > toDate(leftDisplay.value)) rightDisplay.value = candidate
}
function updateRightYear(val: string) {
  const candidate = rightDisplay.value.set({ year: Number(val) })
  if (toDate(candidate) > toDate(leftDisplay.value)) rightDisplay.value = candidate
}
</script>

<template>
  <div :class="cn('p-3 flex flex-col gap-4 sm:flex-row justify-center items-stretch bg-background', props.class)">

    <!-- ── LEFT CARD ─────────────────────────────────────────────── -->
    <div class="flex flex-col gap-3 p-2 w-fit">
      <!-- Header -->
      <div class="flex items-center justify-between h-8 w-full">
        <button
          type="button"
          class="size-7 hover:bg-muted/50 rounded-lg flex items-center justify-center border border-border/50 shrink-0 transition-colors"
          @click="leftPrev"
        >
          <ChevronLeft class="size-4" />
        </button>

        <div class="flex items-center gap-0.5 justify-center flex-1">
          <CalendarSelect
            :model-value="String(leftDisplay.month)"
            :options="monthOptions(leftDisplay, true)"
            @update:model-value="updateLeftMonth"
          />
          <CalendarSelect
            :model-value="String(leftDisplay.year)"
            :options="leftYearOptions"
            @update:model-value="updateLeftYear"
          />
        </div>

        <!-- Next disabled when left would reach/exceed right -->
        <button
          type="button"
          :disabled="!canLeftNext"
          class="size-7 hover:bg-muted/50 rounded-lg flex items-center justify-center border border-border/50 shrink-0 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
          @click="leftNext"
        >
          <ChevronRight class="size-4" />
        </button>
      </div>

      <!-- Grid (left) -->
      <RangeCalendarRoot
        v-bind="sharedProps"
        :model-value="modelValue as any"
        :placeholder="leftDisplay as any"
        :number-of-months="1"
        :week-starts-on="1"
        locale="id-ID"
        data-slot="range-calendar"
        class="w-full"
        @update:model-value="(val) => emits('update:modelValue', val as any)"
      >
        <template #default="{ grid, weekDays }">
          <RangeCalendarGrid v-for="month in grid" :key="month.value.toString()" class="w-full">
            <RangeCalendarGridHead>
              <RangeCalendarGridRow>
                <RangeCalendarHeadCell
                  v-for="day in weekDays"
                  :key="day"
                  class="text-xs font-semibold text-muted-foreground/80"
                >{{ day }}</RangeCalendarHeadCell>
              </RangeCalendarGridRow>
            </RangeCalendarGridHead>
            <RangeCalendarGridBody>
              <RangeCalendarGridRow
                v-for="(weekDates, idx) in month.rows"
                :key="`L-week-${idx}`"
                class="mt-1 w-full"
              >
                <RangeCalendarCell v-for="d in weekDates" :key="d.toString()" :date="d">
                  <RangeCalendarCellTrigger :day="d" :month="month.value" />
                </RangeCalendarCell>
              </RangeCalendarGridRow>
            </RangeCalendarGridBody>
          </RangeCalendarGrid>
        </template>
      </RangeCalendarRoot>
    </div>

    <!-- ── RIGHT CARD ────────────────────────────────────────────── -->
    <div class="flex flex-col gap-3 p-2 w-fit">
      <!-- Header -->
      <div class="flex items-center justify-between h-8 w-full">
        <!-- Prev disabled when right would reach/precede left -->
        <button
          type="button"
          :disabled="!canRightPrev"
          class="size-7 hover:bg-muted/50 rounded-lg flex items-center justify-center border border-border/50 shrink-0 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
          @click="rightPrev"
        >
          <ChevronLeft class="size-4" />
        </button>

        <div class="flex items-center gap-0.5 justify-center flex-1">
          <CalendarSelect
            :model-value="String(rightDisplay.month)"
            :options="monthOptions(rightDisplay, false)"
            @update:model-value="updateRightMonth"
          />
          <CalendarSelect
            :model-value="String(rightDisplay.year)"
            :options="rightYearOptions"
            @update:model-value="updateRightYear"
          />
        </div>

        <button
          type="button"
          class="size-7 hover:bg-muted/50 rounded-lg flex items-center justify-center border border-border/50 shrink-0 transition-colors"
          @click="rightNext"
        >
          <ChevronRight class="size-4" />
        </button>
      </div>

      <!-- Grid (right) -->
      <RangeCalendarRoot
        v-bind="sharedProps"
        :model-value="modelValue as any"
        :placeholder="rightDisplay as any"
        :number-of-months="1"
        :week-starts-on="1"
        locale="id-ID"
        data-slot="range-calendar"
        class="w-full"
        @update:model-value="(val) => emits('update:modelValue', val as any)"
      >
        <template #default="{ grid, weekDays }">
          <RangeCalendarGrid v-for="month in grid" :key="month.value.toString()" class="w-full">
            <RangeCalendarGridHead>
              <RangeCalendarGridRow>
                <RangeCalendarHeadCell
                  v-for="day in weekDays"
                  :key="day"
                  class="text-xs font-semibold text-muted-foreground/80"
                >{{ day }}</RangeCalendarHeadCell>
              </RangeCalendarGridRow>
            </RangeCalendarGridHead>
            <RangeCalendarGridBody>
              <RangeCalendarGridRow
                v-for="(weekDates, idx) in month.rows"
                :key="`R-week-${idx}`"
                class="mt-1 w-full"
              >
                <RangeCalendarCell v-for="d in weekDates" :key="d.toString()" :date="d">
                  <RangeCalendarCellTrigger :day="d" :month="month.value" />
                </RangeCalendarCell>
              </RangeCalendarGridRow>
            </RangeCalendarGridBody>
          </RangeCalendarGrid>
        </template>
      </RangeCalendarRoot>
    </div>

  </div>
</template>
