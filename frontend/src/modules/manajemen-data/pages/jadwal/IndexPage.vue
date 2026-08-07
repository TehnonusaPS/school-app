<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { toast } from 'vue-sonner'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Card, CardContent } from '@/components/ui/card'
import StatCard from '@/components/stat-card/StatCard.vue'
import { Calendar, GraduationCap, Clock, AlertTriangle, BookOpen, Settings, Send, RotateCcw, FileText, CheckCircle2 } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { glassSlide, glassFade } from '@/config/motion'

import ScheduleGrid from './components/ScheduleGrid.vue'
import ScheduleFormSheet from './components/ScheduleFormSheet.vue'
import UnassignedSubjectsList from './components/UnassignedSubjectsList.vue'
import TimeSlotConfigSheet from './components/TimeSlotConfigSheet.vue'

import { fetchAllAcademicYears } from '@/services/academicYearService'
import { getClassrooms } from '@/services/managementService'
import { getTimeSlots, getSchedules, publishSchedule, unpublishSchedule } from '@/services/scheduleService'

const academicYears = ref([])
const allClassrooms = ref([])
const timeSlots = ref([])
const schedules = ref([])

const selectedTahun = ref('')
const selectedKelas = ref('')
const isLoading = ref(false)
const isPublishing = ref(false)

const isFormOpen = ref(false)
const isEditMode = ref(false)
const selectedSlotData = ref(null)
const editItemData = ref(null)

const isTimeConfigOpen = ref(false)
const unassignedListRef = ref(null)

async function loadData() {
  isLoading.value = true
  try {
    const [ayRes, classRes, slotRes] = await Promise.all([
      fetchAllAcademicYears(),
      getClassrooms(),
      getTimeSlots()
    ])
    
    academicYears.value = ayRes.data || []
    allClassrooms.value = Array.isArray(classRes.data) ? classRes.data : (classRes.data?.data || classRes || [])
    timeSlots.value = slotRes.data || []

    const active = ayRes.data.find(ay => ay.is_active)
    if (active) {
      selectedTahun.value = String(active.id)
    } else if (ayRes.data.length > 0) {
      selectedTahun.value = String(ayRes.data[0].id)
    }
  } catch (err) {
    toast.error('Gagal memuat data awal')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadData()
})

const filteredClassrooms = computed(() => {
  return allClassrooms.value || []
})

watch(filteredClassrooms, (newClasses) => {
  if (newClasses.length > 0) {
    const exists = newClasses.some(c => String(c.id) === String(selectedKelas.value))
    if (!exists) {
      selectedKelas.value = String(newClasses[0].id)
    }
  } else {
    selectedKelas.value = ''
  }
})

async function fetchSchedulesList() {
  if (!selectedTahun.value || !selectedKelas.value) {
    schedules.value = []
    return
  }
  try {
    const res = await getSchedules({
      academic_year_id: selectedTahun.value,
      classroom_id: selectedKelas.value
    })
    schedules.value = res.data || []
  } catch (err) {
    toast.error('Gagal memuat jadwal pelajaran')
  }
}

watch([selectedTahun, selectedKelas], () => {
  fetchSchedulesList()
})

async function refreshTimeSlots() {
  try {
    const res = await getTimeSlots()
    timeSlots.value = res.data
  } catch (err) {
    console.error(err)
  }
}

// Published status computed
const isPublished = computed(() => {
  if (!schedules.value || schedules.value.length === 0) return false
  return schedules.value.some(s => s.status === 'published')
})

// Publish & Unpublish Handlers
async function handlePublish() {
  if (!selectedTahun.value || !selectedKelas.value) return
  isPublishing.value = true
  try {
    const res = await publishSchedule({
      academic_year_id: selectedTahun.value,
      classroom_id: selectedKelas.value
    })
    toast.success('Berhasil Dipublikasikan', { description: res.message })
    await fetchSchedulesList()
  } catch (err) {
    toast.error('Gagal mempublikasikan jadwal')
  } finally {
    isPublishing.value = false
  }
}

