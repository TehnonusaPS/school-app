<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import {
  Camera,
  Database,
  Download,
  Printer,
  Info,
  MoreVertical,
  RefreshCw,
  UserCheck,
  Clock,
  CheckCircle2,
  AlertCircle,
  XCircle,
  UserX,
  HelpCircle,
  ShieldCheck,
  Activity,
  ListFilter,
  Users,
  CheckCircle,
  CalendarDays,
} from 'lucide-vue-next'
import {
  getStudents,
  getLogs,
  updateStudentStatus,
  registerStudentFace,
} from '@/services/api/absensi'
import { Button } from '@/components/ui/button'
import { toast } from 'vue-sonner'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
} from '@/components/ui/dialog'
import { useAuthStore } from '@/stores/authStore'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { usePagination } from '@/composables/usePagination'
import { Badge } from '@/components/ui/badge'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { glassFade } from '@/config/motion'
import { kelasList } from '../../../data/mockAbsensi'

const router = useRouter()
const auth = useAuthStore()

// ─── Tab State ───────────────────────────────────────────
const activeTab = ref('rekap') // 'rekap' | 'logs'

// ─── Filter State ────────────────────────────────────────
const filterValues = ref({
  search: '',
  kelas: 'semua',
  status: 'semua',
})

const statusOptions = [
  { label: 'Semua Status', value: 'semua' },
  { label: 'Hadir', value: 'hadir' },
  { label: 'Terlambat', value: 'terlambat' },
  { label: 'Izin', value: 'izin' },
  { label: 'Sakit', value: 'sakit' },
  { label: 'Alpa (Tanpa Keterangan)', value: 'alpa' },
  { label: 'Belum Absen', value: 'belum_absen' },
]

const filters = computed(() => {
  return [
    { type: 'search', key: 'search', placeholder: 'Cari Nama atau NISN...' },
    {
      type: 'select',
      key: 'kelas',
      label: 'Kelas:',
      placeholder: 'Semua Kelas',
      options: [
        { label: 'Semua Kelas', value: 'semua' },
        ...kelasList.map((k) => ({ label: k, value: k })),
      ],
    },
    {
      type: 'select',
      key: 'status',
      label: 'Status:',
      placeholder: 'Semua Status',
      options: statusOptions,
    },
  ]
})

// ─── Table Columns ───────────────────────────────────────
const columns = computed(() => [
  { key: 'nama', label: 'Nama Siswa & Kelas' },
  { key: 'is_face_registered', label: 'Biometrik Wajah' },
  { key: 'jamMasuk', label: 'Jam Masuk', type: 'code' },
  { key: 'jamKeluar', label: 'Jam Keluar', type: 'code' },
  { key: 'status', label: 'Status Presensi' },
  { key: 'actions', label: 'Aksi' },
])

const logColumns = computed(() => [
  { key: 'waktu', label: 'Waktu Scan', type: 'code' },
  { key: 'nama', label: 'Siswa & Kelas' },
  { key: 'nisn', label: 'NISN' },
  { key: 'tipe', label: 'Tipe Absen' },
])

// ─── Data & Loading State ────────────────────────────────
const absensiData = ref([])
const logsData = ref([])
const isLoading = ref(true)
const isError = ref(false)
const errorMessage = ref('')
let pollInterval = null

async function loadData(isPolling = false) {
  if (!isPolling) {
    isLoading.value = true
    isError.value = false
  }
  try {
    const [students, logs] = await Promise.all([
      getStudents(),
      getLogs(),
    ])
    absensiData.value = students || []
    logsData.value = logs || []
    isError.value = false
  } catch (err) {
    if (!isPolling) {
      isError.value = true
      errorMessage.value = err.message || 'Terjadi kesalahan sistem saat memuat data'
    }
  } finally {
    if (!isPolling) isLoading.value = false
  }
}

