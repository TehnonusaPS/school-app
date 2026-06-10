<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import {
  Camera,
  Database,
  Download,
  Printer,
  Info,
  MoreVertical
} from 'lucide-vue-next'
import { getStudents, getPersonalHistory, updateStudentStatus } from '@/services/api/absensi'
import { Button } from '@/components/ui/button'
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

// --- Mock Mapel List ---
const mapelList = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'Pendidikan Jasmani']

// ─── State ──────────────────────────────────────────────
const isPersonalView = computed(() => ['siswa', 'orang_tua'].includes(auth.user?.role))

const filterValues = ref({
  search: '',
  kelas: 'semua',
  mapel: 'semua'
})

const filters = computed(() => {
  const f = [
    { type: 'search', key: 'search', placeholder: 'Cari siswa...' }
  ]
  if (!isPersonalView.value) {
    f.push({
      type: 'select',
      key: 'kelas',
      label: 'Kelas:',
      placeholder: 'Semua Kelas',
      options: kelasList.map(k => ({ label: k, value: k }))
    })
  }
  f.push({
    type: 'select',
    key: 'mapel',
    label: 'Mapel:',
    placeholder: 'Semua Mapel',
    options: mapelList.map(m => ({ label: m, value: m }))
  })
  return f
})

const columns = computed(() => {
  const cols = []
  if (isPersonalView.value) {
    cols.push({ key: 'tanggal', label: 'Tanggal' })
  } else {
    cols.push({ key: 'nama', label: 'Nama Siswa & Kelas' })
  }
  cols.push({ key: 'mapel', label: 'Mata Pelajaran' })
  cols.push({ key: 'jamMasuk', label: 'Jam Masuk', type: 'code' })
  cols.push({ key: 'jamKeluar', label: 'Jam Keluar', type: 'code' })
  cols.push({ key: 'status', label: 'Status' })
  if (!isPersonalView.value) {
    cols.push({ key: 'actions', label: 'Aksi' })
  }
  return cols
})

const absensiData = ref([])
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
    if (isPersonalView.value) {
      const data = await getPersonalHistory(auth.user?.id)
      absensiData.value = data
    } else {
      const data = await getStudents()
      absensiData.value = data
    }
    isError.value = false
  } catch (err) {
    isError.value = true
    errorMessage.value = err.message || 'Terjadi kesalahan sistem'
  } finally {
    if (!isPolling) isLoading.value = false
  }
}

onMounted(() => {
  loadData()
  // Background polling every 10s
  pollInterval = setInterval(() => loadData(true), 10000)
})

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval)
})

// ─── Computed ────────────────────────────────────────────
const filteredData = computed(() => {
  return absensiData.value.filter(item => {
    const matchKelas = filterValues.value.kelas === 'semua' || item.kelas === filterValues.value.kelas
    const matchMapel = filterValues.value.mapel === 'semua' || item.mapel === filterValues.value.mapel
    const matchSearch =
      !filterValues.value.search ||
      item.nama.toLowerCase().includes(filterValues.value.search.toLowerCase())
    return matchKelas && matchMapel && matchSearch
  })
})

const totalSiswa = computed(() => absensiData.value.length)
const sudahHadir = computed(() => absensiData.value.filter(d => d.status === 'hadir').length)
const belumScan = computed(() => absensiData.value.filter(d => d.status === 'belum_absen').length)
const terlambat = computed(() => absensiData.value.filter(d => d.status === 'terlambat').length)

// Personal Stats
const totalPersonalHadir = computed(() => absensiData.value.filter(d => ['hadir', 'terlambat'].includes(d.status)).length)
const totalPersonalIzin = computed(() => absensiData.value.filter(d => d.status === 'izin').length)
const totalPersonalSakit = computed(() => absensiData.value.filter(d => d.status === 'sakit').length)
const totalPersonalAlpa = computed(() => absensiData.value.filter(d => d.status === 'alpa').length)

const perPage = ref(6)
const { currentPage, total, from, to, paginatedItems: paginatedData } = usePagination(filteredData, perPage)

watch(filteredData, () => {
  currentPage.value = 1
})

// ─── Helpers ─────────────────────────────────────────────
function getStatusLabel(status) {
  if (status === 'hadir') return 'Hadir'
  if (status === 'terlambat') return 'Terlambat'
  if (status === 'izin') return 'Izin'
  if (status === 'sakit') return 'Sakit'
  if (status === 'alpa') return 'Tanpa Keterangan'
  return 'Belum Absen'
}

function openScanTab() {
  const route = router.resolve('/absensi/siswa/scan')
  window.open(route.href, '_blank')
}

async function changeStatus(studentId, newStatus) {
  try {
    await updateStudentStatus(studentId, newStatus)
    await loadData() // reload to get new status
  } catch (err) {
    alert(err.message || 'Gagal mengubah status')
  }
}