async function handleUnpublish() {
  if (!selectedTahun.value || !selectedKelas.value) return
  isPublishing.value = true
  try {
    const res = await unpublishSchedule({
      academic_year_id: selectedTahun.value,
      classroom_id: selectedKelas.value
    })
    toast.success('Ditarik ke Draft', { description: res.message })
    await fetchSchedulesList()
  } catch (err) {
    toast.error('Gagal menarik jadwal ke draft')
  } finally {
    isPublishing.value = false
  }
}

// Statistics computed
const totalMapelTerjadwal = computed(() => {
  return new Set(schedules.value.map(s => s.subject_id)).size
})

const totalJamPerminggu = computed(() => {
  return schedules.value.length
})

function handleCellClicked({ slot, day, existing }) {
  if (!selectedTahun.value || !selectedKelas.value) {
    toast.error('Pilih Tahun Ajaran dan Kelas terlebih dahulu.')
    return
  }
  
  selectedSlotData.value = {
    time_slot_id: slot.id,
    day_of_week: day.value,
    slot_label: `${slot.label || `Jam ${slot.slot_number}`} (${slot.start_time.substring(0, 5)} - ${slot.end_time.substring(0, 5)})`,
    day_name: day.name
  }

  if (existing) {
    isEditMode.value = true
    editItemData.value = existing
  } else {
    isEditMode.value = false
    editItemData.value = null
  }
  isFormOpen.value = true
}

function onSaved() {
  fetchSchedulesList()
  if (unassignedListRef.value) {
    unassignedListRef.value.refresh()
  }
}

