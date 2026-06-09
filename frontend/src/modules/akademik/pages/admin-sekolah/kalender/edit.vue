<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import {
  ChevronLeft,
  ChevronRight,
  Plus,
  Edit2,
  Trash2,
  Lock,
  Unlock,
  AlertCircle,
  Calendar as CalendarIcon,
  ArrowRight,
  CornerUpLeft,
  Save
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

// Import Form fields root
import FormInput from '@/components/forms/FormInput.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'

// Import Mock Data
import {
  academicMonths,
  eventTypes,
  getEvents,
  saveEvents,
  getYearStatuses,
  saveYearStatuses
} from '../../../data/mockKalender'
import { glassFade } from '@/config/motion'

const route = useRoute()
const router = useRouter()

// --- State ---
const events = ref([])
const yearStatuses = ref({})
const selectedTahun = ref('')
const activeMonthIdx = ref(0) // Default to July (index 0)
const selectedDateStr = ref('')
const isLoading = ref(false)

// Dialog Form State
const isDialogOpen = ref(false)
const dialogMode = ref('add') // 'add' | 'edit'
const form = ref({
  id: '',
  startDate: '',
  endDate: '',
  title: '',
  type: 'kegiatan',
  description: ''
})
const formErrors = ref({
  startDate: '',
  endDate: '',
  title: '',
  type: ''
})

onMounted(() => {
  isLoading.value = true
  events.value = getEvents()
  yearStatuses.value = getYearStatuses()
  
  // Parse Tahun Ajaran from param (e.g., "2025-2026" to "2025/2026")
  const param = route.params.tahun
  if (param) {
    selectedTahun.value = param.replace('-', '/')
  } else {
    selectedTahun.value = '2025/2026'
  }
  
  // Set active month to June (index 11) if selected year is 2025/2026 (for demo seeding match)
  if (selectedTahun.value === '2025/2026') {
    activeMonthIdx.value = 11
    selectedDateStr.value = '2026-06-08'
  } else {
    const startYear = parseInt(selectedTahun.value.split('/')[0])
    selectedDateStr.value = `${startYear}-07-01`
  }
  
  // Redirect back if locked
  const status = yearStatuses.value[selectedTahun.value]?.status
  if (status === 'pending' || status === 'approved') {
    toast.error('Akses Ditolak', { description: 'Kalender tahun ajaran ini sedang dikunci.' })
    router.push('/akademik/admin-sekolah/kalender')
  }
  
  isLoading.value = false
})

// --- Computed Date Calculations ---
const currentCalendarYear = computed(() => {
  if (!selectedTahun.value) return 2026
  const startYear = parseInt(selectedTahun.value.split('/')[0])
  const monthInfo = academicMonths[activeMonthIdx.value]
  return startYear + monthInfo.yearOffset
})

const selectedMonthVal = computed(() => {
  return academicMonths[activeMonthIdx.value].val
})

const currentMonthName = computed(() => {
  return academicMonths[activeMonthIdx.value].name
})

const currentSemester = computed(() => {
  return academicMonths[activeMonthIdx.value].semester
})

// Helper to check range overlap
function isDateInBetween(dateStr, startStr, endStr) {
  return dateStr >= startStr && dateStr <= endStr
}

// Convert format helpers
function toDMY(dateStr) {
  if (!dateStr) return ''
  const parts = dateStr.split('-')
  if (parts.length !== 3) return dateStr
  const [y, m, d] = parts
  return `${d}-${m}-${y}`
}

function toYMD(dateStr) {
  if (!dateStr) return ''
  const parts = dateStr.split('-')
  if (parts.length !== 3) return dateStr
  const [d, m, y] = parts
  return `${y}-${m}-${d}`
}

// Calendar Grid Cells
const calendarCells = computed(() => {
  const year = currentCalendarYear.value
  const monthVal = selectedMonthVal.value
  const firstDayOffset = new Date(year, monthVal, 1).getDay()
  const totalDays = new Date(year, monthVal + 1, 0).getDate()
  
  const cells = []
  
  // Padding cells
  for (let i = 0; i < firstDayOffset; i++) {
    cells.push({ isPadding: true })
  }
  
  // Real days
  for (let d = 1; d <= totalDays; d++) {
    const dateStr = `${year}-${String(monthVal + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
    const dateObj = new Date(year, monthVal, d)
    const dayOfWeek = dateObj.getDay()
    const isWeekend = dayOfWeek === 0 || dayOfWeek === 6
    
    // Filter events overlapping this date
    const dayEvents = events.value.filter(e => isDateInBetween(dateStr, e.startDate, e.endDate))
    
    cells.push({
      isPadding: false,
      dateNum: d,
      dateStr,
      isWeekend,
      dayOfWeek,
      events: dayEvents
    })
  }
  
  return cells
})

// Month navigation
const prevMonth = () => {
  if (activeMonthIdx.value > 0) {
    activeMonthIdx.value--
  } else {
    toast.info('Batas Awal Semester', {
      description: 'Sudah mencapai batas awal semester ganjil pada tahun ajaran ini.'
    })
  }
}

const nextMonth = () => {
  if (activeMonthIdx.value < 11) {
    activeMonthIdx.value++
  } else {
    toast.info('Batas Akhir Semester', {
      description: 'Sudah mencapai batas akhir semester genap pada tahun ajaran ini.'
    })
  }
}

// Events filter for side list
const selectedDateEvents = computed(() => {
  return events.value.filter(e => isDateInBetween(selectedDateStr.value, e.startDate, e.endDate))
})

const activeMonthEvents = computed(() => {
  const year = currentCalendarYear.value
  const monthVal = selectedMonthVal.value
  const firstDayStr = `${year}-${String(monthVal + 1).padStart(2, '0')}-01`
  const lastDay = new Date(year, monthVal + 1, 0).getDate()
  const lastDayStr = `${year}-${String(monthVal + 1).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`
  
  return events.value
    .filter(e => {
      return e.startDate <= lastDayStr && e.endDate >= firstDayStr
    })
    .sort((a, b) => a.startDate.localeCompare(b.startDate))
})

// Formatted Date Title
const formattedSelectedDate = computed(() => {
  if (!selectedDateStr.value) return ''
  const dObj = new Date(selectedDateStr.value)
  const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
  return `${dayNames[dObj.getDay()]}, ${dObj.getDate()} ${monthNames[dObj.getMonth()]} ${dObj.getFullYear()}`
})

// --- Helper Labels & Styling ---
function getEventTypeLabel(type) {
  const t = eventTypes.find(opt => opt.value === type)
  return t ? t.label : 'Kegiatan'
}

function getEventTypeBadgeClass(type) {
  if (type === 'libur_nasional' || type === 'tanggal_merah') {
    return 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800'
  }
  if (type === 'ujian') {
    return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800'
  }
  return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'
}

// --- CRUD Actions ---
const handleSelectCell = (cell) => {
  if (cell.isPadding) return
  selectedDateStr.value = cell.dateStr
}

const openAddDialog = () => {
  form.value = {
    id: '',
    startDate: toDMY(selectedDateStr.value),
    endDate: toDMY(selectedDateStr.value),
    title: '',
    type: 'kegiatan',
    description: ''
  }
  formErrors.value = { startDate: '', endDate: '', title: '', type: '' }
  dialogMode.value = 'add'
  isDialogOpen.value = true
}

const openEditDialog = (event) => {
  form.value = {
    id: event.id,
    startDate: toDMY(event.startDate),
    endDate: toDMY(event.endDate),
    title: event.title,
    type: event.type,
    description: event.description || ''
  }
  formErrors.value = { startDate: '', endDate: '', title: '', type: '' }
  dialogMode.value = 'edit'
  isDialogOpen.value = true
}

const validateForm = () => {
  let valid = true
  formErrors.value = { startDate: '', endDate: '', title: '', type: '' }
  
  const dmyRegex = /^\d{2}-\d{2}-\d{4}$/
  
  if (!form.value.startDate) {
    formErrors.value.startDate = 'Tanggal mulai wajib diisi.'
    valid = false
  } else if (!dmyRegex.test(form.value.startDate)) {
    formErrors.value.startDate = 'Format harus DD-MM-YYYY (contoh: 08-06-2026).'
    valid = false
  }
  
  if (!form.value.endDate) {
    formErrors.value.endDate = 'Tanggal selesai wajib diisi.'
    valid = false
  } else if (!dmyRegex.test(form.value.endDate)) {
    formErrors.value.endDate = 'Format harus DD-MM-YYYY (contoh: 12-06-2026).'
    valid = false
  }
  
  if (valid) {
    const startYMD = toYMD(form.value.startDate)
    const endYMD = toYMD(form.value.endDate)
    if (startYMD > endYMD) {
      formErrors.value.endDate = 'Tanggal selesai tidak boleh mendahului tanggal mulai.'
      valid = false
    }
  }
  
  if (!form.value.title.trim()) {
    formErrors.value.title = 'Judul agenda wajib diisi.'
    valid = false
  }
  if (!form.value.type) {
    formErrors.value.type = 'Tipe agenda wajib dipilih.'
    valid = false
  }
  
  return valid
}

const saveForm = () => {
  if (!validateForm()) return
  
  const updatedEvents = [...events.value]
  const startYMD = toYMD(form.value.startDate)
  const endYMD = toYMD(form.value.endDate)
  
  if (dialogMode.value === 'add') {
    const newEvent = {
      id: Date.now().toString(),
      startDate: startYMD,
      endDate: endYMD,
      title: form.value.title,
      type: form.value.type,
      description: form.value.description
    }
    updatedEvents.push(newEvent)
    toast.success('Agenda Ditambahkan', { description: 'Agenda range tanggal berhasil dimasukkan.' })
  } else {
    const idx = updatedEvents.findIndex(e => e.id === form.value.id)
    if (idx !== -1) {
      updatedEvents[idx] = {
        ...updatedEvents[idx],
        startDate: startYMD,
        endDate: endYMD,
        title: form.value.title,
        type: form.value.type,
        description: form.value.description
      }
      toast.success('Agenda Diperbarui', { description: 'Perubahan agenda akademik berhasil disimpan.' })
    }
  }
  
  events.value = updatedEvents
  saveEvents(updatedEvents)
  isDialogOpen.value = false
}

const deleteEvent = (event) => {
  if (confirm(`Apakah Anda yakin ingin menghapus agenda "${event.title}"?`)) {
    const updated = events.value.filter(e => e.id !== event.id)
    events.value = updated
    saveEvents(updated)
    toast.success('Agenda Dihapus', { description: 'Agenda akademik berhasil dihapus.' })
  }
}

const handleBack = () => {
  router.push('/akademik/admin-sekolah/kalender')
}

const handleSaveChanges = () => {
  const updated = { ...yearStatuses.value }
  updated[selectedTahun.value] = { status: 'draft', rejectedReason: '' }
  saveYearStatuses(updated)
  
  toast.success('Perubahan Disimpan', {
    description: `Draf Kalender Akademik Tahun Pelajaran ${selectedTahun.value} berhasil disimpan.`
  })
  router.push('/akademik/admin-sekolah/kalender')
}
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 text-left"
  >
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <PageHeader
        title="Edit Kalender Akademik"
        description="Kelola agenda kegiatan sekolah untuk Tahun Pelajaran terpilih."
      />
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <Button
          variant="outline"
          class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer flex items-center gap-2 h-10 shadow-xs"
          @click="handleBack"
        >
          <CornerUpLeft class="h-4 w-4" />
          Kembali ke Daftar
        </Button>
        <Button
          variant="default"
          class="w-full sm:w-auto text-xs font-bold rounded-xl cursor-pointer bg-primary text-white hover:bg-primary/95 flex items-center gap-2 h-10 shadow-xs"
          @click="handleSaveChanges"
        >
          <Save class="h-4 w-4" />
          Simpan Perubahan
        </Button>
      </div>
    </div>

    <!-- Header Controls: Tahun Ajaran & Bulan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center bg-muted/30 p-4 rounded-2xl border border-border">
      <!-- Tahun Ajaran -->
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0 border border-primary/20">
          <CalendarIcon class="h-5 w-5" />
        </div>
        <div class="flex flex-col gap-1 text-left">
          <span class="text-[9px] uppercase font-bold tracking-wider text-muted-foreground">Tahun Pelajaran (Edit Mode)</span>
          <span class="text-sm font-extrabold text-foreground">Tahun Ajaran {{ selectedTahun }}</span>
        </div>
      </div>

      <!-- Bulan & Navigasi -->
      <div class="flex items-center justify-between md:justify-end gap-4 w-full">
        <div class="flex flex-col text-left md:text-right hidden sm:flex">
          <span class="text-[9px] uppercase font-bold tracking-wider text-muted-foreground">Periode Aktif</span>
          <span class="text-xs font-bold text-foreground">{{ currentMonthName }} {{ currentCalendarYear }} • Semester {{ currentSemester }}</span>
        </div>
        
        <div class="flex items-center gap-2 bg-card border border-border p-1 rounded-xl shadow-xs">
          <Button variant="ghost" size="icon" class="h-8 w-8 rounded-lg cursor-pointer" @click="prevMonth">
            <ChevronLeft class="h-4 w-4" />
          </Button>
          <div class="px-3 text-xs font-bold min-w-[90px] text-center">
            {{ currentMonthName }} {{ currentCalendarYear }}
          </div>
          <Button variant="ghost" size="icon" class="h-8 w-8 rounded-lg cursor-pointer" @click="nextMonth">
            <ChevronRight class="h-4 w-4" />
          </Button>
        </div>
      </div>
    </div>

    <!-- Main Container: Calendar & Side list -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_390px] gap-6">
      
      <!-- 1. CALENDAR BOX -->
      <Card class="rounded-2xl border border-border bg-card shadow-xs overflow-hidden h-fit">
        <div class="overflow-x-auto">
          <!-- Calendar Days Header -->
          <div class="grid grid-cols-7 gap-2 text-center font-bold text-[10px] uppercase tracking-wider text-muted-foreground py-3 border-b border-border/60 bg-muted/20 min-w-[650px]">
            <div class="text-rose-500/80">Minggu</div>
            <div>Senin</div>
            <div>Selasa</div>
            <div>Rabu</div>
            <div>Kamis</div>
            <div>Jumat</div>
            <div class="text-rose-500/80">Sabtu</div>
          </div>
          
          <!-- Calendar Grid Cells -->
          <div class="grid grid-cols-7 gap-3 p-4 min-w-[650px]">
            <div
              v-for="(cell, idx) in calendarCells"
              :key="`admin-cell-${idx}`"
              @click="handleSelectCell(cell)"
              class="relative aspect-square rounded-xl border flex flex-col justify-between p-2 transition-all cursor-pointer"
              :class="[
                cell.isPadding ? 'border-transparent bg-transparent pointer-events-none' : (
                  cell.dateStr === selectedDateStr ? 'bg-primary/5 border-primary shadow-xs' : (
                    cell.isWeekend ? 'bg-muted/40 border-border/30 opacity-80' : 'bg-card border-border/80 hover:shadow-xs hover:border-foreground/20'
                  )
                )
              ]"
            >
              <template v-if="!cell.isPadding">
                <!-- Date Number -->
                <div class="flex justify-between items-center">
                  <span class="text-xs font-bold font-mono" :class="[
                    cell.isWeekend ? 'text-rose-500/80' : 'text-foreground/80',
                    cell.dateStr === selectedDateStr ? 'text-primary' : ''
                  ]">
                    {{ cell.dateNum }}
                  </span>
                </div>

                <!-- Events display inside cell -->
                <div class="flex flex-col gap-1 mt-1 overflow-hidden">
                  <template v-if="cell.events.length > 0">
                    <div
                      v-for="e in cell.events.slice(0, 2)"
                      :key="e.id"
                      class="text-[8px] font-bold px-1 py-0.5 rounded border truncate text-left"
                      :class="getEventTypeBadgeClass(e.type)"
                      :title="e.title"
                    >
                      {{ e.title }}
                    </div>
                    <span v-if="cell.events.length > 2" class="text-[8px] text-muted-foreground font-bold pl-1">
                      +{{ cell.events.length - 2 }} lainnya
                    </span>
                  </template>
                  
                  <!-- Default Weekend Libur Label if no custom event -->
                  <template v-else-if="cell.isWeekend">
                    <span class="text-[9px] font-bold text-rose-500/35 uppercase text-center pb-1">
                      Libur
                    </span>
                  </template>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Legend info footer -->
        <div class="px-6 py-4 border-t border-border bg-muted/10 flex flex-wrap items-center justify-center gap-6 text-[10px] font-bold text-muted-foreground/80">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-rose-500/20 border border-rose-500 text-rose-600 flex items-center justify-center text-[8px]"></span>
            <span>Libur Nasional / Merah</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-amber-500/20 border border-amber-500 text-amber-600 flex items-center justify-center text-[8px]"></span>
            <span>Ujian</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-emerald-500/20 border border-emerald-500 text-emerald-600 flex items-center justify-center text-[8px]"></span>
            <span>Kegiatan Sekolah</span>
          </div>
        </div>
      </Card>

      <!-- 2. SIDE PANEL: AGENDA AT SELECTED DATE -->
      <div class="space-y-6">
        <!-- Selected Date list -->
        <Card class="rounded-2xl border border-border bg-card shadow-xs overflow-hidden text-left">
          <CardHeader class="pb-3 bg-muted/10 border-b">
            <CardDescription class="text-[9px] font-bold uppercase tracking-wider text-primary">Agenda Tanggal Terpilih</CardDescription>
            <CardTitle class="text-sm font-bold mt-1 text-foreground leading-tight">
              {{ formattedSelectedDate }}
            </CardTitle>
          </CardHeader>
          <CardContent class="p-4 space-y-4">
            
            <div v-if="selectedDateEvents.length > 0" class="space-y-3">
              <div
                v-for="event in selectedDateEvents"
                :key="event.id"
                class="p-3.5 rounded-xl border border-border bg-card space-y-2 relative"
              >
                <!-- Title & Type -->
                <div class="flex items-start justify-between gap-2">
                  <div class="space-y-1">
                    <Badge variant="outline" class="text-[8px] uppercase tracking-wider" :class="getEventTypeBadgeClass(event.type)">
                      {{ getEventTypeLabel(event.type) }}
                    </Badge>
                    <h4 class="text-xs font-bold text-foreground leading-snug">{{ event.title }}</h4>
                  </div>
                </div>

                <!-- Range display -->
                <div class="flex items-center gap-1 text-[9px] font-bold text-muted-foreground/80">
                  <span class="font-mono bg-muted border px-1.5 py-0.5 rounded">{{ event.startDate }}</span>
                  <ArrowRight class="h-3 w-3" />
                  <span class="font-mono bg-muted border px-1.5 py-0.5 rounded">{{ event.endDate }}</span>
                </div>

                <!-- Description -->
                <p v-if="event.description" class="text-[10px] text-muted-foreground line-clamp-3 leading-relaxed">
                  {{ event.description }}
                </p>

                <!-- Action Button row inside card -->
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-border/50 mt-1">
                  <Button
                    variant="ghost"
                    size="icon"
                    class="h-7 w-7 text-muted-foreground hover:text-foreground cursor-pointer"
                    @click="openEditDialog(event)"
                  >
                    <Edit2 class="h-3.5 w-3.5" />
                  </Button>
                  <Button
                    variant="ghost"
                    size="icon"
                    class="h-7 w-7 text-rose-500 hover:text-rose-600 hover:bg-rose-50 cursor-pointer"
                    @click="deleteEvent(event)"
                  >
                    <Trash2 class="h-3.5 w-3.5" />
                  </Button>
                </div>
              </div>
            </div>

            <!-- Empty selected date state -->
            <div v-else class="py-6 text-center space-y-2">
              <CalendarIcon class="h-8 w-8 text-muted-foreground/35 mx-auto" />
              <div class="text-[11px] font-bold text-muted-foreground">Tidak ada agenda khusus</div>
              <p class="text-[10px] text-muted-foreground/70">
                Secara default, hari ini terhitung sebagai <span class="font-semibold text-foreground/80">{{ new Date(selectedDateStr).getDay() === 0 || new Date(selectedDateStr).getDay() === 6 ? 'Libur Akhir Pekan' : 'Hari Sekolah Efektif' }}</span>.
              </p>
            </div>

            <!-- Add agenda button for selected date -->
            <Button
              variant="outline"
              class="w-full text-xs font-bold rounded-xl border border-dashed hover:bg-muted/30 cursor-pointer h-9"
              @click="openAddDialog"
            >
              <Plus class="h-3.5 w-3.5 mr-1.5" />
              Blokir Tanggal / Agenda Baru
            </Button>

          </CardContent>
        </Card>

        <!-- Summary list of the current month -->
        <Card class="rounded-2xl border border-border bg-card shadow-xs overflow-hidden text-left">
          <CardHeader class="pb-2 bg-muted/10 border-b">
            <CardTitle class="text-xs font-bold text-foreground">Agenda Bulan {{ currentMonthName }}</CardTitle>
            <CardDescription class="text-[9px]">Daftar seluruh agenda akademik bulan ini.</CardDescription>
          </CardHeader>
          <CardContent class="p-0">
            <div class="max-h-[260px] overflow-y-auto divide-y divide-border/60">
              <div
                v-for="event in activeMonthEvents"
                :key="event.id"
                class="p-3 text-xs hover:bg-muted/20 transition-all flex items-center justify-between gap-3"
              >
                <div class="space-y-0.5 overflow-hidden">
                  <div class="flex items-center gap-1.5">
                    <span class="font-mono text-[9px] font-bold text-muted-foreground shrink-0 bg-muted px-1 rounded border">
                      {{ event.startDate.split('-')[2] }} - {{ event.endDate.split('-')[2] }}
                    </span>
                    <span class="font-bold text-foreground truncate" :title="event.title">{{ event.title }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <Badge variant="outline" class="text-[7px] py-0 px-1 hover:bg-transparent" :class="getEventTypeBadgeClass(event.type)">
                      {{ getEventTypeLabel(event.type) }}
                    </Badge>
                  </div>
                </div>
              </div>

              <!-- Empty monthly state -->
              <div v-if="activeMonthEvents.length === 0" class="p-6 text-center text-[10px] font-semibold text-muted-foreground">
                Belum ada agenda bulan ini.
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

    </div>

    <!-- ADD/EDIT EVENT DIALOG -->
    <Dialog v-model:open="isDialogOpen">
      <DialogContent class="sm:max-w-[425px] rounded-2xl p-6 text-left">
        <DialogHeader>
          <DialogTitle class="text-sm font-bold text-foreground">
            {{ dialogMode === 'add' ? 'Blokir Tanggal / Agenda Akademik' : 'Ubah Agenda Akademik' }}
          </DialogTitle>
          <DialogDescription class="text-[10px] text-muted-foreground">
            Masukkan rentang tanggal untuk memblokir jadwal akademik sekaligus.
          </DialogDescription>
        </DialogHeader>

        <!-- Form fields using root components/forms/ -->
        <div class="space-y-4 py-3">
          <!-- Start Date -->
          <FormInput
            label="Tanggal Mulai (DD-MM-YYYY)"
            placeholder="Contoh: 08-06-2026"
            v-model="form.startDate"
            :error="formErrors.startDate"
            required
          />

          <!-- End Date -->
          <FormInput
            label="Tanggal Selesai (DD-MM-YYYY)"
            placeholder="Contoh: 12-06-2026"
            v-model="form.endDate"
            :error="formErrors.endDate"
            required
          />

          <!-- Title -->
          <FormInput
            label="Keterangan / Judul Agenda"
            placeholder="Contoh: Penilaian Tengah Semester, Libur Hari Raya"
            v-model="form.title"
            :error="formErrors.title"
            required
          />

          <!-- Type Select -->
          <FormSelect
            label="Kategori / Tipe Agenda"
            placeholder="Pilih Kategori"
            v-model="form.type"
            :options="eventTypes"
            :error="formErrors.type"
            required
          />

          <!-- Description Textarea -->
          <FormTextArea
            label="Detail Keterangan (Opsional)"
            placeholder="Penjelasan rinci mengenai agenda..."
            v-model="form.description"
            :rows="3"
          />
        </div>

        <DialogFooter class="flex flex-row items-center justify-end gap-2 pt-2 border-t">
          <Button
            type="button"
            variant="ghost"
            class="text-xs font-bold rounded-xl cursor-pointer"
            @click="isDialogOpen = false"
          >
            Batal
          </Button>
          <Button
            type="button"
            class="text-xs font-bold rounded-xl cursor-pointer bg-primary text-white hover:bg-primary/90"
            @click="saveForm"
          >
            Simpan Agenda
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

  </div>
</template>

<style scoped>
/* Scroller style */
.max-h-\[260px\]::-webkit-scrollbar {
  width: 4px;
}
.max-h-\[260px\]::-webkit-scrollbar-track {
  background: transparent;
}
.max-h-\[260px\]::-webkit-scrollbar-thumb {
  background: var(--border);
  border-radius: 4px;
}
</style>
