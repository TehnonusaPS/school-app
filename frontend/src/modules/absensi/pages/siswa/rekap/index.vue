<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
  Calendar as CalendarIcon,
  ChevronLeft,
  ChevronRight,
  UserCheck,
  Activity,
  Coffee,
  AlertCircle
} from 'lucide-vue-next'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { glassSlide, glassFade } from '@/config/motion'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Card } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { useAuthStore } from '@/stores/authStore'
import { academicMonths, tahunList } from '../../../data/mockAbsensi'
import { toast } from 'vue-sonner'
import { today, getLocalTimeZone, CalendarDate } from '@internationalized/date'

const auth = useAuthStore()

const startDate = ref()
const endDate = ref()
const selectedStatus = ref('semua')
const currentPage = ref(1)
const itemsPerPage = 10

// Student state and history
const selectedTahun = ref('2026/2027')
const selectedKelas = ref('2 D')
const activeMonthIdx = ref(11)
const activeTab = ref('calendar')

const studentHistory = {
  '2026/2027': '2 D',
  '2025/2026': '1 A',
  '2024/2025': '1 A'
}

const isLoading = ref(true)

function formatDateStr(dateObj) {
  if (!dateObj) return ''
  if (dateObj.year) {
    return `${dateObj.year}-${String(dateObj.month).padStart(2, '0')}-${String(dateObj.day).padStart(2, '0')}`
  }
  if (dateObj instanceof Date) {
    return dateObj.toISOString().split('T')[0]
  }
  return String(dateObj)
}

async function loadData() {
  isLoading.value = true
  try {
    // Simulated load
    await new Promise(resolve => setTimeout(resolve, 400))
  } catch (err) {
    console.error(err)
  } finally {
    isLoading.value = false
  }
}

const currentCalendarYear = computed(() => {
  const startYear = parseInt(selectedTahun.value.split('/')[0])
  const monthInfo = academicMonths[activeMonthIdx.value]
  return startYear + monthInfo.yearOffset
})

const selectedMonthVal = computed(() => {
  return academicMonths[activeMonthIdx.value].val
})

const selectedSemester = computed(() => {
  return academicMonths[activeMonthIdx.value].semester
})

const prevMonth = () => {
  if (activeMonthIdx.value > 0) {
    activeMonthIdx.value--
  } else {
    toast.info('Batas Semester', {
      description: 'Sudah mencapai batas awal semester ganjil pada tahun ajaran ini.'
    })
  }
}

const nextMonth = () => {
  if (activeMonthIdx.value < 11) {
    activeMonthIdx.value++
  } else {
    toast.info('Batas Semester', {
      description: 'Sudah mencapai batas akhir semester genap pada tahun ajaran ini.'
    })
  }
}

watch(selectedTahun, (newTahun) => {
  selectedKelas.value = studentHistory[newTahun] || '2 D'
}, { immediate: true })

watch([selectedTahun, activeMonthIdx], () => {
  const year = currentCalendarYear.value
  const monthVal = selectedMonthVal.value
  const lastDay = new Date(year, monthVal + 1, 0).getDate()
  
  try {
    startDate.value = new CalendarDate(year, monthVal + 1, 1)
    endDate.value = new CalendarDate(year, monthVal + 1, lastDay)
  } catch (e) {
    startDate.value = new Date(year, monthVal, 1)
    endDate.value = new Date(year, monthVal, lastDay)
  }
}, { immediate: true })

onMounted(() => {
  try {
    const currentMonth = new Date().getMonth()
    const currentYear = new Date().getFullYear()
    
    let startYear = currentMonth >= 6 ? currentYear : currentYear - 1
    const academicYearStr = `${startYear}/${startYear + 1}`
    
    if (tahunList.includes(academicYearStr)) {
      selectedTahun.value = academicYearStr
    } else {
      selectedTahun.value = '2026/2027'
    }
    
    const monthIdx = academicMonths.findIndex(m => m.val === currentMonth)
    activeMonthIdx.value = monthIdx !== -1 ? monthIdx : 11
  } catch (e) {
    startDate.value = new Date()
    endDate.value = new Date()
  }
  loadData()
})

