<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  Camera,
  Database,
  Search,
  Download,
  Printer,
  Info,
  AlertCircle,
} from 'lucide-vue-next'
import { getStudents, getPersonalHistory, updateStudentStatus } from '@/services/api/absensi'
import { Skeleton } from '@/components/ui/skeleton'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
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
import { MoreVertical } from 'lucide-vue-next'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

const router = useRouter()
const auth = useAuthStore()

// ─── State ──────────────────────────────────────────────
const isPersonalView = computed(() => ['siswa', 'orang_tua'].includes(auth.user?.role))

const selectedKelas = ref('semua')
const selectedMapel = ref('semua')
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 6

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
    const matchKelas = selectedKelas.value === 'semua' || item.kelas === selectedKelas.value
    const matchMapel = selectedMapel.value === 'semua' || item.mapel === selectedMapel.value
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

// Personal Stats
const totalPersonalHadir = computed(() => absensiData.value.filter(d => ['hadir', 'terlambat'].includes(d.status)).length)
const totalPersonalIzin = computed(() => absensiData.value.filter(d => d.status === 'izin').length)
const totalPersonalSakit = computed(() => absensiData.value.filter(d => d.status === 'sakit').length)
const totalPersonalAlpa = computed(() => absensiData.value.filter(d => d.status === 'alpa').length)

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredData.value.slice(start, start + itemsPerPage)
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

