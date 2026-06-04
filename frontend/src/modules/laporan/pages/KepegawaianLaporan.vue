<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  UserCheck,
  Download,
  Printer,
  Users,
  Clock,
  AlertTriangle,
  CheckCircle2,
  Search,
  Calendar as CalendarIcon,
  ChevronUp,
  ChevronDown,
} from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Calendar } from '@/components/ui/calendar'
import { today, getLocalTimeZone, startOfMonth, endOfMonth } from '@internationalized/date'

const mockStaf = [
  { id: 1, nik: 'GTK001', nama: 'Pak Ahmad Siregar, S.Pd', jabatan: 'Guru Matematika', status: 'Tetap', hadir: 22, terlambat: 0, izin: 0, sakit: 0, alpa: 0 },
  { id: 2, nik: 'GTK002', nama: 'Bu Dewi Rahayu, M.Pd', jabatan: 'Guru B. Indonesia', status: 'Tetap', hadir: 21, terlambat: 1, izin: 1, sakit: 0, alpa: 0 },
  { id: 3, nik: 'GTK003', nama: 'Pak Rizky Pratama, S.Pd', jabatan: 'Guru Fisika', status: 'Honorer', hadir: 18, terlambat: 2, izin: 0, sakit: 2, alpa: 1 },
  { id: 4, nik: 'GTK004', nama: 'Bu Siti Nurhaliza, S.Pd', jabatan: 'Guru Biologi', status: 'Tetap', hadir: 22, terlambat: 0, izin: 0, sakit: 0, alpa: 0 },
  { id: 5, nik: 'GTK005', nama: 'Pak Hendra Wijaya', jabatan: 'Tenaga Administrasi', status: 'Tetap', hadir: 20, terlambat: 3, izin: 0, sakit: 0, alpa: 2 },
  { id: 6, nik: 'GTK006', nama: 'Bu Laila Sari, S.E', jabatan: 'Bendahara', status: 'Tetap', hadir: 22, terlambat: 0, izin: 0, sakit: 0, alpa: 0 },
  { id: 7, nik: 'GTK007', nama: 'Pak Doni Setiawan', jabatan: 'Guru Olahraga', status: 'Honorer', hadir: 16, terlambat: 4, izin: 2, sakit: 0, alpa: 3 },
  { id: 8, nik: 'GTK008', nama: 'Bu Farida Hanum, S.Pd', jabatan: 'Guru B. Inggris', status: 'Tetap', hadir: 22, terlambat: 0, izin: 0, sakit: 0, alpa: 0 },
]

const HARI_KERJA = 22

const isLoading = ref(true)
const stafData = ref([])
const searchQuery = ref('')
const selectedStatus = ref('semua')
const selectedBulan = ref('5')
const sortField = ref('nama')
const sortDir = ref('asc')
const currentPage = ref(1)
const itemsPerPage = 8
const startDate = ref()
const endDate = ref()

onMounted(() => {
  try {
    const tz = getLocalTimeZone()
    const now = today(tz)
    startDate.value = startOfMonth(now)
    endDate.value = endOfMonth(now)
  } catch(e) {}
  setTimeout(() => { stafData.value = mockStaf; isLoading.value = false }, 500)
})

function displayDate(dateObj) {
  if (!dateObj) return 'Pilih'
  const str = dateObj.year ? `${dateObj.year}-${String(dateObj.month).padStart(2,'0')}-${String(dateObj.day).padStart(2,'0')}` : ''
  if (!str) return 'Pilih'
  const [y, m, d] = str.split('-')
  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']
  return `${d} ${months[parseInt(m)-1]}`
}

function getPersen(s) { return Math.round(((s.hadir + s.terlambat) / HARI_KERJA) * 100) }

const totalHadir = computed(() => stafData.value.filter(s => getPersen(s) >= 90).length)
const totalBermasalah = computed(() => stafData.value.filter(s => s.alpa > 0).length)