watch([startDate, endDate], () => {
  loadData()
})

function getAcademicInfo(dateObj) {
  const year = dateObj.getFullYear()
  const monthVal = dateObj.getMonth() // 0-11
  
  let startYear = monthVal >= 6 ? year : year - 1
  const academicYearStr = `${startYear}/${startYear + 1}`
  const monthIdx = academicMonths.findIndex(m => m.val === monthVal)
  
  return {
    tahun: academicYearStr,
    monthIdx: monthIdx !== -1 ? monthIdx : 11
  }
}

function getDeterministicMockStatus(dateStr, studentId) {
  let hash = 0
  const str = dateStr + '-' + studentId
  for (let i = 0; i < str.length; i++) {
    hash = str.charCodeAt(i) + ((hash << 5) - hash)
  }
  const val = Math.abs(hash) % 100
  if (val < 78) return 'H' // 78% Hadir
  if (val < 83) return 'T' // 5% Terlambat
  if (val < 89) return 'S' // 6% Sakit
  if (val < 94) return 'I' // 5% Izin
  return 'A' // 6% Alpha
}

const studentLogs = computed(() => {
  if (!startDate.value || !endDate.value) return []
  
  const startStr = formatDateStr(startDate.value)
  const endStr = formatDateStr(endDate.value)
  
  const start = new Date(startStr)
  const end = new Date(endStr)
  
  const logs = []
  const saved = localStorage.getItem('attendance_map_db')
  const map = saved ? JSON.parse(saved) : {}
  
  const studentId = auth.user?.id || 1
  const kelas = selectedKelas.value
  
  let curr = new Date(start)
  let safetyCounter = 0
  
  while (curr <= end && safetyCounter < 366) {
    safetyCounter++
    const dayOfWeek = curr.getDay()
    const isWeekend = dayOfWeek === 0 || dayOfWeek === 6
    
    if (!isWeekend) {
      const dayNum = curr.getDate()
      const { tahun, monthIdx } = getAcademicInfo(curr)
      const key = `${kelas}_${tahun}_${monthIdx}_${studentId}_${dayNum}`
      
      let statusVal = map[key]
      const dateStr = curr.toISOString().split('T')[0]
      
      if (statusVal === undefined) {
        statusVal = getDeterministicMockStatus(dateStr, studentId)
      }
      
      if (statusVal) {
        let status = 'hadir'
        let jamMasuk = '07:05:10'
        let jamKeluar = '14:00:00'
        
        if (statusVal === 'H') {
          status = 'hadir'
          const min = (dayNum % 12) + 1
          jamMasuk = `07:${String(min).padStart(2, '0')}:24`
        } else if (statusVal === 'T') {
          status = 'terlambat'
          const min = 16 + (dayNum % 10)
          jamMasuk = `07:${String(min).padStart(2, '0')}:05`
        } else if (statusVal === 'S') {
          status = 'sakit'
          jamMasuk = '-'
          jamKeluar = '-'
        } else if (statusVal === 'I') {
          status = 'izin'
          jamMasuk = '-'
          jamKeluar = '-'
        } else if (statusVal === 'A') {
          status = 'alpa'
          jamMasuk = '-'
          jamKeluar = '-'
        }
        
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
        const formattedDate = `${dayNames[dayOfWeek]}, ${dayNum} ${academicMonths[monthIdx].name} ${curr.getFullYear()}`
        
        logs.push({
          id: key,
          tanggal: formattedDate,
          rawDate: new Date(curr),
          dateStr,
          dayOfWeek,
          jamMasuk,
          jamKeluar,
          status
        })
      }
    }
    
    curr.setDate(curr.getDate() + 1)
  }
  
  return logs.sort((a, b) => b.dateStr.localeCompare(a.dateStr))
})

