<script setup>
import { ref } from 'vue'
import DatePicker from '@/components/date-picker/DatePicker.vue'
import { getLocalTimeZone, today } from '@internationalized/date'
import { formatDate } from '@/utils/formatDate'

const singleDate = ref(today(getLocalTimeZone()))
const dateRange = ref({
  start: today(getLocalTimeZone()),
  end: today(getLocalTimeZone()).add({ days: 7 })
})
</script>

<template>
  <div class="p-4 space-y-4">
    <!-- Single Date Picker -->
    <DatePicker
      v-model="singleDate"
      label="Single Date Selection"
      placeholder="Select a date"
    />
    <div class="text-xs text-muted-foreground">
      Nilai:
      <code class="bg-muted px-1.5 py-0.5 rounded text-xs text-foreground font-semibold">{{
        singleDate ? formatDate(singleDate) : '-'
      }}</code>
    </div>

    <!-- Range Date Picker -->
    <DatePicker
      v-model="dateRange"
      mode="range"
      label="Range Date Selection"
      placeholder="Select date range"
    />
    <div class="text-xs text-muted-foreground">
      Nilai:
      <code class="bg-muted px-1.5 py-0.5 rounded text-xs text-foreground font-semibold">
        {{ dateRange?.start ? formatDate(dateRange.start) : '-' }} -
        {{ dateRange?.end ? formatDate(dateRange.end) : '-' }}
      </code>
    </div>
  </div>
</template>
