<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import {
  Calendar as CalendarIcon,
  CalendarDays,
  ArrowRight,
  Check,
  X,
  MessageSquare,
  CornerUpLeft,
  AlertCircle
} from 'lucide-vue-next'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import PageHeader from '@/components/page-header/PageHeader.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'

import CalendarGrid from '../../../components/calendar/CalendarGrid.vue'
import { useCalendarGrid } from '../../../composables/useCalendarGrid'

import {
  fetchCalendarStatus,
  fetchEvents,
  approveCalendar,
  rejectCalendar
} from '@/services/academicCalendarService'
import { fetchAllAcademicYears } from '@/services/academicYearService'
import { eventTypes, getEventBadgeStyle, getEventTypeInfo } from '../../../data/calendarConstants'

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

// Catatan Per-Agenda Kepsek
const agendaNotes = ref({})
const expandedNoteId = ref(null)

// Modals
const isApproveConfirmOpen = ref(false)
const isRejectDialogOpen = ref(false)
const overallRejectReason = ref('')
const rejectError = ref('')

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
    console.error('Error loading calendar in kepsek show page:', err)
  } finally {
    isLoading.value = false
  }
})

const currentYearStatus = computed(() => {
  return yearStatuses.value[selectedTahun.value]?.status || 'pending'
})

function getEventTypeLabel(type) {
  return getEventTypeInfo(type).label
}

function toggleNote(eventId) {
  expandedNoteId.value = expandedNoteId.value === eventId ? null : eventId
}

const countEventsWithNotes = computed(() => {
  return Object.values(agendaNotes.value).filter(n => n && n.trim().length > 0).length
})

function openApproveConfirm() {
  isApproveConfirmOpen.value = true
}

async function handleConfirmApprove() {
  isApproveConfirmOpen.value = false
  isLoading.value = true
  try {
    if (activeAcademicYearId.value) {
      await approveCalendar(activeAcademicYearId.value)
    }
    toast.success('Kalender Disetujui', {
      description: `Kalender Akademik Tahun Pelajaran ${selectedTahun.value} berhasil disetujui dan dikunci.`
    })
    router.push('/akademik/kepala-sekolah/kalender')
  } catch (err) {
    console.error('Error approving calendar:', err)
    toast.error('Gagal Menyetujui', { description: 'Terjadi kesalahan saat menyetujui kalender.' })
  } finally {
    isLoading.value = false
  }
}

function openRejectDialog() {
  rejectError.value = ''
  isRejectDialogOpen.value = true
}

async function handleConfirmReject() {
  if (!overallRejectReason.value.trim() && countEventsWithNotes.value === 0) {
    rejectError.value = 'Harap isi catatan alasan penolakan secara keseluruhan atau pada agenda spesifik.'
    return
  }

  isRejectDialogOpen.value = false
  isLoading.value = true
  try {
    let finalNote = overallRejectReason.value.trim()
    if (countEventsWithNotes.value > 0) {
      const perAgendaSummary = Object.entries(agendaNotes.value)
        .filter(([_, note]) => note && note.trim())
        .map(([id, note]) => {
          const ev = events.value.find(e => String(e.id) === String(id))
          return `- Agenda "${ev?.title || id}": ${note.trim()}`
        })
        .join('\n')

      finalNote += (finalNote ? '\n\n' : '') + '[Catatan Per-Agenda]:\n' + perAgendaSummary
    }

    if (activeAcademicYearId.value) {
      await rejectCalendar(activeAcademicYearId.value, finalNote)
    }

    toast.success('Kalender Ditolak', {
      description: `Kalender Akademik Tahun Pelajaran ${selectedTahun.value} dikembalikan ke Admin Sekolah untuk perbaikan.`
    })
    router.push('/akademik/kepala-sekolah/kalender')
  } catch (err) {
    console.error('Error rejecting calendar:', err)
    toast.error('Gagal Menolak', { description: 'Terjadi kesalahan saat menolak kalender.' })
  } finally {
    isLoading.value = false
  }
}

function handleBack() {
  router.push('/akademik/kepala-sekolah/kalender')
}
</script>

