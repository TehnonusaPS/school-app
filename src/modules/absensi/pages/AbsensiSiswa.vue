<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  Camera,
  Database,
  Search,
  Download,
  Printer,
  Info,
} from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

const router = useRouter()

// ─── State ──────────────────────────────────────────────
const selectedKelas = ref('')
const selectedMapel = ref('')
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 6

// ─── Mock data & LocalStorage Sync ─────────────────────
const kelasList = ['XI IPA 1', 'XI IPA 2', 'XI IPS 1', 'XII IPA 1']
const mapelList = ['Biologi', 'Matematika', 'Fisika', 'Kimia', 'Bahasa Indonesia']

const absensiData = ref([])
const scanResults = ref([])

const initialData = [
  { id: 1, nama: 'Ahmad Fadil', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: '07:12:45', jamKeluar: '10:15:20', status: 'hadir' },
  { id: 2, nama: 'Bunga Citra', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: '07:25:10', jamKeluar: null, status: 'terlambat' },
  { id: 3, nama: 'Cakra Khan', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: null, jamKeluar: null, status: 'belum_absen' },
  { id: 4, nama: 'Dian Sastro', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: '07:10:05', jamKeluar: '10:15:33', status: 'hadir' },
  { id: 5, nama: 'Elsa Novita', kelas: 'XI IPA 2', mapel: 'Biologi', jamMasuk: '07:05:22', jamKeluar: '10:14:10', status: 'hadir' },
  { id: 6, nama: 'Farhan Ramdan', kelas: 'XI IPA 2', mapel: 'Biologi', jamMasuk: '07:30:00', jamKeluar: null, status: 'terlambat' },
  { id: 7, nama: 'Gita Nirmala', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: null, jamKeluar: null, status: 'belum_absen' },
  { id: 8, nama: 'Hendra Saputra', kelas: 'XI IPA 1', mapel: 'Biologi', jamMasuk: '07:08:11', jamKeluar: '10:15:00', status: 'hadir' },
]

const initialLogs = [
  { id: 1, nama: 'Ahmad Fadil', inisial: 'AF', waktu: '07:12:45', tipe: 'Masuk' },
  { id: 2, nama: 'Dian Sastro', inisial: 'DS', waktu: '07:10:05', tipe: 'Masuk' },
]

function loadData() {
  if (!localStorage.getItem('absensiData')) {
    localStorage.setItem('absensiData', JSON.stringify(initialData))
  }
  if (!localStorage.getItem('scanResults')) {
    localStorage.setItem('scanResults', JSON.stringify(initialLogs))
  }
  absensiData.value = JSON.parse(localStorage.getItem('absensiData'))
  scanResults.value = JSON.parse(localStorage.getItem('scanResults'))
}

onMounted(() => {
  loadData()
})

// ─── Computed ────────────────────────────────────────────
const filteredData = computed(() => {
  return absensiData.value.filter(item => {
    const matchKelas = !selectedKelas.value || item.kelas === selectedKelas.value
    const matchMapel = !selectedMapel.value || item.mapel === selectedMapel.value
    const matchSearch =
      !searchQuery.value ||
      item.nama.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchKelas && matchMapel && matchSearch
  })
})

const totalSiswa = computed(() => absensiData.value.length)
const sudahHadir = computed(() => absensiData.value.filter(d => d.status === 'hadir').length)
const belumScan = computed(() => absensiData.value.filter(d => d.status === 'belum_absen').length)
const terlambat = computed(() => absensiData.value.filter(d => d.status === 'terlambat').length)

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredData.value.slice(start, start + itemsPerPage)
})

// ─── Helpers ─────────────────────────────────────────────
function getStatusLabel(status) {
  if (status === 'hadir') return 'Hadir'
  if (status === 'terlambat') return 'Terlambat'
  return 'Belum Absen'
}

function prevPage() { if (currentPage.value > 1) currentPage.value-- }
function nextPage() { if (currentPage.value < totalPages.value) currentPage.value++ }