const filteredData = computed(() => {
  let d = stafData.value.filter(s => {
    const matchStatus = selectedStatus.value === 'semua' || s.status === selectedStatus.value
    const matchSearch = !searchQuery.value || s.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) || s.jabatan.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchStatus && matchSearch
  })
  return [...d].sort((a,b) => {
    let va = sortField.value === 'persen' ? getPersen(a) : (a[sortField.value]??'')
    let vb = sortField.value === 'persen' ? getPersen(b) : (b[sortField.value]??'')
    if (typeof va==='string') va=va.toLowerCase()
    if (typeof vb==='string') vb=vb.toLowerCase()
    return sortDir.value==='asc'?(va>vb?1:-1):(va<vb?1:-1)
  })
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage))
const paginatedData = computed(() => filteredData.value.slice((currentPage.value-1)*itemsPerPage, currentPage.value*itemsPerPage))

function toggleSort(f) {
  if (sortField.value===f) sortDir.value=sortDir.value==='asc'?'desc':'asc'
  else { sortField.value=f; sortDir.value='asc' }
}

const bulanList = [
  { v: '1', l: 'Januari' }, { v: '2', l: 'Februari' }, { v: '3', l: 'Maret' },
  { v: '4', l: 'April' }, { v: '5', l: 'Mei' }, { v: '6', l: 'Juni' },
  { v: '7', l: 'Juli' }, { v: '8', l: 'Agustus' }, { v: '9', l: 'September' },
  { v: '10', l: 'Oktober' }, { v: '11', l: 'November' }, { v: '12', l: 'Desember' },
]
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <UserCheck class="size-6 text-primary" />
          Laporan Kepegawaian
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Laporan rekapitulasi data kehadiran dan status kepegawaian staf.</p>
      </div>
      <div class="flex gap-2">
        <Button variant="outline" size="sm" class="gap-2"><Printer class="size-4" />Cetak</Button>
        <Button size="sm" class="gap-2"><Download class="size-4" />Ekspor</Button>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 items-end">
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Bulan</Label>
        <Select v-model="selectedBulan">
          <SelectTrigger class="w-[130px]"><SelectValue /></SelectTrigger>
          <SelectContent><SelectItem v-for="b in bulanList" :key="b.v" :value="b.v">{{ b.l }}</SelectItem></SelectContent>
        </Select>
      </div>
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Status Kepegawaian</Label>
        <Select v-model="selectedStatus">
          <SelectTrigger class="w-[140px]"><SelectValue placeholder="Semua" /></SelectTrigger>
          <SelectContent>
            <SelectItem value="semua">Semua</SelectItem>
            <SelectItem value="Tetap">Tetap</SelectItem>
            <SelectItem value="Honorer">Honorer</SelectItem>
          </SelectContent>
        </Select>
      </div>
      <div class="flex-1 min-w-[180px]">
        <Label class="text-xs text-muted-foreground">Cari</Label>
        <div class="relative mt-1.5">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
          <Input v-model="searchQuery" placeholder="Nama / Jabatan..." class="pl-9 h-9" @input="currentPage=1" />
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Staf</span><div class="p-1.5 bg-primary/10 rounded-lg"><Users class="size-4 text-primary" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold text-primary">{{ stafData.length }}</div>
        <p class="text-xs text-muted-foreground mt-1">Guru & Tenaga Kependidikan</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Kehadiran Baik</span><div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg"><CheckCircle2 class="size-4 text-green-600" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold text-green-600 dark:text-green-400">{{ totalHadir }}</div>
        <p class="text-xs text-muted-foreground mt-1">≥ 90% kehadiran</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Hari Kerja</span><div class="p-1.5 bg-muted rounded-lg"><Clock class="size-4 text-muted-foreground" /></div></div>
        <div class="text-3xl font-bold">{{ HARI_KERJA }}</div>
        <p class="text-xs text-muted-foreground mt-1">Hari bulan ini</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Ada Pelanggaran</span><div class="p-1.5 bg-red-50 dark:bg-red-950/40 rounded-lg"><AlertTriangle class="size-4 text-red-600" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold text-red-600 dark:text-red-400">{{ totalBermasalah }}</div>
        <p class="text-xs text-muted-foreground mt-1">Memiliki alpa</p>
      </Card>
    </div>

    <!-- Alert -->
    <Alert v-if="!isLoading && totalBermasalah > 0" variant="destructive" class="border-red-200 bg-red-50 dark:bg-red-950/20 dark:border-red-900/30">
      <AlertTriangle class="size-4" />
      <AlertTitle class="text-red-700 dark:text-red-400">{{ totalBermasalah }} Staf Memiliki Ketidakhadiran Tanpa Keterangan</AlertTitle>
      <AlertDescription class="text-red-600 text-sm mt-1">
        <div class="flex flex-wrap gap-1.5 mt-2">
          <Badge v-for="s in stafData.filter(s=>s.alpa>0)" :key="s.id" variant="outline" class="border-red-300 text-red-700 dark:text-red-400 text-xs">
            {{ s.nama }} ({{ s.alpa }}x alpa)
          </Badge>
        </div>
      </AlertDescription>
    </Alert>

    <!-- Table -->
    <Card class="overflow-hidden">
      <Table>
        <TableHeader>
          <TableRow class="bg-muted/50">
            <TableHead class="font-semibold w-10">No</TableHead>
            <TableHead class="cursor-pointer select-none font-semibold" @click="toggleSort('nama')">
              <div class="flex items-center gap-1">Nama <component :is="sortField==='nama'&&sortDir==='desc'?ChevronDown:ChevronUp" :class="['size-3',sortField==='nama'?'text-primary':'text-muted-foreground/40']" /></div>
            </TableHead>
            <TableHead class="font-semibold">Jabatan</TableHead>
            <TableHead class="font-semibold text-center">Status</TableHead>
            <TableHead class="font-semibold text-center">Hadir</TableHead>
            <TableHead class="font-semibold text-center">Terlambat</TableHead>
            <TableHead class="font-semibold text-center">Izin</TableHead>
            <TableHead class="font-semibold text-center">Sakit</TableHead>
            <TableHead class="font-semibold text-center">Alpa</TableHead>
            <TableHead class="cursor-pointer select-none font-semibold text-center" @click="toggleSort('persen')">
              <div class="flex items-center justify-center gap-1">% <component :is="sortField==='persen'&&sortDir==='desc'?ChevronDown:ChevronUp" :class="['size-3',sortField==='persen'?'text-primary':'text-muted-foreground/40']" /></div>
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="isLoading"><TableRow v-for="i in 8" :key="i"><TableCell v-for="j in 10" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow></template>
          <template v-else>
            <TableRow v-for="(s,idx) in paginatedData" :key="s.id" :class="['hover:bg-muted/30 transition-colors', s.alpa>0?'bg-red-50/30 dark:bg-red-950/10':'']">
              <TableCell class="text-muted-foreground text-xs">{{ (currentPage-1)*itemsPerPage+idx+1 }}</TableCell>
              <TableCell>
                <div class="font-medium text-sm">{{ s.nama }}</div>
                <div class="text-xs text-muted-foreground">{{ s.nik }}</div>
              </TableCell>
              <TableCell class="text-sm text-muted-foreground">{{ s.jabatan }}</TableCell>
              <TableCell class="text-center"><Badge :class="s.status==='Tetap'?'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400':'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400'">{{ s.status }}</Badge></TableCell>
              <TableCell class="text-center font-semibold text-green-600 dark:text-green-400">{{ s.hadir }}</TableCell>
              <TableCell class="text-center font-semibold text-yellow-600 dark:text-yellow-400">{{ s.terlambat }}</TableCell>
              <TableCell class="text-center font-semibold text-indigo-600 dark:text-indigo-400">{{ s.izin }}</TableCell>
              <TableCell class="text-center font-semibold text-blue-600 dark:text-blue-400">{{ s.sakit }}</TableCell>
              <TableCell class="text-center font-semibold text-red-600 dark:text-red-400">{{ s.alpa }}</TableCell>
              <TableCell class="text-center">
                <Badge :class="getPersen(s)>=95?'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400':getPersen(s)>=80?'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400':'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'">{{ getPersen(s) }}%</Badge>
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </Card>

    <!-- Pagination -->
    <div class="flex items-center justify-between text-sm text-muted-foreground" v-if="filteredData.length > 0">
      <span>Menampilkan {{ paginatedData.length }} dari {{ filteredData.length }} staf</span>
      <div class="flex items-center gap-1">
        <Button variant="outline" size="sm" :disabled="currentPage===1" @click="currentPage--">Prev</Button>
        <Button v-for="p in totalPages" :key="p" :variant="p===currentPage?'default':'outline'" size="sm" @click="currentPage=p">{{ p }}</Button>
        <Button variant="outline" size="sm" :disabled="currentPage===totalPages" @click="currentPage++">Next</Button>
      </div>
    </div>
  </div>
</template>
