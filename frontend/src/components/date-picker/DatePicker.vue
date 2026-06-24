<script setup>
import { computed, ref } from 'vue'
import { Calendar as CalendarIcon } from 'lucide-vue-next'
import { Calendar } from '@/components/ui/calendar'
import { RangeCalendar } from '@/components/ui/range-calendar'
import { Label } from '@/components/ui/label'
import { useVModel } from '@vueuse/core'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { formatDate } from '@/utils/formatDate'

const props = defineProps({
  modelValue: {
    type: Object,
    default: undefined
  },
  defaultValue: {
    type: Object,
    default: undefined
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Select date'
  },
  layout: {
    type: String,
    default: 'month-and-year'
  },
  id: {
    type: String,
    default: 'date'
  },
  mode: {
    type: String,
    default: 'single' // 'single' | 'range'
  }
})

const emit = defineEmits(['update:modelValue'])

const value = useVModel(props, 'modelValue', emit, {
  passive: true,
  defaultValue: props.defaultValue,
})

// Controlled open state — stays open after date pick, closes only on outside click
const open = ref(false)

// Format display text based on single or range mode
const displayText = computed(() => {
  if (!value.value) return props.placeholder

  if (props.mode === 'range') {
    const start = value.value.start
    const end = value.value.end

    if (start && end) {
      return `${formatDate(start)} - ${formatDate(end)}`
    } else if (start) {
      return formatDate(start)
    }
    return props.placeholder
  }

  // Single mode
  return formatDate(value.value)
})
</script>

<template>
  <div class="flex flex-col gap-3">
    <Label v-if="label" :for="id" class="px-1">
      {{ label }}
    </Label>
    <Popover v-model:open="open">
      <PopoverTrigger as-child>
        <button
          :id="id"
          type="button"
          data-slot="select-trigger"
          class="w-fit h-8 rounded-lg border border-input bg-background px-2.5 text-sm transition-colors flex items-center justify-start gap-2 outline-none text-foreground font-normal focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-3 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <CalendarIcon class="text-muted-foreground size-4 pointer-events-none opacity-80 shrink-0" />
          <span>{{ displayText }}</span>
        </button>
      </PopoverTrigger>
      <PopoverContent class="w-auto overflow-hidden p-0" align="start">
        <!-- Range Mode -->
        <RangeCalendar
          v-if="mode === 'range'"
          :model-value="value"
          :number-of-months="2"
          @update:model-value="(val) => {
            value = val
            // For range: close only when both start and end are set
            if (val?.start && val?.end) {
              // Don't auto-close — let user close manually
            }
          }"
        />
        <!-- Single Mode -->
        <Calendar
          v-else
          :model-value="value"
          :layout="layout"
          @update:model-value="(val) => {
            if (val) {
              value = val
              // Don't auto-close — stay open until user clicks outside
            }
          }"
        />
      </PopoverContent>
    </Popover>
  </div>
</template>