function prevPage() { if (currentPage.value > 1) currentPage.value-- }
function nextPage() { if (currentPage.value < totalPages.value) currentPage.value++ }

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
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- ── Page Header ── -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">
          {{ isPersonalView ? 'Rekap Absensi Pribadi' : 'Dashboard Absensi Siswa' }}
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">
          {{ isPersonalView ? 'Pantau riwayat kehadiran Anda di setiap mata pelajaran.' : 'Pantau rekapitulasi kehadiran siswa secara real-time dari pemindai Kiosk Sekolah.' }}
        </p>
      </div>
      
      <div v-if="!isPersonalView" class="flex items-center gap-2 shrink-0">
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
      v-if="!isPersonalView"
      class="flex items-start gap-3 rounded-xl border border-indigo-100 bg-indigo-50/50 px-4 py-3.5 text-sm text-indigo-700 dark:border-indigo-900/30 dark:bg-indigo-950/20 dark:text-indigo-400 animate-in fade-in duration-200"
    >
      <Info class="size-4 mt-0.5 shrink-0" />
      <div>
        <span class="font-semibold">Terminal Terhubung:</span> Klik tombol <span class="font-medium text-indigo-900 dark:text-indigo-300">Mulai Presensi Siswa</span> untuk membuka kiosk terminal verifikasi wajah, fingerprint, atau RFID.
      </div>
    </div>

    <!-- ── Stat Cards ── -->
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
      <!-- Card 1 -->
      <Card class="p-5 hover:shadow transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-muted-foreground text-xs uppercase tracking-wider">
            {{ isPersonalView ? 'Total Hadir' : 'Total Siswa' }}
          </span>
          <div :class="[isPersonalView ? 'bg-green-50 dark:bg-green-950/40' : 'bg-muted', 'p-1.5 rounded-lg']">
            <Database :class="['size-4', isPersonalView ? 'text-green-500' : 'text-muted-foreground']" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-9 w-20" />
        <div v-else :class="['text-3xl font-bold tracking-tight', isPersonalView ? 'text-green-600 dark:text-green-400' : 'text-foreground']">
          {{ isPersonalView ? totalPersonalHadir : totalSiswa }}
        </div>
      </Card>
      
      <!-- Card 2 -->
      <Card class="p-5 hover:shadow transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-muted-foreground text-xs uppercase tracking-wider">
            {{ isPersonalView ? 'Sakit' : 'Sudah Hadir' }}
          </span>
          <div :class="[isPersonalView ? 'bg-blue-50 dark:bg-blue-950/40' : 'bg-green-50 dark:bg-green-950/40', 'p-1.5 rounded-lg']">
            <Database :class="['size-4', isPersonalView ? 'text-blue-500' : 'text-green-500']" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-9 w-20" />
        <div v-else :class="['text-3xl font-bold tracking-tight', isPersonalView ? 'text-blue-600 dark:text-blue-400' : 'text-green-600 dark:text-green-400']">
          {{ isPersonalView ? totalPersonalSakit : sudahHadir }}
        </div>
      </Card>

      <!-- Card 3 -->
      <Card class="p-5 hover:shadow transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-muted-foreground text-xs uppercase tracking-wider">
            {{ isPersonalView ? 'Izin' : 'Belum Scan' }}
          </span>
          <div :class="['p-1.5 rounded-lg', isPersonalView ? 'bg-indigo-50 dark:bg-indigo-950/40' : 'bg-yellow-50 dark:bg-yellow-950/40']">
            <Database :class="['size-4', isPersonalView ? 'text-indigo-600 dark:text-indigo-400' : 'text-yellow-600 dark:text-yellow-400']" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-9 w-20" />
        <div v-else :class="['text-3xl font-bold tracking-tight', isPersonalView ? 'text-indigo-600 dark:text-indigo-400' : 'text-yellow-600 dark:text-yellow-400']">
          {{ isPersonalView ? totalPersonalIzin : belumScan }}
        </div>
      </Card>

      <!-- Card 4 -->
      <Card class="p-5 hover:shadow transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-muted-foreground text-xs uppercase tracking-wider">
            {{ isPersonalView ? 'Tanpa Keterangan' : 'Terlambat' }}
          </span>
          <div class="p-1.5 bg-red-50 dark:bg-red-950/40 rounded-lg">
            <Database class="size-4 text-red-500" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-9 w-20" />
        <div v-else class="text-3xl font-bold tracking-tight text-red-600 dark:text-red-400">
          {{ isPersonalView ? totalPersonalAlpa : terlambat }}
        </div>
      </Card>
    </div>

    <!-- ── Filter & Search Bar ── -->
    <div class="flex flex-wrap items-center gap-3">
      <Select v-model="selectedKelas">
        <SelectTrigger class="w-[140px]">
          <SelectValue placeholder="Pilih Kelas" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="semua">Semua Kelas</SelectItem>
          <SelectItem v-for="k in kelasList" :key="k" :value="k">{{ k }}</SelectItem>
        </SelectContent>
      </Select>

      <Select v-model="selectedMapel">
        <SelectTrigger class="w-[160px]">
          <SelectValue placeholder="Mata Pelajaran" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="semua">Semua Mapel</SelectItem>
          <SelectItem v-for="m in mapelList" :key="m" :value="m">{{ m }}</SelectItem>
        </SelectContent>
      </Select>

      <div class="flex-1 min-w-[180px] relative">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground pointer-events-none" />
        <Input
          v-model="searchQuery"
          id="search-siswa"
          type="text"
          placeholder="Cari siswa..."
          class="pl-9"
        />
      </div>

      <Button
        id="btn-download"
        variant="outline"
        size="icon"
        title="Download"
      >
        <Download class="size-4" />
      </Button>
      <Button
        id="btn-print"
        variant="outline"
        size="icon"
        title="Cetak"
      >
        <Printer class="size-4" />
      </Button>
    </div>

    <!-- ── Table ── -->
    <Card class="overflow-hidden">
      <Table>
        <TableHeader>
          <TableRow class="bg-muted/50">
            <TableHead v-if="!isPersonalView" class="font-semibold">Nama Siswa &amp; Kelas</TableHead>
            <TableHead v-if="isPersonalView" class="font-semibold">Tanggal</TableHead>
            <TableHead class="font-semibold">Mata Pelajaran</TableHead>
            <TableHead class="font-semibold">Jam Masuk</TableHead>
            <TableHead class="font-semibold">Jam Keluar</TableHead>
            <TableHead class="font-semibold">Status</TableHead>
            <TableHead v-if="!isPersonalView" class="font-semibold w-[80px]">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="isLoading">
            <TableRow v-for="i in 6" :key="`skel-${i}`">
              <TableCell><Skeleton class="h-5 w-32" /></TableCell>
              <TableCell><Skeleton class="h-5 w-24" /></TableCell>
              <TableCell><Skeleton class="h-5 w-16" /></TableCell>
              <TableCell><Skeleton class="h-5 w-16" /></TableCell>
              <TableCell><Skeleton class="h-6 w-20 rounded-full" /></TableCell>
              <TableCell v-if="!isPersonalView"><Skeleton class="h-8 w-8 rounded-full" /></TableCell>
            </TableRow>
          </template>
          
          <template v-else-if="isError">
            <TableRow>
              <TableCell colspan="5" class="h-32 text-center">
                <Alert variant="destructive" class="max-w-md mx-auto text-left">
                  <AlertCircle class="w-4 h-4" />
                  <AlertTitle>Error</AlertTitle>
                  <AlertDescription>{{ errorMessage }}</AlertDescription>
                </Alert>
              </TableCell>
            </TableRow>
          </template>
          
          <template v-else>
            <TableRow
              v-for="siswa in paginatedData"
              :key="siswa.id"
              class="hover:bg-muted/30 transition-colors"
            >
            <TableCell v-if="!isPersonalView">
              <div class="font-semibold text-sm">{{ siswa.nama }} <span class="text-muted-foreground font-normal">({{ siswa.kelas }})</span></div>
            </TableCell>
            <TableCell v-if="isPersonalView">
              <div class="font-semibold text-sm">{{ siswa.tanggal }}</div>
            </TableCell>
            <TableCell>
              <span class="text-primary font-medium text-sm">{{ siswa.mapel }}</span>
            </TableCell>
            <TableCell class="text-sm font-mono">{{ siswa.jamMasuk ?? '-' }}</TableCell>
            <TableCell class="text-sm font-mono">{{ siswa.jamKeluar ?? '-' }}</TableCell>
            <TableCell>
              <Badge
                :variant="siswa.status === 'hadir' ? 'default' : siswa.status === 'terlambat' ? 'destructive' : 'secondary'"
                :class="{
                  'bg-green-100 text-green-700 hover:bg-green-100 dark:bg-green-900/40 dark:text-green-400': siswa.status === 'hadir',
                  'bg-yellow-100 text-yellow-700 hover:bg-yellow-100 dark:bg-yellow-900/40 dark:text-yellow-400': siswa.status === 'terlambat',
                  'bg-blue-100 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-400': siswa.status === 'sakit',
                  'bg-indigo-100 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-400': siswa.status === 'izin',
                  'bg-red-100 text-red-700 hover:bg-red-100 dark:bg-red-900/40 dark:text-red-400': siswa.status === 'alpa',
                  'bg-muted text-muted-foreground': siswa.status === 'belum_absen',
                }"
              >
                {{ getStatusLabel(siswa.status) }}
              </Badge>
            </TableCell>
            <TableCell v-if="!isPersonalView">
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" class="h-8 w-8 p-0">
                    <span class="sr-only">Buka menu</span>
                    <MoreVertical class="h-4 w-4" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuLabel>Ubah Status</DropdownMenuLabel>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem @click="changeStatus(siswa.id, 'hadir')">Hadir</DropdownMenuItem>
                  <DropdownMenuItem @click="changeStatus(siswa.id, 'sakit')">Sakit</DropdownMenuItem>
                  <DropdownMenuItem @click="changeStatus(siswa.id, 'izin')">Izin</DropdownMenuItem>
                  <DropdownMenuItem @click="changeStatus(siswa.id, 'alpa')">Tanpa Keterangan</DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </TableCell>
            </TableRow>

            <TableRow v-if="paginatedData.length === 0">
              <TableCell :colspan="isPersonalView ? 5 : 6" class="h-32 text-center text-muted-foreground flex-col items-center justify-center">
                <div class="flex flex-col items-center justify-center gap-2">
                  <Search class="size-8 text-muted-foreground/50" />
                  <p class="text-sm">Tidak ada data siswa ditemukan.</p>
                </div>
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </Card>

    <!-- ── Pagination ── -->
    <div class="flex items-center justify-between text-sm text-muted-foreground" v-if="filteredData.length > 0">
      <span>Menampilkan {{ paginatedData.length }} dari {{ filteredData.length }} data</span>
      <div class="flex items-center gap-1">
        <Button
          id="btn-prev-page"
          variant="outline"
          size="sm"
          :disabled="currentPage === 1"
          @click="prevPage"
        >
          Prev
        </Button>
        <Button
          v-for="p in totalPages"
          :key="p"
          :variant="p === currentPage ? 'default' : 'outline'"
          size="sm"
          @click="currentPage = p"
        >
          {{ p }}
        </Button>
        <Button
          id="btn-next-page"
          variant="outline"
          size="sm"
          :disabled="currentPage === totalPages"
          @click="nextPage"
        >
          Next
        </Button>
      </div>
    </div>
  </div>
</template>
