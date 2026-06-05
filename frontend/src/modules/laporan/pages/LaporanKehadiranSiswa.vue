<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  ClipboardList,
  Search,
  Download,
  AlertTriangle,
  CheckCircle2,
  Users,
  TrendingDown,
  ChevronUp,
  ChevronDown,
  Printer,
  Calendar as CalendarIcon,
} from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Progress } from '@/components/ui/progress'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Separator } from '@/components/ui/separator'
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs'

// Mock data untuk kehadiran siswa
const mockData = [
  { id: 1, nisn: '0051234567', nama: 'Ahmad Fadil', kelas: 'XI IPA 1', totalHari: 22, hadir: 20, terlambat: 1, izin: 1, sakit: 0, alpa: 0 },
  { id: 2, nisn: '0069876543', nama: 'Bunga Citra', kelas: 'XI IPA 1', totalHari: 22, hadir: 18, terlambat: 2, izin: 1, sakit: 1, alpa: 0 },
  { id: 3, nisn: '0054321987', nama: 'Cakra Khan', kelas: 'XI IPA 1', totalHari: 22, hadir: 14, terlambat: 1, izin: 2, sakit: 1, alpa: 4 },
  { id: 4, nisn: '0061122334', nama: 'Dian Sastro', kelas: 'XI IPA 1', totalHari: 22, hadir: 21, terlambat: 0, izin: 1, sakit: 0, alpa: 0 },
  { id: 5, nisn: '0055566778', nama: 'Elsa Novita', kelas: 'XI IPA 2', totalHari: 22, hadir: 22, terlambat: 0, izin: 0, sakit: 0, alpa: 0 },
  { id: 6, nisn: '0068899001', nama: 'Farhan Ramdan', kelas: 'XI IPA 2', totalHari: 22, hadir: 12, terlambat: 2, izin: 1, sakit: 2, alpa: 5 },
  { id: 7, nisn: '0052233445', nama: 'Gita Nirmala', kelas: 'XI IPA 2', totalHari: 22, hadir: 19, terlambat: 1, izin: 2, sakit: 0, alpa: 0 },
  { id: 8, nisn: '0067788990', nama: 'Hendra Saputra', kelas: 'XI IPA 1', totalHari: 22, hadir: 17, terlambat: 3, izin: 0, sakit: 2, alpa: 0 },
  { id: 9, nisn: '0051199887', nama: 'Indira Putri', kelas: 'XI IPS 1', totalHari: 22, hadir: 10, terlambat: 1, izin: 3, sakit: 2, alpa: 6 },
  { id: 10, nisn: '0063344556', nama: 'Joko Susilo', kelas: 'XI IPS 1', totalHari: 22, hadir: 20, terlambat: 0, izin: 0, sakit: 2, alpa: 0 },
]

const THRESHOLD = 75 // % kehadiran minimum

const isLoading = ref(true)
const siswaData = ref([])
const searchQuery = ref('')
const selectedKelas = ref('semua')
const selectedSemester = ref('1')
const sortField = ref('nama')
const sortDir = ref('asc')
const currentPage = ref(1)
const itemsPerPage = 8

onMounted(() => {
  setTimeout(() => {
    siswaData.value = mockData
    isLoading.value = false
  }, 600)
})

const kelasList = computed(() => {
  const set = new Set(siswaData.value.map(d => d.kelas))
  return Array.from(set).sort()
})

function getPercentage(siswa) {
  return Math.round(((siswa.hadir + siswa.terlambat) / siswa.totalHari) * 100)
}

const filteredData = computed(() => {
  let data = siswaData.value.filter(item => {
    const matchKelas = selectedKelas.value === 'semua' || item.kelas === selectedKelas.value
    const matchSearch =
      !searchQuery.value ||
      item.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.nisn.includes(searchQuery.value) ||
      item.kelas.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchKelas && matchSearch
  })

  // Sort
  data = [...data].sort((a, b) => {
    let valA = sortField.value === 'persen' ? getPercentage(a) : (a[sortField.value] ?? '')
    let valB = sortField.value === 'persen' ? getPercentage(b) : (b[sortField.value] ?? '')
    if (typeof valA === 'string') valA = valA.toLowerCase()
    if (typeof valB === 'string') valB = valB.toLowerCase()
    if (valA < valB) return sortDir.value === 'asc' ? -1 : 1
    if (valA > valB) return sortDir.value === 'asc' ? 1 : -1
    return 0
  })

  return data
})

function toggleSort(field) {
  if (sortField.value === field) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDir.value = 'asc'
  }
  currentPage.value = 1
}

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredData.value.slice(start, start + itemsPerPage)
})