const filteredStudentData = computed(() => {
  return studentLogs.value.filter(item => {
    if (selectedStatus.value === 'semua') return true
    if (selectedStatus.value === 'hadir') {
      return item.status === 'hadir' || item.status === 'terlambat'
    }
    return item.status === selectedStatus.value
  })
})

const totalStudentPages = computed(() => Math.ceil(filteredStudentData.value.length / itemsPerPage))
const paginatedStudentData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredStudentData.value.slice(start, start + itemsPerPage)
})

const studentStats = computed(() => {
  const logs = studentLogs.value
  return {
    hadir: logs.filter(l => l.status === 'hadir' || l.status === 'terlambat').length,
    sakit: logs.filter(l => l.status === 'sakit').length,
    izin: logs.filter(l => l.status === 'izin').length,
    alpa: logs.filter(l => l.status === 'alpa').length
  }
})

const calendarCells = computed(() => {
  const year = currentCalendarYear.value
  const monthVal = selectedMonthVal.value
  const firstDayOffset = new Date(year, monthVal, 1).getDay()
  const totalDays = new Date(year, monthVal + 1, 0).getDate()
  
  const cells = []
  
  for (let i = 0; i < firstDayOffset; i++) {
    cells.push({ isPadding: true })
  }
  
  const logs = studentLogs.value
  for (let d = 1; d <= totalDays; d++) {
    const dateObj = new Date(year, monthVal, d)
    const dayOfWeek = dateObj.getDay()
    const isWeekend = dayOfWeek === 0 || dayOfWeek === 6
    
    const log = logs.find(l => {
      const dObj = l.rawDate
      return dObj && dObj.getDate() === d && dObj.getMonth() === monthVal && dObj.getFullYear() === year
    })
    
    cells.push({
      isPadding: false,
      dateNum: d,
      isWeekend,
      dayOfWeek,
      status: log?.status || null,
      jamMasuk: log?.jamMasuk || '-',
      jamKeluar: log?.jamKeluar || '-',
      tanggal: log?.tanggal || ''
    })
  }
  
  return cells
})