onMounted(() => {
  loadData()
  // Background polling every 5s for real-time synchronization with Scanner Kiosk
  pollInterval = setInterval(() => loadData(true), 5000)
})

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval)
})

// ─── Filtered Data & Pagination ──────────────────────────
const filteredData = computed(() => {
  return absensiData.value.filter((item) => {
    const matchKelas =
      filterValues.value.kelas === 'semua' ||
      item.kelas === filterValues.value.kelas
    const matchStatus =
      filterValues.value.status === 'semua' ||
      item.status === filterValues.value.status
    const searchLower = filterValues.value.search.toLowerCase()
    const matchSearch =
      !filterValues.value.search ||
      item.nama.toLowerCase().includes(searchLower) ||
      (item.nisn && item.nisn.toLowerCase().includes(searchLower))

    return matchKelas && matchStatus && matchSearch
  })
})

const filteredLogs = computed(() => {
  if (!filterValues.value.search) return logsData.value
  const searchLower = filterValues.value.search.toLowerCase()
  return logsData.value.filter(
    (log) =>
      log.nama.toLowerCase().includes(searchLower) ||
      (log.nisn && log.nisn.toLowerCase().includes(searchLower)) ||
      (log.kelas && log.kelas.toLowerCase().includes(searchLower))
  )
})

// Stats Computation
const totalSiswa = computed(() => absensiData.value.length)
const sudahHadir = computed(() => absensiData.value.filter((d) => d.status === 'hadir').length)
const terlambat = computed(() => absensiData.value.filter((d) => d.status === 'terlambat').length)
const izinSakitAlpa = computed(() => absensiData.value.filter((d) => ['izin', 'sakit', 'alpa'].includes(d.status)).length)
const belumScan = computed(() => absensiData.value.filter((d) => d.status === 'belum_absen').length)

const perPage = ref(10)
const { currentPage, total, from, to, paginatedItems: paginatedData } = usePagination(filteredData, perPage)

watch(filteredData, () => {
  currentPage.value = 1
})

// ─── Action Handlers ─────────────────────────────────────
function openScanTab() {
  const route = router.resolve('/absensi/siswa/scan')
  window.open(route.href, '_blank')
}

function getStatusLabel(status) {
  switch (status) {
    case 'hadir': return 'Hadir'
    case 'terlambat': return 'Terlambat'
    case 'izin': return 'Izin'
    case 'sakit': return 'Sakit'
    case 'alpa': return 'Tanpa Keterangan'
    default: return 'Belum Absen'
  }
}

function getInitials(nama) {
  if (!nama) return 'S'
  const words = nama.trim().split(/\s+/)
  if (words.length === 1) return words[0].substring(0, 2).toUpperCase()
  return (words[0][0] + words[1][0]).toUpperCase()
}

async function changeStatus(studentId, newStatus) {
  try {
    await updateStudentStatus(studentId, newStatus)
    toast.success('Status Berhasil Diperbarui', {
      description: `Status presensi telah diubah menjadi ${getStatusLabel(newStatus)}.`
    })
    await loadData(true)
  } catch (err) {
    toast.error('Gagal Mengubah Status', {
      description: err.message || 'Terjadi kesalahan sistem'
    })
  }
}

// ─── Face Registration Setup ─────────────────────────────
const isRegisterModalOpen = ref(false)
const selectedStudent = ref(null)
const videoRef = ref(null)
const canvasRef = ref(null)
const cameraStatus = ref('idle') // idle | loading | active | error
const isRegistering = ref(false)
let mediaStream = null

async function startCamera() {
  cameraStatus.value = 'loading'
  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
      audio: false
    })
    mediaStream = stream
    cameraStatus.value = 'active'
    await nextTick()
    if (videoRef.value) {
      videoRef.value.srcObject = stream
      videoRef.value.play()
    }
  } catch (err) {
    cameraStatus.value = 'error'
    toast.error('Gagal mengakses kamera. Mohon aktifkan izin kamera.')
  }
}

