<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import {
  Calendar as CalendarIcon,
  Plus,
  CornerUpLeft,
  Save,
  Send,
  Sparkles,
  AlertCircle
} from 'lucide-vue-next'
import PageHeader from '@/components/page-header/PageHeader.vue'
import FormDate from '@/components/forms/FormDate.vue'

import CalendarGrid from '../../../components/calendar/CalendarGrid.vue'
import AgendaSidebar from '../../../components/calendar/AgendaSidebar.vue'
import EventFormModal from '../../../components/calendar/EventFormModal.vue'

import { useCalendarGrid } from '../../../composables/useCalendarGrid'
import { useEventManager } from '../../../composables/useEventManager'

import {
  setupCalendarDates,
  fetchEvents,
  batchStoreEvents,
  fetchCalendarStatus,
  submitCalendar
} from '@/services/academicCalendarService'
import { fetchAllAcademicYears } from '@/services/academicYearService'
import { getClassrooms } from '@/services/managementService'

const route = useRoute()
const router = useRouter()

// Stepper & Setup State
const step = ref(2)
const isLoading = ref(false)

const selectedTahun = ref('')
const currentStatus = ref('draft')
const currentRejectedReason = ref('')

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
  isLoading.value = true
  try {
    const classResponse = await getClassrooms()
    classrooms.value = classResponse.data || []

    const yearResponse = await fetchAllAcademicYears()
    const academicYears = yearResponse.data || []

    const statusResponse = await fetchCalendarStatus()
    const yearStatuses = statusResponse.data || {}

    const param = route.params.tahun
    selectedTahun.value = param ? param.replace('-', '/') : '2025/2026'
    generatedYearName.value = selectedTahun.value

    const statusInfo = yearStatuses[selectedTahun.value] || { status: 'draft' }
    currentStatus.value = statusInfo.status
    currentRejectedReason.value = statusInfo.rejectedReason || ''

    if (currentStatus.value === 'pending') {
      toast.error('Akses Ditolak', { description: 'Kalender tahun ajaran ini sedang dalam pengajuan persetujuan.' })
      router.push('/akademik/admin-sekolah/kalender')
      return
    }

    const yearObj = academicYears.find(ay => ay.name === selectedTahun.value)
    if (yearObj) {
      const response = await fetchEvents(yearObj.id)
      events.value = response.data || []
      if (yearObj.start_date) {
        dateSetup.value.odd_start_date = yearObj.start_date
      }
    }

    const startYear = parseInt(selectedTahun.value.split('/')[0]) || 2026
    const endYear = startYear + 1
    dateSetup.value.odd_start_date = `${startYear}-07-05`
    dateSetup.value.odd_end_date = `${startYear}-12-20`
    dateSetup.value.even_start_date = `${endYear}-01-06`
    dateSetup.value.even_end_date = `${endYear}-06-18`

    generateDynamicMonths(dateSetup.value.odd_start_date, dateSetup.value.even_end_date)
    selectedDateStr.value = `${startYear}-07-05`
  } catch (err) {
    console.error('Error loading calendar data in edit mode:', err)
  } finally {
    isLoading.value = false
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

  toast.success(`Konfigurasi semester disesuaikan untuk Tahun Ajaran ${generatedYearName.value}`)
  step.value = 2
}

function handleSelectCell(cell) {
  if (!cell.isCurrentMonth || !cell.dateStr) return
  selectedDateStr.value = cell.dateStr
  openAddDialog(cell.dateStr)
}

async function handleSaveChanges() {
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
    }

    toast.success('Perubahan Kalender Akademik berhasil disimpan!')
    router.push('/akademik/admin-sekolah/kalender')
  } catch (err) {
    console.error('Error saving calendar changes:', err)
    toast.error('Gagal menyimpan perubahan kalender.')
  } finally {
    isLoading.value = false
  }
}

