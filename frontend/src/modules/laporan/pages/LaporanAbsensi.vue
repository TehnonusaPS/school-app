<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Calendar as CalendarIcon,
  Search,
  Download,
  FileText,
  Users,
  UserCheck,
  UserX,
  Clock,
  TrendingUp,
  Filter,
  Printer,
  RefreshCw,
} from 'lucide-vue-next'
import { getRecapData } from '@/services/api/absensi'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar'
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
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs'
import { Separator } from '@/components/ui/separator'
import { today, getLocalTimeZone, startOfMonth, endOfMonth } from '@internationalized/date'

const startDate = ref()
const endDate = ref()
const selectedStatus = ref('semua')
const selectedKelas = ref('semua')
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 10
const isRefreshing = ref(false)

const recapData = ref([])
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

function displayDate(dateObj) {
  const str = formatDateStr(dateObj)
  if (!str) return 'Pilih Tanggal'
  const [y, m, d] = str.split('-')
  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']
  return `${d} ${months[parseInt(m)-1]} ${y}`
}

async function loadData() {
  isLoading.value = true
  try {
    const startStr = formatDateStr(startDate.value)
    const endStr = formatDateStr(endDate.value)
    const data = await getRecapData(startStr, endStr)
    recapData.value = data
  } catch (err) {
    console.error(err)
  } finally {
    isLoading.value = false
  }
}

async function handleRefresh() {
  isRefreshing.value = true
  await loadData()
  isRefreshing.value = false
}

onMounted(() => {
  try {
    const tz = getLocalTimeZone()
    const now = today(tz)
    startDate.value = startOfMonth(now)
    endDate.value = endOfMonth(now)
  } catch (e) {
    startDate.value = new Date()
    endDate.value = new Date()
  }
  loadData()
})

const kelasList = computed(() => {
  const set = new Set(recapData.value.map(d => d.kelas))
  return Array.from(set).sort()
})

const filteredData = computed(() => {
  return recapData.value.filter(item => {
    const matchStatus = selectedStatus.value === 'semua' || item.status === selectedStatus.value
    const matchKelas = selectedKelas.value === 'semua' || item.kelas === selectedKelas.value
    const matchSearch =
      !searchQuery.value ||
      item.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.kelas.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchStatus && matchKelas && matchSearch
  })
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredData.value.slice(start, start + itemsPerPage)
})

// Stats
const totalHadir = computed(() => recapData.value.filter(d => d.status === 'hadir').length)
const totalTerlambat = computed(() => recapData.value.filter(d => d.status === 'terlambat').length)
const totalIzinSakit = computed(() => recapData.value.filter(d => ['izin', 'sakit'].includes(d.status)).length)
const totalAlpa = computed(() => recapData.value.filter(d => d.status === 'alpa').length)
const persentaseKehadiran = computed(() => {
  const total = recapData.value.length
  if (total === 0) return 0
  return Math.round(((totalHadir.value + totalTerlambat.value) / total) * 100)
})

function getStatusLabel(status) {
  const labels = {
    hadir: 'Hadir',
    terlambat: 'Terlambat',
    izin: 'Izin',
    sakit: 'Sakit',
    alpa: 'Tanpa Keterangan',
  }
  return labels[status] || 'Belum Absen'
}

function getStatusClass(status) {
  return {
    hadir: 'bg-green-100 text-green-700 hover:bg-green-100 dark:bg-green-900/40 dark:text-green-400',
    terlambat: 'bg-yellow-100 text-yellow-700 hover:bg-yellow-100 dark:bg-yellow-900/40 dark:text-yellow-400',
    sakit: 'bg-blue-100 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-400',
    izin: 'bg-indigo-100 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-400',
    alpa: 'bg-red-100 text-red-700 hover:bg-red-100 dark:bg-red-900/40 dark:text-red-400',
  }[status] || 'bg-muted text-muted-foreground'
}

function handlePrint() {
  window.print()
}

