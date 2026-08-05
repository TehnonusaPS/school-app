<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Calendar as CalendarIcon, Plus, Send, Sparkles } from 'lucide-vue-next'
import PageHeader from '@/components/page-header/PageHeader.vue'
import FormDate from '@/components/forms/FormDate.vue'

import CalendarGrid from '../../../components/calendar/CalendarGrid.vue'
import AgendaSidebar from '../../../components/calendar/AgendaSidebar.vue'
import EventFormModal from '../../../components/calendar/EventFormModal.vue'

import { useCalendarGrid } from '../../../composables/useCalendarGrid'
import { useEventManager } from '../../../composables/useEventManager'
import { setupCalendarDates, batchStoreEvents, submitCalendar } from '@/services/academicCalendarService'
import { getClassrooms } from '@/services/managementService'

const router = useRouter()

// Stepper & Setup State
const step = ref(1) // 1: Setup Tanggal Semester, 2: Interactive Editor
const isLoading = ref(false)

const dateSetup = ref({
  odd_start_date: '2026-07-05',
  odd_end_date: '2026-12-20',
  even_start_date: '2027-01-06',
  even_end_date: '2027-06-18'
})
const dateErrors = ref({})
const generatedYearName = ref('')

const events = ref([])
const classrooms = ref([])

// Use Composables
const {
  activeMonthIdx,
  selectedDateStr,
  generateDynamicMonths,
  currentMonthObj,
  calendarDays,
  prevMonth,
  nextMonth
} = useCalendarGrid(events)

const {
  isDialogOpen,
  dialogMode,
  form,
  formErrors,
  selectedCategoryFilter,
  filteredEventsList,
  openAddDialog,
  openEditDialog,
  handleSaveEvent,
  handleDeleteEvent
} = useEventManager(events, classrooms)

const classroomOptions = computed(() => {
  const opts = [{ value: 'all', label: 'Seluruh Kelas' }]
  classrooms.value.forEach(c => {
    opts.push({ value: String(c.id), label: `Kelas ${c.name} (${c.room || ''})` })
  })
  return opts
})

onMounted(async () => {
  try {
    const classResponse = await getClassrooms()
    classrooms.value = classResponse.data || []
  } catch (err) {
    console.error('Error fetching classrooms:', err)
  }
})

function handleSetupDates() {
  dateErrors.value = {}
  if (!dateSetup.value.odd_start_date) dateErrors.value.odd_start_date = 'Tanggal awal ganjil wajib diisi'
  if (!dateSetup.value.odd_end_date) dateErrors.value.odd_end_date = 'Tanggal akhir ganjil wajib diisi'
  if (!dateSetup.value.even_start_date) dateErrors.value.even_start_date = 'Tanggal awal genap wajib diisi'
  if (!dateSetup.value.even_end_date) dateErrors.value.even_end_date = 'Tanggal akhir genap wajib diisi'

  if (Object.keys(dateErrors.value).length > 0) return

  const startY = new Date(dateSetup.value.odd_start_date).getFullYear()
  const endY = new Date(dateSetup.value.even_end_date).getFullYear()
  generatedYearName.value = `${startY}/${endY}`

  generateDynamicMonths(dateSetup.value.odd_start_date, dateSetup.value.even_end_date)

  toast.success(`Konfigurasi semester disiapkan untuk Tahun Ajaran ${generatedYearName.value}`)
  step.value = 2
}

function handleSelectCell(cell) {
  if (!cell.isCurrentMonth || !cell.dateStr) return
  selectedDateStr.value = cell.dateStr
  openAddDialog(cell.dateStr)
}

