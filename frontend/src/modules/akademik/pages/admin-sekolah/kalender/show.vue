<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import {
  Calendar as CalendarIcon,
  Edit2,
  CornerUpLeft,
  AlertCircle,
  Send,
  Lock
} from 'lucide-vue-next'
import PageHeader from '@/components/page-header/PageHeader.vue'

import CalendarGrid from '../../../components/calendar/CalendarGrid.vue'
import AgendaSidebar from '../../../components/calendar/AgendaSidebar.vue'

import { useCalendarGrid } from '../../../composables/useCalendarGrid'
import { fetchCalendarStatus, fetchEvents, submitCalendar } from '@/services/academicCalendarService'
import { fetchAllAcademicYears } from '@/services/academicYearService'
import { eventTypes } from '../../../data/calendarConstants'

const route = useRoute()
const router = useRouter()

// State
const events = ref([])
const yearStatuses = ref({})
const academicYears = ref([])
const selectedTahun = ref('')
const activeAcademicYearId = ref(null)
const isLoading = ref(false)
const selectedCategoryFilter = ref('all')

const {
  activeMonthIdx,
  selectedDateStr,
  generateDynamicMonths,
  currentMonthObj,
  calendarDays,
  prevMonth,
  nextMonth
} = useCalendarGrid(events)

const filteredEventsList = computed(() => {
  if (selectedCategoryFilter.value === 'all') return events.value
  const categoryTypes = eventTypes.filter(t => t.category === selectedCategoryFilter.value).map(t => t.value)
  return events.value.filter(e => categoryTypes.includes(e.type))
})

onMounted(async () => {
  isLoading.value = true
  try {
    const yearResponse = await fetchAllAcademicYears()
    academicYears.value = yearResponse.data || []

    const statusResponse = await fetchCalendarStatus()
    yearStatuses.value = statusResponse.data || {}

    const param = route.params.tahun
    selectedTahun.value = param ? param.replace('-', '/') : '2025/2026'

    const yearObj = academicYears.value.find(ay => ay.name === selectedTahun.value)
    if (yearObj) {
      activeAcademicYearId.value = yearObj.id
      const response = await fetchEvents(yearObj.id)
      events.value = response.data || []
    }

    const startYear = parseInt(selectedTahun.value.split('/')[0]) || 2026
    const endYear = startYear + 1
    generateDynamicMonths(`${startYear}-07-05`, `${endYear}-06-20`)
    selectedDateStr.value = `${startYear}-07-05`
  } catch (err) {
    console.error('Error loading calendar in show page:', err)
  } finally {
    isLoading.value = false
  }
})

const currentYearStatus = computed(() => {
  return yearStatuses.value[selectedTahun.value]?.status || 'draft'
})

const currentYearRejectedReason = computed(() => {
  return yearStatuses.value[selectedTahun.value]?.rejectedReason || ''
})

const canEdit = computed(() => {
  return currentYearStatus.value !== 'pending'
})

function handleEdit() {
  router.push(`/akademik/admin-sekolah/kalender/edit/${selectedTahun.value.replace('/', '-')}`)
}

async function handleSubmitToKepsek() {
  if (!confirm(`Apakah Anda yakin ingin mengajukan Kalender Akademik Tahun Pelajaran ${selectedTahun.value} ke Kepala Sekolah?`)) return
  isLoading.value = true
  try {
    if (activeAcademicYearId.value) {
      await submitCalendar(activeAcademicYearId.value)
    }
    toast.success('Pengajuan Berhasil', {
      description: `Kalender Akademik Tahun Pelajaran ${selectedTahun.value} berhasil diajukan ke Kepala Sekolah.`
    })
    router.push('/akademik/admin-sekolah/kalender')
  } catch (err) {
    console.error('Error submitting calendar:', err)
    toast.error('Gagal Mengajukan', { description: 'Terjadi kesalahan saat mengajukan kalender.' })
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
        title="Detail Kalender Akademik"
        description="Lihat rincian agenda kegiatan sekolah per Tahun Pelajaran."
      />
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <Button variant="outline" class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer flex items-center gap-2 h-10 shadow-xs" @click="handleBack">
          <CornerUpLeft class="h-4 w-4" />
          Kembali ke Daftar
        </Button>
        <Button v-if="canEdit" variant="default" class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer bg-primary text-primary-foreground flex items-center gap-2 h-10 shadow-xs" @click="handleEdit">
          <Edit2 class="h-4 w-4" />
          {{ currentYearStatus === 'approved' ? 'Kelola Agenda Susulan' : 'Edit Kalender' }}
        </Button>
        <Button v-if="currentYearStatus === 'draft' || currentYearStatus === 'rejected'" variant="default" class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-2 h-10 shadow-xs" :disabled="isLoading" @click="handleSubmitToKepsek">
          <Send class="h-4 w-4" />
          Ajukan ke Kepsek
        </Button>
      </div>
    </div>

    <!-- Header Controls & Banner -->
    <Card class="bg-card border border-border dark:border-zinc-800 shadow-xs">
      <CardContent class="p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-lg border border-primary/20">
            <CalendarIcon class="h-5 w-5" />
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h3 class="text-sm font-bold text-foreground">Tahun Ajaran {{ selectedTahun }}</h3>
              <Badge
                :show-dot="true"
                :pulse="true"
                variant="outline"
                class="text-[9px] uppercase font-bold"
                :class="[
                  currentYearStatus === 'approved' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/30' : (
                    currentYearStatus === 'pending' ? 'bg-orange-500/10 text-orange-600 border-orange-500/30' : (
                      currentYearStatus === 'rejected' ? 'bg-rose-500/10 text-rose-600 border-rose-500/30' : 'bg-muted text-muted-foreground'
                    )
                  )
                ]"
              >
                {{ currentYearStatus === 'approved' ? 'Disetujui' : (currentYearStatus === 'pending' ? 'Menunggu Persetujuan Kepsek' : (currentYearStatus === 'rejected' ? 'Ditolak / Perlu Perbaikan' : 'Draf')) }}
              </Badge>
            </div>
            <p v-if="currentYearStatus === 'rejected' && currentYearRejectedReason" class="text-xs font-bold text-rose-500 mt-1 flex items-center gap-1">
              <AlertCircle class="h-3.5 w-3.5" />
              Catatan Penolakan Kepala Sekolah: "{{ currentYearRejectedReason }}"
            </p>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Main Container Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6 text-left items-stretch">
      <CalendarGrid
        :calendar-days="calendarDays"
        :selected-date-str="selectedDateStr"
        :current-month-label="currentMonthObj.label"
        :active-month-idx="activeMonthIdx"
        :max-month-idx="13"
        @select-cell="selectedDateStr = $event.dateStr"
        @prev-month="prevMonth"
        @next-month="nextMonth"
      />

      <AgendaSidebar
        :events="events"
        :filtered-events="filteredEventsList"
        :selected-filter="selectedCategoryFilter"
        :readonly="true"
        @filter-change="selectedCategoryFilter = $event"
      />
    </div>
  </div>
</template>