// Stats
const siswaDiBawahThreshold = computed(() => siswaData.value.filter(s => getPercentage(s) < THRESHOLD))
const siswaHadirPerfect = computed(() => siswaData.value.filter(s => getPercentage(s) === 100))
const avgKehadiran = computed(() => {
  if (siswaData.value.length === 0) return 0
  const total = siswaData.value.reduce((sum, s) => sum + getPercentage(s), 0)
  return Math.round(total / siswaData.value.length)
})

function getProgressColor(pct) {
  if (pct >= 90) return 'bg-green-500'
  if (pct >= 75) return 'bg-yellow-500'
  return 'bg-red-500'
}
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
          <ClipboardList class="size-6 text-primary" />
          Kehadiran Siswa
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">
          Rekapitulasi persentase kehadiran dan daftar siswa yang perlu perhatian khusus.
        </p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <Button id="btn-print-kehadiran" variant="outline" size="sm" class="gap-2">
          <Printer class="size-4" />
          Cetak
        </Button>
        <Button id="btn-export-kehadiran" size="sm" class="gap-2">
          <Download class="size-4" />
          Ekspor
        </Button>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Siswa</span>
          <div class="p-1.5 bg-muted rounded-lg">
            <Users class="size-4 text-muted-foreground" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold tracking-tight">{{ siswaData.length }}</div>
        <p class="text-xs text-muted-foreground mt-1">Terdaftar semester ini</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Rata-rata</span>
          <div class="p-1.5 bg-primary/10 rounded-lg">
            <ClipboardList class="size-4 text-primary" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-16" />
        <div v-else class="text-3xl font-bold tracking-tight text-primary">{{ avgKehadiran }}%</div>
        <p class="text-xs text-muted-foreground mt-1">Kehadiran seluruh kelas</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Hadir Penuh</span>
          <div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg">
            <CheckCircle2 class="size-4 text-green-600" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight text-green-600 dark:text-green-400">
          {{ siswaHadirPerfect.length }}
        </div>
        <p class="text-xs text-muted-foreground mt-1">Siswa dengan 100% hadir</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow border-red-200 dark:border-red-900/40">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Perlu Perhatian</span>
          <div class="p-1.5 bg-red-50 dark:bg-red-950/40 rounded-lg">
            <TrendingDown class="size-4 text-red-600" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight text-red-600 dark:text-red-400">
          {{ siswaDiBawahThreshold.length }}
        </div>
        <p class="text-xs text-muted-foreground mt-1">Di bawah {{ THRESHOLD }}% kehadiran</p>
      </Card>
    </div>

    <!-- Alert Siswa Bermasalah -->
    <Alert v-if="!isLoading && siswaDiBawahThreshold.length > 0" variant="destructive" class="border-red-200 bg-red-50 dark:bg-red-950/20 dark:border-red-900/30">
      <AlertTriangle class="size-4" />
      <AlertTitle class="text-red-700 dark:text-red-400">
        {{ siswaDiBawahThreshold.length }} Siswa di Bawah Batas Minimum Kehadiran ({{ THRESHOLD }}%)
      </AlertTitle>
      <AlertDescription class="text-red-600 dark:text-red-500 text-sm mt-1">
        <div class="flex flex-wrap gap-1.5 mt-2">
          <Badge
            v-for="s in siswaDiBawahThreshold"
            :key="s.id"
            variant="outline"
            class="border-red-300 text-red-700 dark:border-red-800 dark:text-red-400 text-xs"
          >
            {{ s.nama }} ({{ getPercentage(s) }}%)
          </Badge>
        </div>
      </AlertDescription>
    </Alert>

    <!-- Filters -->
    <div class="flex flex-wrap items-end gap-3">
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Semester</Label>
        <Select v-model="selectedSemester">
          <SelectTrigger id="select-semester" class="w-[140px]">
            <SelectValue placeholder="Pilih Semester" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="1">Semester 1</SelectItem>
            <SelectItem value="2">Semester 2</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Kelas</Label>
        <Select v-model="selectedKelas">
          <SelectTrigger id="select-kelas-kehadiran" class="w-[140px]">
            <SelectValue placeholder="Semua Kelas" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="semua">Semua Kelas</SelectItem>
            <SelectItem v-for="k in kelasList" :key="k" :value="k">{{ k }}</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div class="flex-1 min-w-[180px] relative">
        <Label class="text-xs text-muted-foreground">Cari Siswa</Label>
        <div class="relative mt-1.5">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
          <Input
            id="search-kehadiran"
            v-model="searchQuery"
            type="text"
            placeholder="Nama / NISN / Kelas..."
            class="pl-9 h-9"
            @input="currentPage = 1"
          />
        </div>
      </div>
    </div>

    <!-- Table -->
    <Card class="overflow-hidden">
      <Table>
        <TableHeader>
          <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
            <TableHead class="font-semibold cursor-pointer select-none" @click="toggleSort('nama')">
              <div class="flex items-center gap-1">
                Nama Siswa
                <component :is="sortField === 'nama' && sortDir === 'desc' ? ChevronDown : ChevronUp"
                  :class="['size-3.5', sortField === 'nama' ? 'text-primary' : 'text-muted-foreground/50']" />
              </div>
            </TableHead>
            <TableHead class="font-semibold cursor-pointer select-none" @click="toggleSort('kelas')">
              <div class="flex items-center gap-1">
                Kelas
                <component :is="sortField === 'kelas' && sortDir === 'desc' ? ChevronDown : ChevronUp"
                  :class="['size-3.5', sortField === 'kelas' ? 'text-primary' : 'text-muted-foreground/50']" />
              </div>
            </TableHead>
            <TableHead class="font-semibold text-center">Hadir</TableHead>
            <TableHead class="font-semibold text-center">Terlambat</TableHead>
            <TableHead class="font-semibold text-center">Izin</TableHead>
            <TableHead class="font-semibold text-center">Sakit</TableHead>
            <TableHead class="font-semibold text-center">Alpa</TableHead>
            <TableHead class="font-semibold cursor-pointer select-none" @click="toggleSort('persen')">
              <div class="flex items-center gap-1">
                % Hadir
                <component :is="sortField === 'persen' && sortDir === 'desc' ? ChevronDown : ChevronUp"
                  :class="['size-3.5', sortField === 'persen' ? 'text-primary' : 'text-muted-foreground/50']" />
              </div>
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="isLoading">
            <TableRow v-for="(i, index) in 8" :key="`skel-${i}`">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
              <TableCell v-for="j in 10" :key="j"><Skeleton class="h-5 w-full" /></TableCell>
            </TableRow>
          </template>

          <template v-else>
            <TableRow
              v-for="(siswa, idx) in paginatedData"
              :key="siswa.id"
              :class="[
                'hover:bg-muted/30 transition-colors',
                getPercentage(siswa) < THRESHOLD ? 'bg-red-50/50 dark:bg-red-950/10' : ''
              ]"
            >
              <TableCell class="text-muted-foreground text-sm">{{ (currentPage - 1) * itemsPerPage + idx + 1 }}</TableCell>
              <TableCell>
                <div class="font-medium text-sm">{{ siswa.nama }}</div>
                <div class="text-xs text-muted-foreground">{{ siswa.nisn }}</div>
              </TableCell>
              <TableCell>
                <Badge variant="outline" class="text-xs font-normal">{{ siswa.kelas }}</Badge>
              </TableCell>
              <TableCell class="text-center font-semibold text-green-600 dark:text-green-400">{{ siswa.hadir }}</TableCell>
              <TableCell class="text-center font-semibold text-yellow-600 dark:text-yellow-400">{{ siswa.terlambat }}</TableCell>
              <TableCell class="text-center font-semibold text-indigo-600 dark:text-indigo-400">{{ siswa.izin }}</TableCell>
              <TableCell class="text-center font-semibold text-blue-600 dark:text-blue-400">{{ siswa.sakit }}</TableCell>
              <TableCell class="text-center font-semibold text-red-600 dark:text-red-400">{{ siswa.alpa }}</TableCell>
              <TableCell>
                <div class="flex items-center gap-2 min-w-[100px]">
                  <div class="flex-1 h-1.5 rounded-full bg-muted overflow-hidden">
                    <div
                      :class="['h-full rounded-full transition-all', getProgressColor(getPercentage(siswa))]"
                      :style="{ width: `${getPercentage(siswa)}%` }"
                    />
                  </div>
                  <Badge
                    :class="[
                      'text-xs font-semibold shrink-0',
                      getPercentage(siswa) >= 90 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' :
                      getPercentage(siswa) >= THRESHOLD ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400' :
                      'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'
                    ]"
                  >
                    {{ getPercentage(siswa) }}%
                  </Badge>
                </div>
              </TableCell>
            </TableRow>

            <TableRow v-if="paginatedData.length === 0">
              <TableCell colspan="10" class="h-32 text-center text-muted-foreground">
                <div class="flex flex-col items-center justify-center gap-2">
                  <Search class="size-8 text-muted-foreground/40" />
                  <p class="text-sm">Tidak ada siswa ditemukan.</p>
                </div>
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </Card>

    <!-- Pagination -->
    <div class="flex items-center justify-between text-sm text-muted-foreground" v-if="filteredData.length > 0">
      <span>Menampilkan {{ paginatedData.length }} dari {{ filteredData.length }} siswa</span>
      <div class="flex items-center gap-1">
        <Button
          id="btn-prev-kehadiran"
          variant="outline"
          size="sm"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >Prev</Button>
        <Button
          v-for="p in totalPages"
          :key="p"
          :variant="p === currentPage ? 'default' : 'outline'"
          size="sm"
          @click="currentPage = p"
        >{{ p }}</Button>
        <Button
          id="btn-next-kehadiran"
          variant="outline"
          size="sm"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >Next</Button>
      </div>
    </div>
  </div>
</template>