function openScanTab() {
  const route = router.resolve('/absensi/siswa/scan')
  window.open(route.href, '_blank')
}
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- ── Page Header ── -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">
          Dashboard Absensi Siswa
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">
          Pantau rekapitulasi kehadiran siswa secara real-time dari pemindai Kiosk Sekolah.
        </p>
      </div>
      
      <div class="flex items-center gap-2 shrink-0">
        <Button 
          id="btn-buka-kiosk" 
          class="gap-2" 
          @click="openScanTab"
        >
          <Camera class="size-4" />
          Mulai Presensi Siswa
        </Button>
      </div>
    </div>

    <!-- ── Info Banner ── -->
    <div 
      class="flex items-start gap-3 rounded-xl border border-indigo-100 bg-indigo-50/50 px-4 py-3.5 text-sm text-indigo-700 dark:border-indigo-900/30 dark:bg-indigo-950/20 dark:text-indigo-400 animate-in fade-in duration-200"
    >
      <Info class="size-4 mt-0.5 shrink-0" />
      <div>
        <span class="font-semibold">Terminal Terhubung:</span> Klik tombol <span class="font-medium text-indigo-900 dark:text-indigo-300">Mulai Presensi Siswa</span> untuk membuka kiosk terminal verifikasi wajah, fingerprint, atau RFID.
      </div>
    </div>

    <!-- ── Stat Cards ── -->
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
      <!-- Total Siswa -->
      <div class="rounded-xl border bg-card p-5 shadow-sm hover:shadow transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-muted-foreground text-xs uppercase tracking-wider">Total Siswa</span>
          <div class="p-1.5 bg-muted rounded-lg">
            <Database class="size-4 text-muted-foreground" />
          </div>
        </div>
        <div class="text-3xl font-bold tracking-tight text-foreground">{{ totalSiswa }}</div>
      </div>
      <!-- Sudah Hadir -->
      <div class="rounded-xl border bg-card p-5 shadow-sm hover:shadow transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-muted-foreground text-xs uppercase tracking-wider">Sudah Hadir</span>
          <div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg">
            <Database class="size-4 text-green-500" />
          </div>
        </div>
        <div class="text-3xl font-bold tracking-tight text-green-600 dark:text-green-400">{{ sudahHadir }}</div>
      </div>
      <!-- Belum Scan -->
      <div class="rounded-xl border bg-card p-5 shadow-sm hover:shadow transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-muted-foreground text-xs uppercase tracking-wider">Belum Scan</span>
          <div class="p-1.5 bg-yellow-50 dark:bg-yellow-950/40 rounded-lg">
            <Database class="size-4 text-yellow-600 dark:text-yellow-400" />
          </div>
        </div>
        <div class="text-3xl font-bold tracking-tight text-yellow-600 dark:text-yellow-400">{{ belumScan }}</div>
      </div>
      <!-- Terlambat -->
      <div class="rounded-xl border bg-card p-5 shadow-sm hover:shadow transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-muted-foreground text-xs uppercase tracking-wider">Terlambat</span>
          <div class="p-1.5 bg-red-50 dark:bg-red-950/40 rounded-lg">
            <Database class="size-4 text-red-500" />
          </div>
        </div>
        <div class="text-3xl font-bold tracking-tight text-red-600 dark:text-red-400">{{ terlambat }}</div>
      </div>
    </div>

    <!-- ── Filter & Search Bar ── -->
    <div class="flex flex-wrap items-center gap-3">
      <select
        v-model="selectedKelas"
        id="filter-kelas"
        class="h-9 rounded-lg border border-input bg-background px-3 py-1.5 text-sm text-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-1 min-w-[130px]"
      >
        <option value="">Pilih Kelas</option>
        <option v-for="k in kelasList" :key="k" :value="k">{{ k }}</option>
      </select>

      <select
        v-model="selectedMapel"
        id="filter-mapel"
        class="h-9 rounded-lg border border-input bg-background px-3 py-1.5 text-sm text-foreground shadow-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-1 min-w-[140px]"
      >
        <option value="">Mata Pelajaran</option>
        <option v-for="m in mapelList" :key="m" :value="m">{{ m }}</option>
      </select>

      <div class="flex-1 min-w-[180px] relative">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground pointer-events-none" />
        <input
          v-model="searchQuery"
          id="search-siswa"
          type="text"
          placeholder="Cari siswa..."
          class="h-9 w-full rounded-lg border border-input bg-background pl-9 pr-3 py-1.5 text-sm text-foreground shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-1"
        />
      </div>

      <button
        id="btn-download"
        class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-input bg-background text-muted-foreground shadow-sm hover:bg-muted transition-colors"
        title="Download"
      >
        <Download class="size-4" />
      </button>
      <button
        id="btn-print"
        class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-input bg-background text-muted-foreground shadow-sm hover:bg-muted transition-colors"
        title="Cetak"
      >
        <Printer class="size-4" />
      </button>
    </div>

    <!-- ── Table ── -->
    <div class="rounded-xl border shadow-sm overflow-hidden bg-card">
      <Table>
        <TableHeader>
          <TableRow class="bg-muted/50">
            <TableHead class="font-semibold">Nama Siswa &amp; Kelas</TableHead>
            <TableHead class="font-semibold">Mata Pelajaran</TableHead>
            <TableHead class="font-semibold">Jam Masuk</TableHead>
            <TableHead class="font-semibold">Jam Keluar</TableHead>
            <TableHead class="font-semibold">Status</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow
            v-for="siswa in paginatedData"
            :key="siswa.id"
            class="hover:bg-muted/30 transition-colors"
          >
            <TableCell>
              <div class="font-semibold text-sm">{{ siswa.nama }} <span class="text-muted-foreground font-normal">({{ siswa.kelas }})</span></div>
            </TableCell>
            <TableCell>
              <span class="text-primary font-medium text-sm">{{ siswa.mapel }}</span>
            </TableCell>
            <TableCell class="text-sm font-mono">{{ siswa.jamMasuk ?? '-' }}</TableCell>
            <TableCell class="text-sm font-mono">{{ siswa.jamKeluar ?? '-' }}</TableCell>
            <TableCell>
              <span
                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold"
                :class="{
                  'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400': siswa.status === 'hadir',
                  'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400': siswa.status === 'terlambat',
                  'bg-muted text-muted-foreground': siswa.status === 'belum_absen',
                }"
              >
                <span
                  class="size-1.5 rounded-full"
                  :class="{
                    'bg-green-500': siswa.status === 'hadir',
                    'bg-yellow-500': siswa.status === 'terlambat',
                    'bg-gray-400': siswa.status === 'belum_absen',
                  }"
                />
                {{ getStatusLabel(siswa.status) }}
              </span>
            </TableCell>
          </TableRow>

          <TableRow v-if="paginatedData.length === 0">
            <TableCell colspan="5" class="h-24 text-center text-muted-foreground text-sm">
              Tidak ada data siswa ditemukan.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <!-- ── Pagination ── -->
    <div class="flex items-center justify-between text-sm text-muted-foreground" v-if="filteredData.length > 0">
      <span>Menampilkan {{ paginatedData.length }} dari {{ filteredData.length }} data</span>
      <div class="flex items-center gap-1">
        <button
          id="btn-prev-page"
          class="h-8 px-3 rounded-md border border-input bg-background text-sm font-medium hover:bg-muted transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="currentPage === 1"
          @click="prevPage"
        >
          Prev
        </button>
        <button
          v-for="p in totalPages"
          :key="p"
          class="h-8 w-8 rounded-md border text-sm font-medium transition-colors"
          :class="p === currentPage ? 'bg-primary text-primary-foreground border-primary' : 'border-input bg-background hover:bg-muted'"
          @click="currentPage = p"
        >
          {{ p }}
        </button>
        <button
          id="btn-next-page"
          class="h-8 px-3 rounded-md border border-input bg-background text-sm font-medium hover:bg-muted transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="currentPage === totalPages"
          @click="nextPage"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>