async function handleSubmitToKepsek() {
  if (!confirm('Simpan dan ajukan Kalender Akademik ini ke Kepala Sekolah untuk persetujuan?')) return

  isLoading.value = true
  try {
    const res = await setupCalendarDates(dateSetup.value)
    const oddYearObj = res.data.find(ay => ay.semester === 'odd') || res.data[0]
    const academicYearId = oddYearObj ? oddYearObj.id : null

    if (academicYearId) {
      await batchStoreEvents({
        academic_year_id: academicYearId,
        events: events.value
      })
      await submitCalendar(academicYearId)
    }

    toast.success('Kalender Akademik berhasil diajukan ke Kepala Sekolah!')
    router.push('/akademik/admin-sekolah/kalender')
  } catch (err) {
    console.error('Error submitting calendar:', err)
    toast.error('Gagal menyimpan dan mengajukan kalender.')
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="space-y-6 p-1 pb-12 text-left">
    <PageHeader
      back
      title="Buat Kalender Akademik Baru"
      description="Lengkapi batas semester dan susun agenda kegiatan tahun ajaran baru."
    />

    <!-- Stepper Navigation -->
    <div class="flex items-center gap-3 bg-card border border-border dark:border-zinc-800 p-3 rounded-2xl shadow-xs">
      <button
        type="button"
        @click="step = 1"
        class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
        :class="step === 1 ? 'bg-primary text-primary-foreground shadow-xs' : 'text-muted-foreground hover:bg-muted/50'"
      >
        <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px]" :class="step === 1 ? 'border-primary-foreground' : 'border-muted-foreground'">1</span>
        <span>Setup Tanggal Semester</span>
      </button>

      <span class="text-muted-foreground/40 text-xs">•</span>

      <button
        type="button"
        @click="step === 2 || handleSetupDates()"
        class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
        :class="step === 2 ? 'bg-primary text-primary-foreground shadow-xs' : 'text-muted-foreground hover:bg-muted/50'"
      >
        <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px]" :class="step === 2 ? 'border-primary-foreground' : 'border-muted-foreground'">2</span>
        <span>Editor Kalender & Agenda</span>
      </button>
    </div>

    <!-- STEP 1: SETUP TANGGAL SEMESTER -->
    <div v-if="step === 1" class="space-y-6">
      <Card class="border border-border dark:border-zinc-800 shadow-sm text-left">
        <CardHeader class="pb-3 border-b border-border/60 bg-muted/20">
          <CardTitle class="text-sm font-bold text-foreground">Pengaturan Tanggal Semester Ganjil & Genap</CardTitle>
          <CardDescription class="text-xs">Tentukan batas awal dan akhir kegiatan pembelajaran untuk Tahun Ajaran Baru</CardDescription>
        </CardHeader>
        <CardContent class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Semester Ganjil Box -->
            <div class="p-4 rounded-2xl border border-primary/20 bg-primary/5 space-y-4">
              <div class="flex items-center gap-2 text-primary font-bold text-xs uppercase tracking-wider">
                <Sparkles class="h-4 w-4" />
                Semester Ganjil (Odd Semester)
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <FormDate v-model="dateSetup.odd_start_date" label="Tanggal Awal Pembelajaran" :error="dateErrors.odd_start_date" />
                <FormDate v-model="dateSetup.odd_end_date" label="Tanggal Akhir Pembelajaran" :error="dateErrors.odd_end_date" />
              </div>
            </div>

            <!-- Semester Genap Box -->
            <div class="p-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 space-y-4">
              <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-xs uppercase tracking-wider">
                <Sparkles class="h-4 w-4" />
                Semester Genap (Even Semester)
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <FormDate v-model="dateSetup.even_start_date" label="Tanggal Awal Pembelajaran" :error="dateErrors.even_start_date" />
                <FormDate v-model="dateSetup.even_end_date" label="Tanggal Akhir Pembelajaran" :error="dateErrors.even_end_date" />
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-2">
            <Button type="button" class="bg-primary text-primary-foreground font-bold text-xs rounded-xl h-10 px-6 cursor-pointer" @click="handleSetupDates">
              Lanjutkan ke Editor Kalender &rarr;
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- STEP 2: INTERACTIVE CALENDAR EDITOR -->
    <div v-else class="space-y-6">
      <!-- Setup Summary Banner -->
      <Card class="bg-card border border-border dark:border-zinc-800 shadow-xs">
        <CardContent class="p-4 flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-lg border border-primary/20">
              <CalendarIcon class="h-5 w-5" />
            </div>
            <div>
              <h3 class="text-sm font-bold text-foreground">Tahun Ajaran {{ generatedYearName }}</h3>
              <p class="text-xs text-muted-foreground">
                Ganjil: {{ dateSetup.odd_start_date }} s/d {{ dateSetup.odd_end_date }} &bull; Genap: {{ dateSetup.even_start_date }} s/d {{ dateSetup.even_end_date }}
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <Button variant="outline" size="sm" class="gap-1.5 text-xs font-bold cursor-pointer" @click="openAddDialog()">
              <Plus class="h-4 w-4" />
              + Tambah Agenda Baru
            </Button>
            <Button size="sm" class="gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold cursor-pointer" :disabled="isLoading" @click="handleSubmitToKepsek">
              <Send class="h-4 w-4" />
              Simpan & Ajukan ke Kepala Sekolah
            </Button>
          </div>
        </CardContent>
      </Card>

      <!-- Main Interactive Grid Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6 text-left items-stretch">
        <CalendarGrid
          :calendar-days="calendarDays"
          :selected-date-str="selectedDateStr"
          :current-month-label="currentMonthObj.label"
          :active-month-idx="activeMonthIdx"
          :max-month-idx="13"
          @select-cell="handleSelectCell"
          @prev-month="prevMonth"
          @next-month="nextMonth"
        />

        <AgendaSidebar
          :events="events"
          :filtered-events="filteredEventsList"
          :selected-filter="selectedCategoryFilter"
          @filter-change="selectedCategoryFilter = $event"
          @edit="openEditDialog"
          @delete="handleDeleteEvent"
        />
      </div>
    </div>

    <!-- DIALOG FORM MODAL -->
    <EventFormModal
      v-model:open="isDialogOpen"
      :mode="dialogMode"
      :form="form"
      :form-errors="formErrors"
      :classroom-options="classroomOptions"
      @save="handleSaveEvent"
    />
  </div>
</template>
