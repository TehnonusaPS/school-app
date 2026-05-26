<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Calendar as CalendarIcon,
  Search,
  Download,
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
import { Card } from '@/components/ui/card'
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

const startDate = ref()
const endDate = ref()
const selectedStatus = ref('semua')
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

const recapData = ref([])
const isLoading = ref(true)

function formatDateStr(dateObj) {
  if (!dateObj) return ''
  // Handle CalendarDate object from shadcn
  if (dateObj.year) {
    return `${dateObj.year}-${String(dateObj.month).padStart(2, '0')}-${String(dateObj.day).padStart(2, '0')}`
  }
  // Handle native Date
  if (dateObj instanceof Date) {
    return dateObj.toISOString().split('T')[0]
  }
  return String(dateObj)
}

function displayDate(dateObj) {
  const str = formatDateStr(dateObj)
  if (!str) return 'Pilih Tanggal'
  return str
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

import { today, getLocalTimeZone, startOfMonth, endOfMonth } from '@internationalized/date'

onMounted(() => {
  try {
    const tz = getLocalTimeZone()
    const now = today(tz)
    startDate.value = startOfMonth(now)
    endDate.value = endOfMonth(now)
  } catch (e) {
    // fallback if internationalized/date fails
    startDate.value = new Date()
    endDate.value = new Date()
  }
  loadData()
})

const filteredData = computed(() => {
  return recapData.value.filter(item => {
    const matchStatus = selectedStatus.value === 'semua' || item.status === selectedStatus.value
    const matchSearch =
      !searchQuery.value ||
      item.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.kelas.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchStatus && matchSearch
  })
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredData.value.slice(start, start + itemsPerPage)
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
  <div class="space-y-6 animate-in fade-in duration-300">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">
          Rekap Data Absensi
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">
          Pantau riwayat kehadiran siswa secara fleksibel berdasarkan rentang waktu.
        </p>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap items-end gap-3 bg-muted/30 p-4 rounded-xl border">
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Dari Tanggal</Label>
        <Popover>
          <PopoverTrigger as-child>
            <Button
              variant="outline"
              :class="['w-[150px] justify-start text-left font-normal', !startDate && 'text-muted-foreground']"
            >
              <CalendarIcon class="mr-2 h-4 w-4" />
              <span>{{ displayDate(startDate) }}</span>
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0" align="start">
            <Calendar v-model="startDate" />
          </PopoverContent>
        </Popover>
      </div>
      
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Sampai Tanggal</Label>
        <Popover>
          <PopoverTrigger as-child>
            <Button
              variant="outline"
              :class="['w-[150px] justify-start text-left font-normal', !endDate && 'text-muted-foreground']"
            >
              <CalendarIcon class="mr-2 h-4 w-4" />
              <span>{{ displayDate(endDate) }}</span>
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0" align="start">
            <Calendar v-model="endDate" />
          </PopoverContent>
        </Popover>
      </div>
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Status</Label>
        <Select v-model="selectedStatus">
          <SelectTrigger class="w-[140px]">
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

      <div class="flex-1 min-w-[180px] relative lg:ml-auto mt-2 lg:mt-0">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground pointer-events-none" />
        <Input
          v-model="searchQuery"
          type="text"
          placeholder="Cari siswa/kelas..."
          class="pl-9"
        />
      </div>

      <Button @click="loadData" variant="default" class="shrink-0 gap-2">
        <Search class="size-4" /> Filter
      </Button>
      <Button variant="outline" class="shrink-0 px-3" title="Download">
        <Download class="size-4" />
      </Button>
    </div>

    <!-- Table -->
    <Card class="overflow-hidden">
      <Table>
        <TableHeader>
          <TableRow class="bg-muted/50">
            <TableHead class="font-semibold">Tanggal</TableHead>
            <TableHead class="font-semibold">Nama Siswa &amp; Kelas</TableHead>
            <TableHead class="font-semibold">Jam Masuk</TableHead>
            <TableHead class="font-semibold">Jam Keluar</TableHead>
            <TableHead class="font-semibold">Status</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="isLoading">
            <TableRow v-for="i in 5" :key="`skel-${i}`">
              <TableCell><Skeleton class="h-5 w-24" /></TableCell>
              <TableCell><Skeleton class="h-5 w-40" /></TableCell>
              <TableCell><Skeleton class="h-5 w-16" /></TableCell>
              <TableCell><Skeleton class="h-5 w-16" /></TableCell>
              <TableCell><Skeleton class="h-6 w-20 rounded-full" /></TableCell>
            </TableRow>
          </template>
          
          <template v-else>
            <TableRow
              v-for="log in paginatedData"
              :key="log.id"
              class="hover:bg-muted/30 transition-colors"
            >
            <TableCell>
              <div class="font-medium text-sm">{{ log.tanggal }}</div>
            </TableCell>
            <TableCell>
              <div class="font-semibold text-sm">{{ log.nama }} <span class="text-muted-foreground font-normal">({{ log.kelas }})</span></div>
            </TableCell>
            <TableCell class="text-sm font-mono">{{ log.jamMasuk ?? '-' }}</TableCell>
            <TableCell class="text-sm font-mono">{{ log.jamKeluar ?? '-' }}</TableCell>
            <TableCell>
              <Badge
                :variant="log.status === 'hadir' ? 'default' : log.status === 'terlambat' ? 'destructive' : 'secondary'"
                :class="{
                  'bg-green-100 text-green-700 hover:bg-green-100 dark:bg-green-900/40 dark:text-green-400': log.status === 'hadir',
                  'bg-yellow-100 text-yellow-700 hover:bg-yellow-100 dark:bg-yellow-900/40 dark:text-yellow-400': log.status === 'terlambat',
                  'bg-blue-100 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-400': log.status === 'sakit',
                  'bg-indigo-100 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-400': log.status === 'izin',
                  'bg-red-100 text-red-700 hover:bg-red-100 dark:bg-red-900/40 dark:text-red-400': log.status === 'alpa',
                }"
              >
                {{ getStatusLabel(log.status) }}
              </Badge>
            </TableCell>
            </TableRow>

            <TableRow v-if="paginatedData.length === 0">
              <TableCell colspan="5" class="h-32 text-center text-muted-foreground">
                <div class="flex flex-col items-center justify-center gap-2">
                  <CalendarIcon class="size-8 text-muted-foreground/50" />
                  <p class="text-sm">Tidak ada rekap absensi pada rentang waktu ini.</p>
                </div>
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </Card>

    <!-- Pagination -->
    <div class="flex items-center justify-between text-sm text-muted-foreground" v-if="filteredData.length > 0">
      <span>Menampilkan {{ paginatedData.length }} dari {{ filteredData.length }} data</span>
      <div class="flex items-center gap-1">
        <Button
          variant="outline"
          size="sm"
          :disabled="currentPage === 1"
          @click="currentPage--"
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
          variant="outline"
          size="sm"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          Next
        </Button>
      </div>
    </div>
  </div>
</template>
