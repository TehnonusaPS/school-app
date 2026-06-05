<script lang="ts" setup>
import type { CalendarRootEmits, CalendarRootProps, DateValue } from 'reka-ui'
import type { HTMLAttributes, Ref } from 'vue'
import type { LayoutTypes } from '.'
import { getLocalTimeZone, today } from '@internationalized/date'
import { createReusableTemplate, reactiveOmit, useVModel } from '@vueuse/core'
import { CalendarRoot, useDateFormatter, useForwardPropsEmits } from 'reka-ui'
import { createYear, createYearRange, toDate } from 'reka-ui/date'
import { computed, toRaw } from 'vue'
import { cn } from '@/lib/utils'
import CalendarSelect from './CalendarSelect.vue'
import { CalendarCell, CalendarCellTrigger, CalendarGrid, CalendarGridBody, CalendarGridHead, CalendarGridRow, CalendarHeadCell, CalendarHeader, CalendarHeading, CalendarNextButton, CalendarPrevButton } from '.'

const props = withDefaults(defineProps<CalendarRootProps & { class?: HTMLAttributes['class'], layout?: LayoutTypes, yearRange?: DateValue[] }>(), {
  modelValue: undefined,
  layout: undefined,
})
const emits = defineEmits<CalendarRootEmits>()

const delegatedProps = reactiveOmit(props, 'class', 'layout', 'placeholder')

const placeholder = useVModel(props, 'placeholder', emits, {
  passive: true,
  defaultValue: props.defaultPlaceholder ?? today(getLocalTimeZone()),
}) as Ref<DateValue>

const formatter = useDateFormatter(props.locale ?? 'id-ID')

const yearRange = computed(() => {
  return props.yearRange ?? createYearRange({
    start: props?.minValue ?? (toRaw(props.placeholder) ?? props.defaultPlaceholder ?? today(getLocalTimeZone()))
      .cycle('year', -100),

    end: props?.maxValue ?? (toRaw(props.placeholder) ?? props.defaultPlaceholder ?? today(getLocalTimeZone()))
      .cycle('year', 10),
  })
})

const [DefineMonthTemplate, ReuseMonthTemplate] = createReusableTemplate<{ date: DateValue }>()
const [DefineYearTemplate, ReuseYearTemplate] = createReusableTemplate<{ date: DateValue }>()

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <DefineMonthTemplate v-slot="{ date }">
    <CalendarSelect
      :model-value="String(date.month)"
      :options="createYear({ dateObj: date }).map(m => ({
        value: String(m.month),
        label: formatter.custom(toDate(m), { month: 'short' })
      }))"
      @update:model-value="(val) => { placeholder = placeholder.set({ month: Number(val) }) }"
    />
  </DefineMonthTemplate>

  <DefineYearTemplate v-slot="{ date }">
    <CalendarSelect
      :model-value="String(date.year)"
      :options="yearRange.map(y => ({
        value: String(y.year),
        label: formatter.custom(toDate(y), { year: 'numeric' })
      }))"
      @update:model-value="(val) => { placeholder = placeholder.set({ year: Number(val) }) }"
    />
  </DefineYearTemplate>


  <CalendarRoot
    v-slot="{ grid, weekDays, date }"
    v-bind="forwarded"
    locale="id-ID"
    :week-starts-on="1"
    v-model:placeholder="placeholder"
    data-slot="calendar"
    :class="cn('p-2 [--cell-radius:var(--radius-md)] [--cell-size:--spacing(7)] group/calendar bg-background in-data-[slot=card-content]:bg-transparent in-data-[slot=popover-content]:bg-transparent', props.class)"
  >
    <CalendarHeader class="!p-0 !justify-between">
      <div class="flex items-center justify-between w-full h-8">
        <!-- Prev button -->
        <CalendarPrevButton>
          <slot name="calendar-prev-icon" />
        </CalendarPrevButton>

        <!-- Month / Year selectors -->
        <slot name="calendar-heading" :date="date" :month="ReuseMonthTemplate" :year="ReuseYearTemplate">
          <template v-if="layout === 'month-and-year'">
            <div class="flex items-center justify-center gap-0.5">
              <ReuseMonthTemplate :date="date" />
              <ReuseYearTemplate :date="date" />
            </div>
          </template>
          <template v-else-if="layout === 'month-only'">
            <div class="flex items-center justify-center gap-1">
              <ReuseMonthTemplate :date="date" />
              {{ formatter.custom(toDate(date), { year: 'numeric' }) }}
            </div>
          </template>
          <template v-else-if="layout === 'year-only'">
            <div class="flex items-center justify-center gap-1">
              {{ formatter.custom(toDate(date), { month: 'short' }) }}
              <ReuseYearTemplate :date="date" />
            </div>
          </template>
          <template v-else>
            <CalendarHeading />
          </template>
        </slot>

        <!-- Next button -->
        <CalendarNextButton>
          <slot name="calendar-next-icon" />
        </CalendarNextButton>
      </div>
    </CalendarHeader>

    <div class="flex flex-col gap-y-4 mt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
      <CalendarGrid v-for="month in grid" :key="month.value.toString()">
        <CalendarGridHead>
          <CalendarGridRow>
            <CalendarHeadCell
              v-for="(day, index) in weekDays" :key="index"
            >
              {{ day }}
            </CalendarHeadCell>
          </CalendarGridRow>
        </CalendarGridHead>
        <CalendarGridBody>
          <CalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`" class="mt-2 w-full">
            <CalendarCell
              v-for="weekDate in weekDates"
              :key="weekDate.toString()"
              :date="weekDate"
            >
              <CalendarCellTrigger
                :day="weekDate"
                :month="month.value"
              />
            </CalendarCell>
          </CalendarGridRow>
        </CalendarGridBody>
      </CalendarGrid>
    </div>
  </CalendarRoot>
</template>