// Config actions on header
const headerActions = computed(() => [
  {
    label: 'Atur Jam Pelajaran',
    icon: Settings,
    variant: 'outline',
    onClick: () => { isTimeConfigOpen.value = true }
  }
])
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 w-full mx-auto px-0 text-left"
  >
    <!-- Header -->
    <PageHeader
      title="Manajemen Jadwal Pelajaran"
      description="Kelola jadwal pelajaran mingguan kelas dan pembagian guru pengajar per semester"
      :actions="headerActions"
    />

    <!-- Status Banner (Draft vs Published) -->
    <div
      v-if="schedules.length > 0"
      v-motion
      :initial="glassSlide.initial"
      :visible-once="glassSlide.visible"
      class="p-4 rounded-2xl border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs transition-all shadow-2xs"
      :class="isPublished 
        ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-950 dark:text-emerald-200' 
        : 'bg-amber-500/10 border-amber-500/30 text-amber-950 dark:text-amber-200'"
    >
      <div class="flex items-start gap-3">
        <div 
          class="p-2 rounded-xl shrink-0 font-bold mt-0.5"
          :class="isPublished ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-amber-500/20 text-amber-600 dark:text-amber-400'"
        >
          <component :is="isPublished ? CheckCircle2 : FileText" class="size-4" />
        </div>
        <div>
          <h4 class="font-extrabold flex items-center gap-2 text-sm flex-wrap">
            Status: {{ isPublished ? 'DIPUBLIKASIKAN' : 'DRAFT (Belum Dipublikasikan)' }}
            <span 
              class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider"
              :class="isPublished ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-500/30'"
            >
              {{ isPublished ? 'Aktif dibaca Guru & Siswa' : 'Private Admin Only' }}
            </span>
          </h4>
          <p class="text-[11px] opacity-80 mt-0.5 leading-relaxed">
            {{ isPublished 
              ? 'Jadwal pelajaran kelas ini telah dipublikasikan dan secara resmi dapat dilihat oleh Guru, Siswa, dan Orang Tua.' 
              : 'Jadwal pelajaran ini masih tersimpan sebagai Draft. Anda dapat terus menyusun atau mengubahnya tanpa mempengaruhi akun Guru, Siswa, maupun Orang Tua.' }}
          </p>
        </div>
      </div>

      <Button
        v-if="!isPublished"
        type="button"
        size="sm"
        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shrink-0 px-4 h-9 shadow-sm cursor-pointer border-none"
        :disabled="isPublishing"
        @click="handlePublish"
      >
        <Send class="size-3.5 mr-1.5" />
        Publikasikan Jadwal
      </Button>

      <Button
        v-else
        type="button"
        size="sm"
        variant="outline"
        class="border-emerald-500/40 hover:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 font-bold rounded-xl shrink-0 px-4 h-9 cursor-pointer"
        :disabled="isPublishing"
        @click="handleUnpublish"
      >
        <RotateCcw class="size-3.5 mr-1.5" />
        Tarik ke Draft
      </Button>
    </div>

    <!-- Minimalist Filter Bar -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
      class="flex flex-wrap items-center gap-4 bg-muted/30 p-3 rounded-2xl border border-border/80 text-left"
    >
      <!-- Academic Year Selection -->
      <div class="flex items-center gap-2">
        <Calendar class="size-4 text-muted-foreground shrink-0" />
        <span class="text-xs font-semibold text-muted-foreground select-none">Tahun Ajaran:</span>
        <Select v-model="selectedTahun">
          <SelectTrigger class="h-9 w-[220px] text-xs font-bold rounded-xl bg-background border-border shadow-sm">
            <SelectValue placeholder="Pilih Tahun Ajaran..." />
          </SelectTrigger>
          <SelectContent class="rounded-xl">
            <SelectItem
              v-for="ay in academicYears"
              :key="ay.id"
              :value="String(ay.id)"
            >
              Tahun Ajaran {{ ay.name }} ({{ ay.semester === 'odd' ? 'Ganjil' : 'Genap' }})
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <!-- Vertical divider for desktop -->
      <div class="h-5 w-px bg-border/80 hidden md:block"></div>

      <!-- Classroom Selection -->
      <div class="flex items-center gap-2">
        <GraduationCap class="size-4 text-muted-foreground shrink-0" />
        <span class="text-xs font-semibold text-muted-foreground select-none">Kelas:</span>
        <Select v-model="selectedKelas">
          <SelectTrigger class="h-9 w-[180px] text-xs font-bold rounded-xl bg-background border-border shadow-sm">
            <SelectValue placeholder="Pilih Kelas..." />
          </SelectTrigger>
          <SelectContent class="rounded-xl">
            <SelectItem
              v-for="cls in filteredClassrooms"
              :key="cls.id"
              :value="String(cls.id)"
            >
              Kelas {{ cls.name }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>

    <!-- Stats -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }"
      class="grid gap-4 grid-cols-1 sm:grid-cols-3"
    >
      <StatCard
        label="MAPEL DIJADWALKAN"
        :value="String(totalMapelTerjadwal)"
        sub="Mata Pelajaran"
        :icon="BookOpen"
        illustration="globe"
        variant="primary"
      />
      <StatCard
        label="TOTAL JAM PERMINGGU"
        :value="String(totalJamPerminggu)"
        sub="Jam Pelajaran"
        :icon="Clock"
        illustration="school_bell"
        variant="emerald"
      />
      <StatCard
        label="KONFLIK JADWAL GURU"
        value="0"
        sub="Aman / Bebas Konflik"
        :icon="AlertTriangle"
        illustration="abc_board"
        variant="violet"
      />
    </div>

    <!-- Main Grid Workspace -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 300 } }"
      class="grid grid-cols-1 lg:grid-cols-[3fr_1fr] gap-6 flex-1 items-start"
    >
      <!-- Weekly Grid -->
      <ScheduleGrid
        :time-slots="timeSlots"
        :schedules="schedules"
        @cell-clicked="handleCellClicked"
      />

      <!-- Unassigned Side panel -->
      <div class="h-full lg:min-h-[400px]">
        <UnassignedSubjectsList
          ref="unassignedListRef"
          :academic-year-id="selectedTahun"
          :classroom-id="selectedKelas"
        />
      </div>
    </div>

    <!-- Sheets -->
    <ScheduleFormSheet
      v-model:open="isFormOpen"
      :is-edit-mode="isEditMode"
      :academic-year-id="selectedTahun"
      :classroom-id="selectedKelas"
      :selected-slot="selectedSlotData"
      :edit-item="editItemData"
      @saved="onSaved"
      @deleted="onSaved"
    />

    <TimeSlotConfigSheet
      v-model:open="isTimeConfigOpen"
      @saved="refreshTimeSlots"
    />
  </div>
</template>