const tableActions = computed(() => {
  if (isPersonalView.value) return []
  return [
    {
      label: 'Cetak',
      icon: Printer,
      variant: 'outline',
      click: () => {}
    },
    {
      label: 'Download',
      icon: Download,
      variant: 'outline',
      click: () => {}
    }
  ]
})
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
      :title="isPersonalView ? 'Rekap Absensi Pribadi' : 'Dashboard Absensi Siswa'"
      :description="isPersonalView ? 'Pantau riwayat kehadiran Anda di setiap mata pelajaran.' : 'Pantau rekapitulasi kehadiran siswa secara real-time dari pemindai Kiosk Sekolah.'"
      :actions="!isPersonalView ? [
        {
          label: 'Mulai Presensi Siswa',
          icon: Camera,
          variant: 'default',
          click: openScanTab
        }
      ] : []"
    />

    <!-- ── Info Banner ── -->
    <div 
      v-if="!isPersonalView"
      class="flex items-start gap-3 rounded-xl border border-indigo-100 bg-indigo-50/50 px-4 py-3.5 text-sm text-indigo-700 dark:border-indigo-900/30 dark:bg-indigo-950/20 dark:text-indigo-400 animate-in fade-in duration-200"
    >
      <Info class="size-4 mt-0.5 shrink-0" />
      <div>
        <span class="font-semibold">Terminal Terhubung:</span> Klik tombol <span class="font-medium text-indigo-900 dark:text-indigo-300">Mulai Presensi Siswa</span> untuk membuka kiosk terminal verifikasi wajah, fingerprint, atau RFID.
      </div>
    </div>

    <!-- ── Stat Cards ── -->
    <StatCardGrid cols="4">
      <StatCard
        :label="isPersonalView ? 'Total Hadir' : 'Total Siswa'"
        :value="isLoading ? '-' : (isPersonalView ? totalPersonalHadir : totalSiswa)"
        :icon="Database"
        :illustration="isPersonalView ? 'school_bell' : 'graduation_cap'"
        variant="primary"
      />
      <StatCard
        :label="isPersonalView ? 'Sakit' : 'Sudah Hadir'"
        :value="isLoading ? '-' : (isPersonalView ? totalPersonalSakit : sudahHadir)"
        :icon="Database"
        :illustration="isPersonalView ? 'atom' : 'school_bell'"
        :variant="isPersonalView ? 'blue' : 'emerald'"
      />
      <StatCard
        :label="isPersonalView ? 'Izin' : 'Belum Scan'"
        :value="isLoading ? '-' : (isPersonalView ? totalPersonalIzin : belumScan)"
        :icon="Database"
        :illustration="isPersonalView ? 'paper_sheet' : 'pencil'"
        :variant="isPersonalView ? 'violet' : 'amber'"
      />
      <StatCard
        :label="isPersonalView ? 'Tanpa Keterangan' : 'Terlambat'"
        :value="isLoading ? '-' : (isPersonalView ? totalPersonalAlpa : terlambat)"
        :icon="Database"
        :illustration="isPersonalView ? 'star' : 'ruler'"
        variant="amber"
      />
    </StatCardGrid>

    <!-- ── DataTableCard ── -->
    <div>
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
          <div class="font-semibold text-sm text-left">
            {{ item.nama }} 
            <span class="text-muted-foreground font-normal">({{ item.kelas }})</span>
          </div>
        </template>

        <template #cell-tanggal="{ value }">
          <div class="font-semibold text-sm text-left">{{ value }}</div>
        </template>

        <template #cell-mapel="{ value }">
          <span class="text-primary font-medium text-sm text-left block">{{ value }}</span>
        </template>

        <template #cell-status="{ value }">
          <Badge
            :variant="value === 'hadir' ? 'default' : value === 'terlambat' ? 'destructive' : 'secondary'"
            :class="{
              'bg-green-100 text-green-700 hover:bg-green-100 dark:bg-green-900/40 dark:text-green-400': value === 'hadir',
              'bg-yellow-100 text-yellow-700 hover:bg-yellow-100 dark:bg-yellow-900/40 dark:text-yellow-400': value === 'terlambat',
              'bg-blue-100 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-400': value === 'sakit',
              'bg-indigo-100 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-400': value === 'izin',
              'bg-red-100 text-red-700 hover:bg-red-100 dark:bg-red-900/40 dark:text-red-400': value === 'alpa',
              'bg-muted text-muted-foreground': value === 'belum_absen',
            }"
          >
            {{ getStatusLabel(value) }}
          </Badge>
        </template>

        <template #cell-actions="{ item }">
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" class="h-8 w-8 p-0 cursor-pointer">
                <span class="sr-only">Buka menu</span>
                <MoreVertical class="h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuLabel>Ubah Status</DropdownMenuLabel>
              <DropdownMenuSeparator />
              <DropdownMenuItem class="cursor-pointer" @click="changeStatus(item.id, 'hadir')">Hadir</DropdownMenuItem>
              <DropdownMenuItem class="cursor-pointer" @click="changeStatus(item.id, 'sakit')">Sakit</DropdownMenuItem>
              <DropdownMenuItem class="cursor-pointer" @click="changeStatus(item.id, 'izin')">Izin</DropdownMenuItem>
              <DropdownMenuItem class="cursor-pointer" @click="changeStatus(item.id, 'alpa')">Tanpa Keterangan</DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </template>
      </DataTableCard>
    </div>
  </div>
</template>