<template>
  <div class="space-y-6 text-left p-1 pb-12">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <PageHeader
        title="Tinjauan Kalender Akademik"
        description="Periksa draf agenda kegiatan sekolah sebelum memberikan persetujuan atau penolakan."
      />
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <Button variant="outline" class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer flex items-center gap-2 h-10 shadow-xs" @click="handleBack">
          <CornerUpLeft class="h-4 w-4" />
          Kembali ke Daftar
        </Button>
        <template v-if="currentYearStatus === 'pending'">
          <Button variant="default" class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-2 h-10 shadow-xs" :disabled="isLoading" @click="openApproveConfirm">
            <Check class="h-4 w-4" />
            Setujui Kalender
          </Button>
          <Button variant="destructive" class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer flex items-center gap-2 h-10 shadow-xs" :disabled="isLoading" @click="openRejectDialog">
            <X class="h-4 w-4" />
            Tolak & Minta Perbaikan
          </Button>
        </template>
      </div>
    </div>

    <!-- Header Banner Status -->
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
                    currentYearStatus === 'pending' ? 'bg-orange-500/10 text-orange-600 border-orange-500/30' : 'bg-rose-500/10 text-rose-600 border-rose-500/30'
                  )
                ]"
              >
                {{ currentYearStatus === 'approved' ? 'Disetujui' : (currentYearStatus === 'pending' ? 'Menunggu Persetujuan Anda' : 'Ditolak') }}
              </Badge>
            </div>
            <p class="text-xs text-muted-foreground mt-0.5">
              Anda dapat memberikan catatan perbaikan pada tiap-tiap agenda di panel sebelah kanan sebelum mengambil keputusan.
            </p>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Main 2-Column Grid Layout -->
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

      <!-- Right Side Panel: Kepsek Interactive Review Sidebar -->
      <Card class="rounded-2xl border border-border dark:border-zinc-800 bg-card shadow-sm text-left flex flex-col h-full max-h-[660px] overflow-hidden">
        <CardHeader class="pb-3 bg-muted/20 dark:bg-zinc-900/40 border-b border-border/60 flex flex-row items-center justify-between shrink-0">
          <div>
            <CardTitle class="text-xs font-bold text-foreground">Daftar Agenda & Catatan Tinjauan</CardTitle>
            <CardDescription class="text-[10px]">Total {{ events.length }} agenda &bull; {{ countEventsWithNotes }} catatan perbaikan</CardDescription>
          </div>
          <Badge :show-dot="true" :pulse="true" variant="outline" class="text-[9px] font-mono uppercase bg-orange-500/10 text-orange-600 border-orange-500/30">
            Review
          </Badge>
        </CardHeader>

        <CardContent class="p-3.5 flex-1 flex flex-col min-h-0 space-y-3 overflow-hidden">
          <!-- Filter Category Chips -->
          <div class="flex items-center gap-1 overflow-x-auto pb-1 shrink-0 no-scrollbar text-[10px]">
            <button
              type="button"
              @click="selectedCategoryFilter = 'all'"
              class="px-2.5 py-1 rounded-lg font-bold border transition-all cursor-pointer whitespace-nowrap"
              :class="selectedCategoryFilter === 'all' ? 'bg-primary text-primary-foreground border-primary' : 'bg-muted/40 text-muted-foreground hover:bg-muted'"
            >
              Semua ({{ events.length }})
            </button>
            <button
              type="button"
              @click="selectedCategoryFilter = 'holiday'"
              class="px-2.5 py-1 rounded-lg font-bold border transition-all cursor-pointer whitespace-nowrap"
              :class="selectedCategoryFilter === 'holiday' ? 'bg-rose-500 text-white border-rose-500' : 'bg-rose-500/10 text-rose-600 border-rose-500/20 hover:bg-rose-500/20'"
            >
              Libur
            </button>
            <button
              type="button"
              @click="selectedCategoryFilter = 'exam'"
              class="px-2.5 py-1 rounded-lg font-bold border transition-all cursor-pointer whitespace-nowrap"
              :class="selectedCategoryFilter === 'exam' ? 'bg-blue-500 text-white border-blue-500' : 'bg-blue-500/10 text-blue-600 border-blue-500/20 hover:bg-blue-500/20'"
            >
              Ujian
            </button>
            <button
              type="button"
              @click="selectedCategoryFilter = 'academic'"
              class="px-2.5 py-1 rounded-lg font-bold border transition-all cursor-pointer whitespace-nowrap"
              :class="selectedCategoryFilter === 'academic' ? 'bg-cyan-500 text-white border-cyan-500' : 'bg-cyan-500/10 text-cyan-600 border-cyan-500/20 hover:bg-cyan-500/20'"
            >
              Akademik
            </button>
            <button
              type="button"
              @click="selectedCategoryFilter = 'activity'"
              class="px-2.5 py-1 rounded-lg font-bold border transition-all cursor-pointer whitespace-nowrap"
              :class="selectedCategoryFilter === 'activity' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20 hover:bg-emerald-500/20'"
            >
              Kegiatan
            </button>
          </div>

          <!-- Agenda List with Per-Agenda Kepsek Note Toggle -->
          <div class="flex-1 overflow-y-auto space-y-2.5 pr-1 no-scrollbar min-h-0 max-h-[540px]">
            <div
              v-for="ev in filteredEventsList"
              :key="ev.id"
              class="p-3 rounded-xl border border-border/80 dark:border-zinc-800 bg-background dark:bg-zinc-950 space-y-2 text-left"
            >
              <div class="flex items-center justify-between gap-2">
                <Badge :show-dot="true" :pulse="true" variant="outline" class="text-[9px] font-bold uppercase gap-1" :class="getEventBadgeStyle(ev.type)">
                  {{ getEventTypeLabel(ev.type) }}
                </Badge>
                <button
                  type="button"
                  @click="toggleNote(ev.id)"
                  class="text-xs font-semibold flex items-center gap-1 transition-colors px-2 py-0.5 rounded-lg border"
                  :class="agendaNotes[ev.id] ? 'bg-amber-500/10 text-amber-600 border-amber-500/30' : 'bg-muted/40 text-muted-foreground border-transparent hover:bg-muted'"
                >
                  <MessageSquare class="h-3 w-3" />
                  <span>{{ agendaNotes[ev.id] ? 'Catatan Ada' : '+ Catatan' }}</span>
                </button>
              </div>

              <h5 class="text-xs font-bold text-foreground leading-snug">
                {{ ev.title }}
              </h5>

              <div class="flex items-center gap-2 text-[10px] font-mono text-muted-foreground">
                <span>{{ ev.startDate }}</span>
                <ArrowRight class="h-3 w-3 opacity-60" />
                <span>{{ ev.endDate }}</span>
              </div>

              <!-- Collapsible Note Field for Kepsek -->
              <div v-if="expandedNoteId === ev.id || agendaNotes[ev.id]" class="pt-2 border-t border-border/40">
                <FormTextArea
                  v-model="agendaNotes[ev.id]"
                  placeholder="Tuliskan saran/catatan khusus perbaikan agenda ini..."
                  :rows="2"
                />
              </div>
            </div>

            <div v-if="filteredEventsList.length === 0" class="py-16 text-center space-y-2">
              <CalendarDays class="h-8 w-8 text-muted-foreground/30 mx-auto" />
              <div class="text-xs font-bold text-muted-foreground">Belum ada agenda terdaftar</div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- MODAL CONFIRM APPROVE -->
    <Dialog :open="isApproveConfirmOpen" @update:open="isApproveConfirmOpen = false">
      <DialogContent class="sm:max-w-md bg-card dark:bg-zinc-900 border border-border">
        <DialogHeader>
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
              <Check class="h-5 w-5" />
            </div>
            <div>
              <DialogTitle class="text-base font-bold text-foreground">Setujui Kalender Akademik</DialogTitle>
              <DialogDescription class="text-xs text-muted-foreground">Tahun Ajaran {{ selectedTahun }}</DialogDescription>
            </div>
          </div>
        </DialogHeader>

        <div class="py-2 text-xs text-foreground space-y-2">
          <p>Apakah Anda yakin ingin menyetujui Kalender Akademik ini?</p>
          <p class="text-muted-foreground">Setelah disetujui, status kalender akan resmi berlaku di sekolah.</p>
        </div>

        <DialogFooter class="gap-2 sm:gap-0">
          <Button variant="outline" size="sm" @click="isApproveConfirmOpen = false">Batal</Button>
          <Button size="sm" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold" :disabled="isLoading" @click="handleConfirmApprove">
            Ya, Setujui Kalender
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- MODAL CONFIRM REJECT -->
    <Dialog :open="isRejectDialogOpen" @update:open="isRejectDialogOpen = false">
      <DialogContent class="sm:max-w-md bg-card dark:bg-zinc-900 border border-border text-left">
        <DialogHeader>
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-rose-500/10 text-rose-600 flex items-center justify-center shrink-0">
              <AlertCircle class="h-5 w-5" />
            </div>
            <div>
              <DialogTitle class="text-base font-bold text-foreground">Tolak & Minta Perbaikan Kalender</DialogTitle>
              <DialogDescription class="text-xs text-muted-foreground">Tahun Ajaran {{ selectedTahun }}</DialogDescription>
            </div>
          </div>
        </DialogHeader>

        <div class="space-y-3 py-2 text-left">
          <p class="text-xs text-foreground">
            Berikan catatan umum alasan penolakan agar Admin Sekolah dapat melakukan perbaikan draf agenda kalender:
          </p>

          <FormTextArea
            v-model="overallRejectReason"
            placeholder="Contoh: Tanggal PTS Semester Ganjil perlu ditinjau ulang..."
            :rows="3"
            :error="rejectError"
          />

          <div v-if="countEventsWithNotes > 0" class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs">
            <span class="font-bold">Terdeteksi {{ countEventsWithNotes }} catatan per-agenda.</span> Catatan per-agenda tersebut akan otomatis dilampirkan dalam laporan penolakan ke Admin Sekolah.
          </div>
        </div>

        <DialogFooter class="gap-2 sm:gap-0">
          <Button variant="outline" size="sm" @click="isRejectDialogOpen = false">Batal</Button>
          <Button size="sm" class="bg-rose-600 hover:bg-rose-700 text-white font-bold" :disabled="isLoading" @click="handleConfirmReject">
            Tolak Kalender
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