function stopCamera() {
  if (mediaStream) {
    mediaStream.getTracks().forEach(track => track.stop())
    mediaStream = null
  }
  if (videoRef.value) videoRef.value.srcObject = null
  cameraStatus.value = 'idle'
}

function openFaceRegistration(student) {
  selectedStudent.value = student
  isRegisterModalOpen.value = true
  startCamera()
}

function closeFaceRegistration() {
  stopCamera()
  isRegisterModalOpen.value = false
  selectedStudent.value = null
}

async function captureAndRegister() {
  if (!videoRef.value || !canvasRef.value || !selectedStudent.value) return
  isRegistering.value = true

  try {
    const video = videoRef.value
    const canvas = canvasRef.value
    const context = canvas.getContext('2d')

    const size = Math.min(video.videoWidth, video.videoHeight)
    const sx = (video.videoWidth - size) / 2
    const sy = (video.videoHeight - size) / 2

    canvas.width = 480
    canvas.height = 480
    context.drawImage(video, sx, sy, size, size, 0, 0, 480, 480)

    const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.85))
    const formData = new FormData()
    formData.append('image', blob, 'face_registration.jpg')

    const res = await registerStudentFace(selectedStudent.value.id, formData)
    if (res.success) {
      toast.success('Registrasi Sukses', {
        description: `Wajah ${selectedStudent.value.nama} berhasil didaftarkan secara biometrik.`
      })
      closeFaceRegistration()
      await loadData(true)
    }
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal meregistrasi wajah.'
    toast.error('Registrasi Gagal', { description: msg })
  } finally {
    isRegistering.value = false
  }
}