// Ringkasan per siswa
const ringkasanSiswa = computed(() => {
  const map = {}
  recapData.value.forEach(item => {
    if (!map[item.nama]) {
      map[item.nama] = { nama: item.nama, kelas: item.kelas, hadir: 0, terlambat: 0, izin: 0, sakit: 0, alpa: 0, total: 0 }
    }
    map[item.nama].total++
    if (item.status === 'hadir') map[item.nama].hadir++
    else if (item.status === 'terlambat') map[item.nama].terlambat++
    else if (item.status === 'izin') map[item.nama].izin++
    else if (item.status === 'sakit') map[item.nama].sakit++
    else if (item.status === 'alpa') map[item.nama].alpa++
  })
  return Object.values(map)
})
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
          <FileText class="size-6 text-primary" />
          Laporan Absensi
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">
          Rekap dan analisis kehadiran siswa berdasarkan rentang waktu yang dipilih.
        </p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <Button id="btn-refresh-absensi" variant="outline" size="sm" class="gap-2" @click="handleRefresh" :disabled="isLoading">
          <RefreshCw :class="['size-4', isRefreshing && 'animate-spin']" />
          Perbarui
        </Button>
        <Button id="btn-print-absensi" variant="outline" size="sm" class="gap-2" @click="handlePrint">
          <Printer class="size-4" />
          Cetak
        </Button>
        <Button id="btn-export-absensi" size="sm" class="gap-2">
          <Download class="size-4" />
          Ekspor Excel
        </Button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-5">
      <Card class="p-4 hover:shadow-md transition-shadow col-span-1">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Kehadiran</span>
          <div class="p-1.5 bg-primary/10 rounded-lg">
            <TrendingUp class="size-4 text-primary" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-16 mb-1" />
        <div v-else class="text-3xl font-bold tracking-tight text-primary">{{ persentaseKehadiran }}%</div>
        <p class="text-xs text-muted-foreground mt-1">Rata-rata kehadiran</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Hadir</span>
          <div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg">
            <UserCheck class="size-4 text-green-600" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight text-green-600 dark:text-green-400">{{ totalHadir }}</div>
        <p class="text-xs text-muted-foreground mt-1">Tepat waktu</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Terlambat</span>
          <div class="p-1.5 bg-yellow-50 dark:bg-yellow-950/40 rounded-lg">
            <Clock class="size-4 text-yellow-600" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight text-yellow-600 dark:text-yellow-400">{{ totalTerlambat }}</div>
        <p class="text-xs text-muted-foreground mt-1">Tidak tepat waktu</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Izin/Sakit</span>
          <div class="p-1.5 bg-blue-50 dark:bg-blue-950/40 rounded-lg">
            <Users class="size-4 text-blue-600" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight text-blue-600 dark:text-blue-400">{{ totalIzinSakit }}</div>
        <p class="text-xs text-muted-foreground mt-1">Ada keterangan</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Alpa</span>
          <div class="p-1.5 bg-red-50 dark:bg-red-950/40 rounded-lg">
            <UserX class="size-4 text-red-600" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight text-red-600 dark:text-red-400">{{ totalAlpa }}</div>
        <p class="text-xs text-muted-foreground mt-1">Tanpa keterangan</p>
      </Card>
    </div>

    <!-- Filters -->
    <Card class="overflow-hidden">
      <CardHeader class="py-3 px-4 border-b bg-muted/30">
        <div class="flex items-center gap-2">
          <Filter class="size-4 text-muted-foreground" />
          <CardTitle class="text-sm font-medium">Filter Data</CardTitle>
        </div>
      </CardHeader>
      <CardContent class="py-3 px-4">
        <div class="flex flex-wrap items-end gap-3">
          <!-- Start Date -->
          <div class="flex flex-col gap-1.5">
            <Label class="text-xs text-muted-foreground">Dari Tanggal</Label>
            <Popover>
              <PopoverTrigger as-child>
                <Button
                  id="btn-start-date"
                  variant="outline"
                  :class="['w-[155px] justify-start text-left font-normal text-sm', !startDate && 'text-muted-foreground']"
                >
                  <CalendarIcon class="mr-2 h-3.5 w-3.5" />
                  <span>{{ displayDate(startDate) }}</span>
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0" align="start">
                <Calendar v-model="startDate" />
              </PopoverContent>
            </Popover>
          </div>

          <!-- End Date -->
          <div class="flex flex-col gap-1.5">
            <Label class="text-xs text-muted-foreground">Sampai Tanggal</Label>
            <Popover>
              <PopoverTrigger as-child>
                <Button
                  id="btn-end-date"
                  variant="outline"
                  :class="['w-[155px] justify-start text-left font-normal text-sm', !endDate && 'text-muted-foreground']"
                >
                  <CalendarIcon class="mr-2 h-3.5 w-3.5" />
                  <span>{{ displayDate(endDate) }}</span>
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0" align="start">
                <Calendar v-model="endDate" />
              </PopoverContent>
            </Popover>
          </div>

          <!-- Kelas Filter -->
          <div class="flex flex-col gap-1.5">
            <Label class="text-xs text-muted-foreground">Kelas</Label>
            <Select v-model="selectedKelas">
              <SelectTrigger id="select-kelas" class="w-[140px]">
                <SelectValue placeholder="Semua Kelas" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Kelas</SelectItem>
                <SelectItem v-for="k in kelasList" :key="k" :value="k">{{ k }}</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Status Filter -->
          <div class="flex flex-col gap-1.5">
            <Label class="text-xs text-muted-foreground">Status</Label>
            <Select v-model="selectedStatus">
              <SelectTrigger id="select-status" class="w-[150px]">
                <SelectValue placeholder="Semua Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Status</SelectItem>
                <SelectItem value="hadir">Hadir</SelectItem>
                <SelectItem value="terlambat">Terlambat</SelectItem>
                <SelectItem value="izin">Izin</SelectItem>
                <SelectItem value="sakit">Sakit</SelectItem>
                <SelectItem value="alpa">Tanpa Keterangan</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Search -->
          <div class="flex-1 min-w-[180px] relative">
            <Label class="text-xs text-muted-foreground">Cari</Label>
            <div class="relative mt-1.5">
              <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
              <Input
                id="search-absensi"
                v-model="searchQuery"
                type="text"
                placeholder="Nama siswa / kelas..."
                class="pl-9 h-9"
                @input="currentPage = 1"
              />
            </div>
          </div>

          <Button id="btn-filter-absensi" @click="loadData" class="gap-2 mt-auto">
            <Search class="size-4" />
            Tampilkan
          </Button>
        </div>
      </CardContent>
    </Card>

    <!-- Tabs: Detail & Ringkasan -->
    <Tabs default-value="detail">
      <TabsList class="w-full justify-start">
        <TabsTrigger value="detail">Riwayat Detail</TabsTrigger>
        <TabsTrigger value="ringkasan">Ringkasan per Siswa</TabsTrigger>
      </TabsList>

      <!-- Detail Tab -->
      <TabsContent value="detail" class="mt-4">
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[120px]">Tanggal</TableHead>
                <TableHead class="font-semibold">Nama Siswa</TableHead>
                <TableHead class="font-semibold">Kelas</TableHead>
                <TableHead class="font-semibold text-center">Jam Masuk</TableHead>
                <TableHead class="font-semibold text-center">Jam Keluar</TableHead>
                <TableHead class="font-semibold">Status</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading">
                <TableRow v-for="i in 8" :key="`skel-${i}`">
                  <TableCell><Skeleton class="h-5 w-24" /></TableCell>
                  <TableCell><Skeleton class="h-5 w-36" /></TableCell>
                  <TableCell><Skeleton class="h-5 w-20" /></TableCell>
                  <TableCell><Skeleton class="h-5 w-16 mx-auto" /></TableCell>
                  <TableCell><Skeleton class="h-5 w-16 mx-auto" /></TableCell>
                  <TableCell><Skeleton class="h-6 w-22 rounded-full" /></TableCell>
                </TableRow>
              </template>

              <template v-else>
                <TableRow
                  v-for="log in paginatedData"
                  :key="log.id"
                  class="hover:bg-muted/30 transition-colors"
                >
                  <TableCell class="text-sm text-muted-foreground">{{ log.tanggal }}</TableCell>
                  <TableCell class="font-medium text-sm">{{ log.nama }}</TableCell>
                  <TableCell>
                    <Badge variant="outline" class="text-xs font-normal">{{ log.kelas }}</Badge>
                  </TableCell>
                  <TableCell class="text-center text-sm font-mono">{{ log.jamMasuk ?? '-' }}</TableCell>
                  <TableCell class="text-center text-sm font-mono">{{ log.jamKeluar ?? '-' }}</TableCell>
                  <TableCell>
                    <Badge variant="secondary" :class="getStatusClass(log.status)">
                      {{ getStatusLabel(log.status) }}
                    </Badge>
                  </TableCell>
                </TableRow>

                <TableRow v-if="paginatedData.length === 0">
                  <TableCell colspan="6" class="h-32 text-center text-muted-foreground">
                    <div class="flex flex-col items-center justify-center gap-2">
                      <CalendarIcon class="size-8 text-muted-foreground/40" />
                      <p class="text-sm">Tidak ada data pada filter yang dipilih.</p>
                    </div>
                  </TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>

        <!-- Pagination -->
        <div class="flex items-center justify-between text-sm text-muted-foreground mt-3" v-if="filteredData.length > 0">
          <span>Menampilkan {{ paginatedData.length }} dari {{ filteredData.length }} data</span>
          <div class="flex items-center gap-1">
            <Button
              id="btn-prev-absensi"
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
              id="btn-next-absensi"
              variant="outline"
              size="sm"
              :disabled="currentPage === totalPages"
              @click="currentPage++"
            >Next</Button>
          </div>
        </div>
      </TabsContent>

      <!-- Ringkasan Tab -->
      <TabsContent value="ringkasan" class="mt-4">
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold">Nama Siswa</TableHead>
                <TableHead class="font-semibold">Kelas</TableHead>
                <TableHead class="font-semibold text-center">Hadir</TableHead>
                <TableHead class="font-semibold text-center">Terlambat</TableHead>
                <TableHead class="font-semibold text-center">Izin</TableHead>
                <TableHead class="font-semibold text-center">Sakit</TableHead>
                <TableHead class="font-semibold text-center">Alpa</TableHead>
                <TableHead class="font-semibold text-center">% Hadir</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading">
                <TableRow v-for="i in 5" :key="`sum-skel-${i}`">
                  <TableCell v-for="j in 8" :key="j"><Skeleton class="h-5 w-full" /></TableCell>
                </TableRow>
              </template>
              <template v-else>
                <TableRow
                  v-for="siswa in ringkasanSiswa"
                  :key="siswa.nama"
                  class="hover:bg-muted/30 transition-colors"
                >
                  <TableCell class="font-medium text-sm">{{ siswa.nama }}</TableCell>
                  <TableCell>
                    <Badge variant="outline" class="text-xs font-normal">{{ siswa.kelas }}</Badge>
                  </TableCell>
                  <TableCell class="text-center">
                    <span class="text-green-600 dark:text-green-400 font-semibold">{{ siswa.hadir }}</span>
                  </TableCell>
                  <TableCell class="text-center">
                    <span class="text-yellow-600 dark:text-yellow-400 font-semibold">{{ siswa.terlambat }}</span>
                  </TableCell>
                  <TableCell class="text-center">
                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ siswa.izin }}</span>
                  </TableCell>
                  <TableCell class="text-center">
                    <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ siswa.sakit }}</span>
                  </TableCell>
                  <TableCell class="text-center">
                    <span class="text-red-600 dark:text-red-400 font-semibold">{{ siswa.alpa }}</span>
                  </TableCell>
                  <TableCell class="text-center">
                    <Badge
                      :class="[
                        (((siswa.hadir + siswa.terlambat) / siswa.total) * 100) >= 80
                          ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
                          : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'
                      ]"
                    >
                      {{ Math.round(((siswa.hadir + siswa.terlambat) / siswa.total) * 100) }}%
                    </Badge>
                  </TableCell>
                </TableRow>
                <TableRow v-if="ringkasanSiswa.length === 0">
                  <TableCell colspan="8" class="h-32 text-center text-muted-foreground">
                    <div class="flex flex-col items-center justify-center gap-2">
                      <Users class="size-8 text-muted-foreground/40" />
                      <p class="text-sm">Tidak ada data ringkasan.</p>
                    </div>
                  </TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>
    </Tabs>
  </div>
</template>