function getStatusLabel(status) {
  if (status === 'hadir') return 'Hadir'
  if (status === 'terlambat') return 'Terlambat'
  if (status === 'izin') return 'Izin'
  if (status === 'sakit') return 'Sakit'
  if (status === 'alpa') return 'Tanpa Keterangan'
  return 'Belum Absen'
}
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6"
  >
    <PageHeader
      title="Rekap Kehadiran Saya"
      description="Lihat riwayat kehadiran bulanan dan analisis kecenderungan harian Anda secara mandiri."
    />

    <!-- Student Profile Card & Filters row -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto] items-center gap-4 bg-muted/30 p-4 rounded-2xl border border-border">
      <!-- Profile info -->
      <div class="flex items-center gap-3 text-left pl-1">
        <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0 border border-primary/20 uppercase">
          {{ auth.user?.name ? auth.user.name.substring(0, 2) : 'SI' }}
        </div>
        <div>
          <div class="font-bold text-foreground text-sm flex items-center gap-2">
            <span>{{ auth.user?.name || 'Ahmad Wibowo' }}</span>
            <Badge variant="outline" class="text-[9px] uppercase tracking-wider font-extrabold bg-primary/5 text-primary border-primary/20">
              Siswa
            </Badge>
            <Badge v-if="selectedTahun === '2026/2027'" variant="default" class="text-[8px] uppercase font-extrabold bg-emerald-500 text-white hover:bg-emerald-600 border-none px-1.5 py-0.5 rounded leading-none">
              Aktif
            </Badge>
            <Badge v-else variant="secondary" class="text-[8px] uppercase font-extrabold bg-muted text-muted-foreground px-1.5 py-0.5 rounded border leading-none">
              Riwayat Kelas
            </Badge>
          </div>
          <div class="text-xs text-muted-foreground font-semibold mt-0.5">
            NIS: <span class="text-foreground/85 font-mono">{{ auth.user?.id ? '005123456' + auth.user.id : '0051234567' }}</span> • Kelas: <span class="text-foreground/85 font-bold">{{ selectedKelas }}</span>
          </div>
        </div>
      </div>

      <!-- Date Range and Status Filters -->
      <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
        <!-- Select Tahun Pelajaran (History) -->
        <div class="flex flex-col gap-1 text-left w-[150px]">
          <span class="text-[9px] uppercase font-bold tracking-wider text-muted-foreground">Tahun Pelajaran</span>
          <Select v-model="selectedTahun">
            <SelectTrigger class="w-full rounded-xl h-10 shadow-xs text-xs cursor-pointer">
              <SelectValue placeholder="Pilih Tahun" />
            </SelectTrigger>
            <SelectContent class="rounded-xl border-border bg-card">
              <SelectItem v-for="t in tahunList" :key="t" :value="t">
                {{ t }} ({{ studentHistory[t] }})
              </SelectItem>
            </SelectContent>
          </Select>
        </div>

        <!-- Slide Bulan (Pager Slider) -->
        <div class="flex flex-col gap-1 text-left w-[185px]">
          <span class="text-[9px] uppercase font-bold tracking-wider text-muted-foreground">Slide Bulan</span>
          <div class="flex items-center bg-card p-0.5 rounded-xl border border-border h-10 shadow-xs w-full">
            <Button
              variant="ghost"
              size="icon"
              class="h-8.5 w-8.5 rounded-lg hover:bg-muted cursor-pointer shrink-0"
              @click="prevMonth"
            >
              <ChevronLeft class="size-4 text-muted-foreground" />
            </Button>
            <div class="px-2 flex-1 text-center font-bold text-xs text-foreground uppercase tracking-wide truncate">
              {{ academicMonths[activeMonthIdx].name }} {{ currentCalendarYear }}
            </div>
            <Button
              variant="ghost"
              size="icon"
              class="h-8.5 w-8.5 rounded-lg hover:bg-muted cursor-pointer shrink-0"
              @click="nextMonth"
            >
              <ChevronRight class="size-4 text-muted-foreground" />
            </Button>
          </div>
        </div>

        <!-- Select Status -->
        <div class="flex flex-col gap-1 text-left w-[135px]">
          <span class="text-[9px] uppercase font-bold tracking-wider text-muted-foreground">Status Presensi</span>
          <Select v-model="selectedStatus">
            <SelectTrigger class="w-full rounded-xl h-10 shadow-xs text-xs cursor-pointer">
              <SelectValue placeholder="Semua Status" />
            </SelectTrigger>
            <SelectContent class="rounded-xl border-border bg-card">
              <SelectItem value="semua">Semua Status</SelectItem>
              <SelectItem value="hadir">Hadir</SelectItem>
              <SelectItem value="sakit">Sakit</SelectItem>
              <SelectItem value="izin">Izin</SelectItem>
              <SelectItem value="alpa">Alpa</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>
    </div>

    <!-- KPI stats row (4 grid cards) -->
    <StatCardGrid cols="4">
      <StatCard
        label="Hadir"
        :value="studentStats.hadir"
        sub="Hari"
        :icon="UserCheck"
        illustration="school_bell"
        variant="emerald"
      />
      <StatCard
        label="Sakit"
        :value="studentStats.sakit"
        sub="Hari"
        :icon="Activity"
        illustration="atom"
        variant="blue"
      />
      <StatCard
        label="Izin"
        :value="studentStats.izin"
        sub="Hari"
        :icon="Coffee"
        illustration="paper_sheet"
        variant="violet"
      />
      <StatCard
        label="Alpa"
        :value="studentStats.alpa"
        sub="Hari"
        :icon="AlertCircle"
        illustration="star"
        variant="amber"
      />
    </StatCardGrid>

    <!-- Full Width Logs & Calendar Tabbed Card -->
    <div class="space-y-4">
      <!-- Tabs Header -->
      <div class="flex items-center justify-between border-b pb-2">
        <div class="flex items-center gap-1 bg-muted/65 p-1 rounded-xl border border-border/50">
          <button
            @click="activeTab = 'calendar'"
            :class="[
              'px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer border',
              activeTab === 'calendar' ? 'bg-card text-foreground shadow-xs border-border/40' : 'text-muted-foreground hover:text-foreground border-transparent'
            ]"
          >
            Tampilan Kalender
          </button>
          <button
            @click="activeTab = 'list'"
            :class="[
              'px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer border',
              activeTab === 'list' ? 'bg-card text-foreground shadow-xs border-border/40' : 'text-muted-foreground hover:text-foreground border-transparent'
            ]"
          >
            Tampilan List
          </button>
        </div>
        <Badge variant="secondary" class="font-semibold text-xs rounded-lg px-2.5 py-0.5">
          {{ activeTab === 'calendar' ? calendarCells.filter(c => !c.isPadding && !c.isWeekend).length + ' Hari Sekolah' : filteredStudentData.length + ' Hari Terdata' }}
        </Badge>
      </div>

      <!-- Main Card Container -->
      <Card class="rounded-2xl border border-border bg-card shadow-xs overflow-hidden">
        
        <!-- 1. CALENDAR VIEW -->
        <div v-show="activeTab === 'calendar'">
          <div class="overflow-x-auto">
            <!-- Calendar Header (Days) -->
            <div class="grid grid-cols-7 gap-2 text-center font-bold text-[10px] uppercase tracking-wider text-muted-foreground py-3 border-b border-border/60 bg-muted/20 min-w-[700px]">
              <div class="text-rose-500/80">Minggu</div>
              <div>Senin</div>
              <div>Selasa</div>
              <div>Rabu</div>
              <div>Kamis</div>
              <div>Jumat</div>
              <div class="text-rose-500/80">Sabtu</div>
            </div>
            
            <!-- Calendar Loading Skeleton -->
            <div v-if="isLoading" class="grid grid-cols-7 gap-3 p-4 min-w-[700px]">
              <div v-for="i in 35" :key="`student-cal-skel-${i}`" class="aspect-square rounded-xl border border-border/40 flex items-center justify-center p-2">
                <Skeleton class="h-8 w-8 rounded-full" />
              </div>
            </div>
            
            <!-- Calendar Real Grid -->
            <div v-else class="grid grid-cols-7 gap-3 p-4 min-w-[700px]">
              <div
                v-for="(cell, idx) in calendarCells"
                :key="`student-cell-${idx}`"
                class="relative aspect-square rounded-xl border flex flex-col justify-between p-2.5 transition-all"
                :class="[
                  cell.isPadding ? 'border-transparent bg-transparent pointer-events-none' : (
                    cell.isWeekend ? 'bg-muted/40 border-border/30 text-muted-foreground/40 opacity-70' : 'bg-card border-border/80 hover:shadow-xs hover:border-foreground/20'
                  )
                ]"
              >
                <template v-if="!cell.isPadding">
                  <!-- Date Number -->
                  <span class="text-xs font-bold font-mono" :class="cell.isWeekend ? 'text-rose-500/80' : 'text-foreground/80'">
                    {{ cell.dateNum }}
                  </span>

                  <!-- Status Display -->
                  <div v-if="!cell.isWeekend" class="flex flex-col items-center justify-center flex-1">
                    <Popover>
                      <PopoverTrigger as-child>
                        <button
                          class="h-8.5 w-8.5 rounded-full flex items-center justify-center text-[10px] font-extrabold shadow-xs transition-all hover:scale-115 active:scale-95 cursor-pointer uppercase border border-black/5"
                          :class="[
                            (cell.status === 'hadir' || cell.status === 'terlambat') ? 'bg-emerald-500 text-white hover:bg-emerald-600 shadow-emerald-500/15' : '',
                            cell.status === 'sakit' ? 'bg-blue-500 text-white hover:bg-blue-600 shadow-blue-500/15' : '',
                            cell.status === 'izin' ? 'bg-indigo-500 text-white hover:bg-indigo-600 shadow-indigo-500/15' : '',
                            cell.status === 'alpa' ? 'bg-red-500 text-white hover:bg-red-600 shadow-red-500/15' : '',
                            !cell.status ? 'bg-muted text-muted-foreground/60' : ''
                          ]"
                        >
                          {{ cell.status ? ((cell.status === 'hadir' || cell.status === 'terlambat') ? 'H' : cell.status.substring(0, 1).toUpperCase()) : '-' }}
                        </button>
                      </PopoverTrigger>
                      <PopoverContent class="w-56 p-3 rounded-xl border border-border shadow-md bg-card text-left z-50">
                        <div class="space-y-2">
                          <div class="font-bold text-[9px] text-muted-foreground uppercase tracking-wider">
                            Detail Kehadiran
                          </div>
                          <div class="text-xs font-bold text-foreground">
                            {{ cell.tanggal || `${cell.dateNum} ${academicMonths[activeMonthIdx].name} ${currentCalendarYear}` }}
                          </div>
                          <div class="flex items-center gap-2 mt-1">
                            <span class="text-[9px] font-extrabold uppercase px-2.5 py-0.5 rounded-full"
                              :class="[
                                cell.status === 'hadir' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : '',
                                cell.status === 'terlambat' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : '',
                                cell.status === 'sakit' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : '',
                                cell.status === 'izin' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : '',
                                cell.status === 'alpa' ? 'bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20' : '',
                                !cell.status ? 'bg-muted text-muted-foreground' : ''
                              ]"
                            >
                              {{ getStatusLabel(cell.status) }}
                            </span>
                          </div>
                          <div class="text-[10px] text-muted-foreground space-y-1 pt-1.5 border-t border-border/60">
                            <div class="flex justify-between">
                              <span>Jam Masuk:</span>
                              <span class="font-mono text-foreground font-bold">{{ cell.jamMasuk }}</span>
                            </div>
                            <div class="flex justify-between">
                              <span>Jam Keluar:</span>
                              <span class="font-mono text-foreground font-bold">{{ cell.jamKeluar }}</span>
                            </div>
                          </div>
                        </div>
                      </PopoverContent>
                    </Popover>
                  </div>

                  <!-- Weekend Display -->
                  <span v-else class="text-[9px] font-bold text-rose-500/35 uppercase self-center pb-2.5">
                    Libur
                  </span>
                </template>
              </div>
            </div>
          </div>

          <!-- Calendar Legend -->
          <div class="px-6 py-4 border-t border-border bg-muted/10 flex flex-wrap items-center justify-center gap-6 text-[10px] font-bold text-muted-foreground/80">
            <div class="flex items-center gap-1.5">
              <span class="w-4.5 h-4.5 rounded-full bg-emerald-500 flex items-center justify-center text-[9px] font-extrabold text-white">H</span>
              <span>Hadir</span>
            </div>
            <div class="flex items-center gap-1.5">
              <span class="w-4.5 h-4.5 rounded-full bg-indigo-500 flex items-center justify-center text-[9px] font-extrabold text-white">I</span>
              <span>Izin</span>
            </div>
            <div class="flex items-center gap-1.5">
              <span class="w-4.5 h-4.5 rounded-full bg-blue-500 flex items-center justify-center text-[9px] font-extrabold text-white">S</span>
              <span>Sakit</span>
            </div>
            <div class="flex items-center gap-1.5">
              <span class="w-4.5 h-4.5 rounded-full bg-red-500 flex items-center justify-center text-[9px] font-extrabold text-white">A</span>
              <span>Alpha</span>
            </div>
          </div>
        </div>

        <!-- 2. LIST VIEW -->
        <div v-show="activeTab === 'list'">
          <div class="overflow-x-auto">
            <Table>
              <TableHeader class="bg-muted/40 border-b border-border/60">
                <TableRow>
                  <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider text-left">Hari &amp; Tanggal</TableHead>
                  <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider text-center w-[150px]">Jam Masuk</TableHead>
                  <TableHead class="font-bold text-foreground py-4 px-4 text-xs uppercase tracking-wider text-center w-[150px]">Jam Keluar</TableHead>
                  <TableHead class="font-bold text-foreground py-4 px-6 text-xs uppercase tracking-wider text-center w-[150px]">Status</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <template v-if="isLoading">
                  <TableRow v-for="i in 5" :key="`student-skel-${i}`" class="border-b border-border/50">
                    <TableCell class="py-4 px-6"><Skeleton class="h-5 w-40" /></TableCell>
                    <TableCell class="py-4 px-4"><Skeleton class="h-5 w-16 mx-auto" /></TableCell>
                    <TableCell class="py-4 px-4"><Skeleton class="h-5 w-16 mx-auto" /></TableCell>
                    <TableCell class="py-4 px-6"><Skeleton class="h-6 w-20 rounded-full mx-auto" /></TableCell>
                  </TableRow>
                </template>
                <template v-else>
                  <TableRow
                    v-for="log in paginatedStudentData"
                    :key="log.id"
                    class="hover:bg-muted/10 transition-colors border-b border-border/50"
                  >
                    <TableCell class="py-4 px-6 font-bold text-foreground text-sm text-left">
                      {{ log.tanggal }}
                    </TableCell>
                    <TableCell class="py-4 px-4 text-center font-mono text-xs text-muted-foreground">
                      {{ log.jamMasuk }}
                    </TableCell>
                    <TableCell class="py-4 px-4 text-center font-mono text-xs text-muted-foreground">
                      {{ log.jamKeluar }}
                    </TableCell>
                    <TableCell class="py-4 px-6 text-center flex justify-center py-3">
                      <Badge
                        :class="[
                          'rounded-full px-2.5 py-0.5 font-bold uppercase tracking-wider text-[9px]',
                          log.status === 'hadir' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : '',
                          log.status === 'terlambat' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : '',
                          log.status === 'sakit' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : '',
                          log.status === 'izin' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : '',
                          log.status === 'alpa' ? 'bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20' : ''
                        ]"
                        class="mx-auto"
                      >
                        {{ getStatusLabel(log.status) }}
                      </Badge>
                    </TableCell>
                  </TableRow>

                  <TableRow v-if="paginatedStudentData.length === 0">
                    <TableCell colspan="4" class="py-16 text-center text-muted-foreground">
                      <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                        <CalendarIcon class="size-8 text-muted-foreground/60 animate-pulse" />
                        <p class="font-bold text-base text-foreground/80">Tidak Ada Data</p>
                        <p class="text-xs text-muted-foreground/80 leading-relaxed">
                          Tidak ditemukan riwayat kehadiran untuk filter rentang waktu atau status yang dipilih.
                        </p>
                      </div>
                    </TableCell>
                  </TableRow>
                </template>
              </TableBody>
            </Table>
          </div>
        </div>
      </Card>

      <!-- Pagination (Only shown in List View) -->
      <div class="flex items-center justify-between text-sm text-muted-foreground" v-if="filteredStudentData.length > 0 && activeTab === 'list'">
        <span>Menampilkan {{ paginatedStudentData.length }} dari {{ filteredStudentData.length }} data</span>
        <div class="flex items-center gap-1">
          <Button
            variant="outline"
            size="sm"
            :disabled="currentPage === 1"
            @click="currentPage--"
            class="rounded-lg cursor-pointer"
          >
            Prev
          </Button>
          <Button
            v-for="p in totalStudentPages"
            :key="p"
            :variant="p === currentPage ? 'default' : 'outline'"
            size="sm"
            @click="currentPage = p"
            class="rounded-lg cursor-pointer animate-all duration-200"
          >
            {{ p }}
          </Button>
          <Button
            variant="outline"
            size="sm"
            :disabled="currentPage === totalStudentPages"
            @click="currentPage++"
            class="rounded-lg cursor-pointer"
          >
            Next
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>