const tableActions = computed(() => [
  {
    label: 'Refresh Data',
    icon: RefreshCw,
    variant: 'outline',
    click: () => loadData()
  }
])
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 animate-in fade-in duration-300"
  >
    <!-- ── Page Header ── -->
    <PageHeader
      title="Dashboard Presensi Siswa"
      description="Rekam dan pantau rekapitulasi kehadiran siswa secara real-time dari pemindai Kiosk Sekolah."
      :actions="[
        {
          label: 'Buka Kamera Absensi',
          icon: Camera,
          variant: 'default',
          click: openScanTab
        }
      ]"
    />

    <!-- ── Stat Cards ── -->
    <StatCardGrid cols="5">
      <StatCard
        label="Total Siswa"
        :value="isLoading ? '-' : totalSiswa"
        :icon="Users"
        illustration="graduation_cap"
        variant="primary"
      />
      <StatCard
        label="Hadir Tepat Waktu"
        :value="isLoading ? '-' : sudahHadir"
        :icon="CheckCircle"
        illustration="school_bell"
        variant="emerald"
      />
      <StatCard
        label="Terlambat"
        :value="isLoading ? '-' : terlambat"
        :icon="Clock"
        illustration="ruler"
        variant="amber"
      />
      <StatCard
        label="Izin / Sakit / Alpa"
        :value="isLoading ? '-' : izinSakitAlpa"
        :icon="AlertCircle"
        illustration="paper_sheet"
        variant="violet"
      />
      <StatCard
        label="Belum Absen"
        :value="isLoading ? '-' : belumScan"
        :icon="HelpCircle"
        illustration="pencil"
        variant="blue"
      />
    </StatCardGrid>

    <!-- ── Custom Glass Navigation Tabs ── -->
    <div class="flex items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-3">
      <div class="flex items-center gap-2 p-1 bg-slate-100 dark:bg-slate-900/60 rounded-xl border border-slate-200/60 dark:border-slate-800">
        <button
          @click="activeTab = 'rekap'"
          class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer"
          :class="activeTab === 'rekap' 
            ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm border border-slate-200/50 dark:border-slate-700' 
            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
        >
          <Users class="size-4" />
          Daftar Presensi Siswa Hari Ini
          <Badge variant="secondary" class="ml-1 text-[10px] px-1.5 py-0.2">
            {{ filteredData.length }}
          </Badge>
        </button>

        <button
          @click="activeTab = 'logs'"
          class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer"
          :class="activeTab === 'logs' 
            ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm border border-slate-200/50 dark:border-slate-700' 
            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
        >
          <Activity class="size-4" />
          Log Pemindai Real-Time
          <Badge variant="secondary" class="ml-1 text-[10px] px-1.5 py-0.2 bg-indigo-500/10 text-indigo-500">
            {{ filteredLogs.length }}
          </Badge>
        </button>
      </div>

      <div class="flex items-center gap-2">
        <span class="text-xs text-slate-500 dark:text-slate-400 hidden md:inline-flex items-center gap-1.5">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
          </span>
          Sync otomatis tiap 5s
        </span>
        <Button variant="ghost" size="sm" class="h-8 gap-1.5 text-xs text-slate-600 dark:text-slate-300" @click="loadData(false)">
          <RefreshCw class="size-3.5" :class="{ 'animate-spin': isLoading }" />
          Refresh
        </Button>
      </div>
    </div>

    <!-- ── TAB 1: REKAP PRESENSI SISWA ── -->
    <div v-if="activeTab === 'rekap'">
      <DataTableCard
        :columns="columns"
        :items="paginatedData"
        :filters="filters"
        :actions="tableActions"
        v-model:filterValues="filterValues"
        v-model:perPage="perPage"
        illustration="school_bell"
        :from="from"
        :to="to"
        :total="total"
        :page="currentPage"
        @update:page="currentPage = $event"
      >
        <template #cell-nama="{ item }">
          <div class="flex items-center gap-3">
            <div class="relative size-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center shrink-0">
              <img v-if="item.foto" :src="item.foto" :alt="item.nama" class="size-full object-cover" />
              <span v-else class="font-bold text-xs text-indigo-600 dark:text-indigo-400">
                {{ getInitials(item.nama) }}
              </span>
            </div>
            <div class="flex flex-col text-left">
              <span class="font-semibold text-sm text-slate-900 dark:text-slate-100 leading-tight">
                {{ item.nama }}
              </span>
              <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                <span>NISN: <code class="font-mono">{{ item.nisn || '-' }}</code></span>
                <span>•</span>
                <span class="font-medium text-slate-700 dark:text-slate-300">{{ item.kelas || '-' }}</span>
              </div>
            </div>
          </div>
        </template>

        <template #cell-is_face_registered="{ item }">
          <Badge
            :variant="item.is_face_registered ? 'default' : 'secondary'"
            :class="item.is_face_registered 
              ? 'bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 dark:bg-emerald-500/20 dark:text-emerald-400 border border-emerald-500/30' 
              : 'bg-slate-500/10 text-slate-500 hover:bg-slate-500/20 dark:bg-slate-500/20 dark:text-slate-400 border border-slate-500/20'"
            class="text-[11px] gap-1 px-2 py-0.5 font-medium"
          >
            <ShieldCheck v-if="item.is_face_registered" class="size-3" />
            {{ item.is_face_registered ? 'Terdaftar (AI)' : 'Belum Terdaftar' }}
          </Badge>
        </template>

        <template #cell-jamMasuk="{ value }">
          <div class="font-mono text-xs font-semibold" :class="value ? 'text-slate-900 dark:text-slate-200' : 'text-slate-400 dark:text-slate-600'">
            {{ value || '--:--' }}
          </div>
        </template>

        <template #cell-jamKeluar="{ value }">
          <div class="font-mono text-xs font-semibold" :class="value ? 'text-slate-900 dark:text-slate-200' : 'text-slate-400 dark:text-slate-600'">
            {{ value || '--:--' }}
          </div>
        </template>

        <template #cell-status="{ value }">
          <Badge
            :class="{
              'bg-emerald-500/15 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 border-emerald-500/30': value === 'hadir',
              'bg-amber-500/15 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 border-amber-500/30': value === 'terlambat',
              'bg-indigo-500/15 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 border-indigo-500/30': value === 'sakit',
              'bg-purple-500/15 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400 border-purple-500/30': value === 'izin',
              'bg-rose-500/15 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 border-rose-500/30': value === 'alpa',
              'bg-slate-500/10 text-slate-500 dark:bg-slate-500/20 dark:text-slate-400 border-slate-500/20': value === 'belum_absen',
            }"
            class="px-2.5 py-1 text-xs font-semibold border"
          >
            {{ getStatusLabel(value) }}
          </Badge>
        </template>

        <template #cell-actions="{ item }">
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="icon" class="h-8 w-8 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800">
                <span class="sr-only">Buka menu</span>
                <MoreVertical class="h-4 w-4 text-slate-600 dark:text-slate-400" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-56">
              <DropdownMenuLabel class="text-xs text-slate-500">Biometrik Wajah</DropdownMenuLabel>
              <DropdownMenuItem class="cursor-pointer gap-2" @click="openFaceRegistration(item)">
                <Camera class="size-4 text-indigo-500" />
                Registrasi Wajah AI
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuLabel class="text-xs text-slate-500">Ubah Status Presensi Hari Ini</DropdownMenuLabel>
              <DropdownMenuItem class="cursor-pointer gap-2 text-emerald-600 dark:text-emerald-400" @click="changeStatus(item.id, 'hadir')">
                <CheckCircle2 class="size-4" />
                Hadir
              </DropdownMenuItem>
              <DropdownMenuItem class="cursor-pointer gap-2 text-amber-600 dark:text-amber-400" @click="changeStatus(item.id, 'terlambat')">
                <Clock class="size-4" />
                Terlambat
              </DropdownMenuItem>
              <DropdownMenuItem class="cursor-pointer gap-2 text-purple-600 dark:text-purple-400" @click="changeStatus(item.id, 'izin')">
                <AlertCircle class="size-4" />
                Izin
              </DropdownMenuItem>
              <DropdownMenuItem class="cursor-pointer gap-2 text-indigo-600 dark:text-indigo-400" @click="changeStatus(item.id, 'sakit')">
                <AlertCircle class="size-4" />
                Sakit
              </DropdownMenuItem>
              <DropdownMenuItem class="cursor-pointer gap-2 text-rose-600 dark:text-rose-400" @click="changeStatus(item.id, 'alpa')">
                <XCircle class="size-4" />
                Tanpa Keterangan (Alpa)
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem class="cursor-pointer gap-2 text-slate-500" @click="changeStatus(item.id, 'belum_absen')">
                <UserX class="size-4" />
                Reset (Belum Absen)
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </template>
      </DataTableCard>
    </div>

    <!-- ── TAB 2: LOG ACTIVITY PEMINDAI REAL-TIME ── -->
    <div v-if="activeTab === 'logs'" class="space-y-4">
      <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="font-semibold text-base text-slate-900 dark:text-white flex items-center gap-2">
              <Activity class="size-4 text-indigo-500" />
              Feed Scan Kiosk Real-Time Hari Ini
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Riwayat siswa yang melakukan verifikasi presensi via Wajah AI, RFID, atau Fingerprint.
            </p>
          </div>
          <Badge variant="outline" class="gap-1 font-mono text-xs">
            Total {{ filteredLogs.length }} Aktivitas
          </Badge>
        </div>

        <!-- Logs Table / Empty State -->
        <div v-if="filteredLogs.length === 0" class="flex flex-col items-center justify-center py-12 text-center text-slate-400">
          <Activity class="size-12 stroke-[1.5] mb-3 text-slate-300 dark:text-slate-700 animate-pulse" />
          <p class="font-medium text-sm text-slate-600 dark:text-slate-400">Belum ada aktivitas scan hari ini</p>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Buka terminal kiosk presensi untuk memulai verifikasi scan siswa.</p>
        </div>

        <div v-else class="relative overflow-x-auto rounded-xl border border-slate-200/80 dark:border-slate-800">
          <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-4 py-3 font-semibold">Waktu Scan</th>
                <th class="px-4 py-3 font-semibold">Nama Siswa</th>
                <th class="px-4 py-3 font-semibold">NISN</th>
                <th class="px-4 py-3 font-semibold">Kelas</th>
                <th class="px-4 py-3 font-semibold">Tipe Absen</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
              <tr v-for="log in filteredLogs" :key="log.id" class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                <td class="px-4 py-3 font-mono font-semibold text-xs text-indigo-600 dark:text-indigo-400">
                  {{ log.waktu }}
                </td>
                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">
                  <div class="flex items-center gap-2">
                    <div class="size-7 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[10px] font-bold">
                      {{ log.inisial || getInitials(log.nama) }}
                    </div>
                    <span>{{ log.nama }}</span>
                  </div>
                </td>
                <td class="px-4 py-3 font-mono text-xs text-slate-500 dark:text-slate-400">
                  {{ log.nisn || '-' }}
                </td>
                <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300 font-medium">
                  {{ log.kelas || '-' }}
                </td>
                <td class="px-4 py-3">
                  <Badge
                    :variant="log.tipe === 'Masuk' ? 'default' : 'secondary'"
                    :class="log.tipe === 'Masuk' 
                      ? 'bg-emerald-500/15 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 border-emerald-500/30' 
                      : 'bg-indigo-500/15 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 border-indigo-500/30'"
                    class="border text-xs px-2.5 py-0.5 font-semibold"
                  >
                    {{ log.tipe || 'Masuk' }}
                  </Badge>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ── Modal Registrasi Wajah AI ── -->
    <Dialog :open="isRegisterModalOpen" @update:open="val => !val && closeFaceRegistration()">
      <DialogContent class="sm:max-w-[480px]">
        <DialogHeader>
          <DialogTitle class="flex items-center gap-2">
            <Camera class="size-5 text-indigo-500" />
            Registrasi Wajah Siswa (AI)
          </DialogTitle>
          <DialogDescription>
            Mendaftarkan biometrik wajah untuk <strong>{{ selectedStudent?.nama }}</strong> (NISN: {{ selectedStudent?.nisn || '-' }}).
          </DialogDescription>
        </DialogHeader>

        <div class="flex flex-col items-center justify-center p-4 space-y-4">
          <!-- Kamera Feed -->
          <div class="relative w-full aspect-video rounded-2xl overflow-hidden bg-slate-900 border border-slate-800 flex items-center justify-center shadow-inner">
            <video v-if="cameraStatus === 'active'" ref="videoRef" autoplay playsinline class="w-full h-full object-cover scale-x-[-1]" />
            
            <div v-if="cameraStatus === 'loading'" class="flex flex-col items-center gap-2 text-white text-xs">
              <span class="animate-spin rounded-full h-6 w-6 border-b-2 border-white"></span>
              <span>Menyalakan kamera...</span>
            </div>

            <div v-if="cameraStatus === 'idle' || cameraStatus === 'error'" class="flex flex-col items-center gap-2 text-slate-400 text-xs">
              <Camera class="size-8" />
              <span>Kamera belum aktif</span>
            </div>
          </div>

          <!-- Canvas hidden -->
          <canvas ref="canvasRef" class="hidden" width="480" height="480" />
        </div>

        <DialogFooter class="flex gap-2 justify-end">
          <Button variant="outline" @click="closeFaceRegistration">Batal</Button>
          <Button v-if="cameraStatus !== 'active'" @click="startCamera">Nyalakan Kamera</Button>
          <Button v-else :disabled="isRegistering" @click="captureAndRegister">
            {{ isRegistering ? 'Mendaftarkan...' : 'Ambil Foto & Registrasi' }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