async function handleSubmitToKepsek() {
  if (!confirm('Simpan dan ajukan Kalender Akademik ini ke Kepala Sekolah?')) return

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

function handleBack() {
  router.push('/akademik/admin-sekolah/kalender')
}
</script>

<template>
  <div class="space-y-6 text-left p-1 pb-12">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <PageHeader
        title="Edit Kalender Akademik"
        description="Kelola jadwal kegiatan tahunan, libur nasional, ujian, dan agenda sekolah."
      />
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <Button variant="outline" class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer flex items-center gap-2 h-10 shadow-xs" @click="handleBack">
          <CornerUpLeft class="h-4 w-4" />
          Batal / Kembali
        </Button>
        <Button v-if="step === 2" variant="default" class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer bg-primary text-primary-foreground flex items-center gap-2 h-10 shadow-xs" :disabled="isLoading" @click="handleSaveChanges">
          <Save class="h-4 w-4" />
          Simpan Perubahan
        </Button>
      </div>
    </div>

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

      <div class="ml-auto hidden sm:flex items-center gap-2">
        <Badge
          :show-dot="true"
          :pulse="true"
          variant="outline"
          class="text-[10px] font-extrabold uppercase px-2.5 py-0.5"
          :class="[
            currentStatus === 'approved' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/30' : (
              currentStatus === 'rejected' ? 'bg-rose-500/10 text-rose-600 border-rose-500/30' : 'bg-amber-500/10 text-amber-600 border-amber-500/30'
            )
          ]"
        >
          {{ currentStatus === 'approved' ? 'Disetujui (Agenda Susulan)' : (currentStatus === 'rejected' ? 'Ditolak / Perlu Perbaikan' : 'Draf Edit') }}
        </Badge>
      </div>
    </div>

    <!-- STEP 1: SETUP TANGGAL SEMESTER -->
    <div v-if="step === 1" class="space-y-6">
      <Card class="border border-border dark:border-zinc-800 shadow-sm text-left">
        <CardHeader class="pb-3 border-b border-border/60 bg-muted/20">
          <CardTitle class="text-sm font-bold text-foreground">Pengaturan Tanggal Semester Ganjil & Genap</CardTitle>
          <CardDescription class="text-xs">Tentukan batas awal dan akhir kegiatan pembelajaran untuk Tahun Ajaran {{ generatedYearName }}</CardDescription>
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
      <Card class="bg-card border border-border dark:border-zinc-800 shadow-xs">
        <CardContent class="p-4 flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-lg border border-primary/20">
              <CalendarIcon class="h-5 w-5" />
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-foreground">Tahun Ajaran {{ generatedYearName }}</h3>
                <Badge :show-dot="true" :pulse="true" variant="outline" class="text-[9px] uppercase font-bold" :class="currentStatus === 'approved' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/30' : 'bg-amber-500/10 text-amber-600 border-amber-500/30'">
                  {{ currentStatus === 'approved' ? 'Disetujui (Tambah Agenda Susulan)' : 'Mode Edit' }}
                </Badge>
              </div>
              <p class="text-xs text-muted-foreground">
                Ganjil: {{ dateSetup.odd_start_date }} s/d {{ dateSetup.odd_end_date }} &bull; Genap: {{ dateSetup.even_start_date }} s/d {{ dateSetup.even_end_date }}
              </p>
              <p v-if="currentStatus === 'rejected' && currentRejectedReason" class="text-xs font-bold text-rose-500 mt-1 flex items-center gap-1">
                <AlertCircle class="h-3.5 w-3.5" />
                Catatan Penolakan Kepala Sekolah: "{{ currentRejectedReason }}"
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <Button variant="outline" size="sm" class="gap-1.5 text-xs font-bold cursor-pointer" @click="openAddDialog()">
              <Plus class="h-4 w-4" />
              + Tambah Agenda Baru
            </Button>
            <Button v-if="currentStatus === 'draft' || currentStatus === 'rejected'" size="sm" class="gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold cursor-pointer" @click="handleSubmitToKepsek">
              <Send class="h-4 w-4" />
              Ajukan ke Kepala Sekolah
            </Button>
          </div>
        </CardContent>
      </Card>

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
