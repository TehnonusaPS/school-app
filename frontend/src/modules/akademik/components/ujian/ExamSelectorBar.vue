<script setup>
import { computed } from 'vue'
import { Printer } from 'lucide-vue-next'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import FormSelect from '@/components/forms/FormSelect.vue'

const props = defineProps({
  academicYears: { type: Array, required: true },
  examEvents: { type: Array, required: true },
  loadingEvents: { type: Boolean, default: false },
  formatDateIndo: { type: Function, required: true }
})

const emit = defineEmits(['print'])

const selectedAcademicYearId = defineModel('academicYearId')
const selectedEventId = defineModel('eventId')

const academicYearOptions = computed(() => {
  return props.academicYears.map(y => ({
    value: String(y.id),
    label: `Tahun Ajaran ${y.name} ${y.is_active ? '(Aktif)' : ''}`
  }))
})

const examEventOptions = computed(() => {
  return props.examEvents.map(evt => ({
    value: String(evt.id),
    label: `${evt.title} (${props.formatDateIndo(evt.start_date, true)} s.d ${props.formatDateIndo(evt.end_date, true)})`
  }))
})

const selectedAcademicYearStr = computed({
  get: () => (selectedAcademicYearId.value !== null && selectedAcademicYearId.value !== undefined) ? String(selectedAcademicYearId.value) : '',
  set: (val) => {
    selectedAcademicYearId.value = val ? parseInt(val) : null
  }
})

const selectedEventStr = computed({
  get: () => (selectedEventId.value !== null && selectedEventId.value !== undefined) ? String(selectedEventId.value) : '',
  set: (val) => {
    selectedEventId.value = val ? parseInt(val) : null
  }
})
</script>

<template>
  <Card class="border-border/60 shadow-xs">
    <CardContent class="p-5 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Academic Year Selection using FormSelect -->
        <div>
          <FormSelect
            v-model="selectedAcademicYearStr"
            label="1. Pilih Kalender / Tahun Ajaran"
            placeholder="-- Pilih Tahun Ajaran --"
            :options="academicYearOptions"
          />
        </div>

        <!-- Exam Event Selection using FormSelect -->
        <div>
          <FormSelect
            v-model="selectedEventStr"
            label="2. Pilih Jenis / Event Ujian (UTS / UAS)"
            :placeholder="loadingEvents ? 'Memuat event...' : (examEvents.length === 0 ? 'Belum Ada Event Ujian di Kalender Ini' : '-- Pilih Jenis Ujian --')"
            :options="examEventOptions"
          />
        </div>
      </div>

      <!-- Action Row: Button Cetak Jadwal on Bottom-Left -->
      <div v-if="selectedEventId" class="flex items-center justify-start pt-2 border-t border-border/40">
        <Button
          @click="emit('print')"
          variant="outline"
          size="sm"
          class="rounded-xl text-xs font-bold gap-2 border-border/80 hover:bg-accent hover:text-accent-foreground shadow-2xs"
        >
          <Printer class="size-4 text-indigo-500" />
          <span>Cetak Jadwal</span>
        </Button>
      </div>
    </CardContent>
  </Card>
</template>
