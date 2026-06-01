<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  BookOpen,
  Download,
  Printer,
  GraduationCap,
  TrendingUp,
  TrendingDown,
  Users,
  Award,
  Search,
  ChevronUp,
  ChevronDown,
  BarChart3,
} from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardDescription,
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
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs'
import { Separator } from '@/components/ui/separator'

const mockKelasData = [
  { id: 1, kelas: 'X IPA 1', waliKelas: 'Pak Budi, S.Pd', totalSiswa: 32, lulus: 30, tidakLulus: 2, rataNilai: 81.2, kehadiran: 93 },
  { id: 2, kelas: 'X IPA 2', waliKelas: 'Bu Rina, M.Pd', totalSiswa: 30, lulus: 28, tidakLulus: 2, rataNilai: 79.5, kehadiran: 91 },
  { id: 3, kelas: 'XI IPA 1', waliKelas: 'Bu Sari Dewi, S.Pd', totalSiswa: 35, lulus: 33, tidakLulus: 2, rataNilai: 83.7, kehadiran: 95 },
  { id: 4, kelas: 'XI IPA 2', waliKelas: 'Pak Rahmat, M.Pd', totalSiswa: 34, lulus: 31, tidakLulus: 3, rataNilai: 77.4, kehadiran: 88 },
  { id: 5, kelas: 'XI IPS 1', waliKelas: 'Bu Laila, S.Pd', totalSiswa: 33, lulus: 32, tidakLulus: 1, rataNilai: 80.1, kehadiran: 90 },
  { id: 6, kelas: 'XII IPA 1', waliKelas: 'Pak Hasan, M.Pd', totalSiswa: 31, lulus: 31, tidakLulus: 0, rataNilai: 85.6, kehadiran: 97 },
  { id: 7, kelas: 'XII IPS 1', waliKelas: 'Bu Dewi, S.Pd', totalSiswa: 29, lulus: 27, tidakLulus: 2, rataNilai: 78.9, kehadiran: 89 },
]

const mockMapelData = [
  { mapel: 'Matematika', avg: 79.2, tertinggi: 98, terendah: 55, tuntas: 78 },
  { mapel: 'Fisika', avg: 77.8, tertinggi: 95, terendah: 52, tuntas: 72 },
  { mapel: 'Kimia', avg: 80.1, tertinggi: 97, terendah: 58, tuntas: 81 },
  { mapel: 'Biologi', avg: 82.4, tertinggi: 99, terendah: 61, tuntas: 85 },
  { mapel: 'Bahasa Indonesia', avg: 84.6, tertinggi: 98, terendah: 64, tuntas: 92 },
  { mapel: 'Bahasa Inggris', avg: 76.3, tertinggi: 94, terendah: 50, tuntas: 69 },
  { mapel: 'Sejarah', avg: 81.5, tertinggi: 96, terendah: 60, tuntas: 83 },
  { mapel: 'Ekonomi', avg: 79.9, tertinggi: 95, terendah: 57, tuntas: 78 },
]

const isLoading = ref(true)
const kelasData = ref([])
const mapelData = ref([])
const selectedSemester = ref('1')
const selectedTahunAjar = ref('2025/2026')
const searchQuery = ref('')
const sortField = ref('kelas')
const sortDir = ref('asc')

onMounted(() => {
  setTimeout(() => {
    kelasData.value = mockKelasData
    mapelData.value = mockMapelData
    isLoading.value = false
  }, 600)
})

const totalSiswa = computed(() => kelasData.value.reduce((s, k) => s + k.totalSiswa, 0))
const totalLulus = computed(() => kelasData.value.reduce((s, k) => s + k.lulus, 0))
const avgNilai = computed(() => {
  if (!kelasData.value.length) return 0
  return (kelasData.value.reduce((s, k) => s + k.rataNilai, 0) / kelasData.value.length).toFixed(1)
})
const avgKehadiran = computed(() => {
  if (!kelasData.value.length) return 0
  return Math.round(kelasData.value.reduce((s, k) => s + k.kehadiran, 0) / kelasData.value.length)
})

const filteredKelas = computed(() => {
  let data = kelasData.value.filter(k =>
    !searchQuery.value || k.kelas.toLowerCase().includes(searchQuery.value.toLowerCase()) || k.waliKelas.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
  return [...data].sort((a, b) => {
    let va = a[sortField.value], vb = b[sortField.value]
    if (typeof va === 'string') va = va.toLowerCase()
    if (typeof vb === 'string') vb = vb.toLowerCase()
    return sortDir.value === 'asc' ? (va > vb ? 1 : -1) : (va < vb ? 1 : -1)
  })
})

function toggleSort(field) {
  if (sortField.value === field) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  else { sortField.value = field; sortDir.value = 'asc' }
}

function sortIcon(field) {
  return sortField.value === field && sortDir.value === 'desc' ? ChevronDown : ChevronUp
}
function sortIconClass(field) {
  return sortField.value === field ? 'text-primary' : 'text-muted-foreground/40'
}
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <BookOpen class="size-6 text-primary" />
          Laporan Akademik
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Rekapitulasi nilai, kelulusan, dan pencapaian akademik seluruh kelas.</p>
      </div>
      <div class="flex gap-2 shrink-0">
        <Button variant="outline" size="sm" class="gap-2"><Printer class="size-4" />Cetak</Button>
        <Button size="sm" class="gap-2"><Download class="size-4" />Ekspor</Button>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 items-end">
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Tahun Ajaran</Label>
        <Select v-model="selectedTahunAjar">
          <SelectTrigger class="w-[140px]"><SelectValue /></SelectTrigger>
          <SelectContent>
            <SelectItem value="2025/2026">2025/2026</SelectItem>
            <SelectItem value="2024/2025">2024/2025</SelectItem>
          </SelectContent>
        </Select>
      </div>
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Semester</Label>
        <Select v-model="selectedSemester">
          <SelectTrigger class="w-[130px]"><SelectValue /></SelectTrigger>
          <SelectContent>
            <SelectItem value="1">Semester 1</SelectItem>
            <SelectItem value="2">Semester 2</SelectItem>
          </SelectContent>
        </Select>
      </div>
      <div class="flex-1 min-w-[180px]">
        <Label class="text-xs text-muted-foreground">Cari Kelas</Label>
        <div class="relative mt-1.5">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
          <Input v-model="searchQuery" placeholder="Nama kelas / wali kelas..." class="pl-9 h-9" />
        </div>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Siswa</span>
          <div class="p-1.5 bg-muted rounded-lg"><Users class="size-4 text-muted-foreground" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold">{{ totalSiswa }}</div>
        <p class="text-xs text-muted-foreground mt-1">Di {{ kelasData.length }} kelas</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Rata-rata Nilai</span>
          <div class="p-1.5 bg-primary/10 rounded-lg"><BarChart3 class="size-4 text-primary" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold text-primary">{{ avgNilai }}</div>
        <p class="text-xs text-muted-foreground mt-1">Seluruh mata pelajaran</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Lulus KKM</span>
          <div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg"><Award class="size-4 text-green-600" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold text-green-600 dark:text-green-400">{{ totalLulus }}</div>
        <p class="text-xs text-muted-foreground mt-1">Siswa tuntas</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Kehadiran</span>
          <div class="p-1.5 bg-blue-50 dark:bg-blue-950/40 rounded-lg"><GraduationCap class="size-4 text-blue-600" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ avgKehadiran }}%</div>
        <p class="text-xs text-muted-foreground mt-1">Rata-rata kehadiran</p>
      </Card>
    </div>

    <!-- Tabs -->
    <Tabs default-value="kelas">
      <TabsList>
        <TabsTrigger value="kelas">Per Kelas</TabsTrigger>
        <TabsTrigger value="mapel">Per Mata Pelajaran</TabsTrigger>
      </TabsList>

      <!-- Per Kelas -->
      <TabsContent value="kelas" class="mt-4">
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="cursor-pointer select-none font-semibold" @click="toggleSort('kelas')">
                  <div class="flex items-center gap-1">Kelas <component :is="sortIcon('kelas')" :class="['size-3', sortIconClass('kelas')]" /></div>
                </TableHead>
                <TableHead class="font-semibold">Wali Kelas</TableHead>
                <TableHead class="font-semibold text-center">Total Siswa</TableHead>
                <TableHead class="font-semibold text-center">Lulus</TableHead>
                <TableHead class="font-semibold text-center">Tidak Lulus</TableHead>
                <TableHead class="cursor-pointer select-none font-semibold text-center" @click="toggleSort('rataNilai')">
                  <div class="flex items-center justify-center gap-1">Rata² Nilai <component :is="sortIcon('rataNilai')" :class="['size-3', sortIconClass('rataNilai')]" /></div>
                </TableHead>
                <TableHead class="cursor-pointer select-none font-semibold text-center" @click="toggleSort('kehadiran')">
                  <div class="flex items-center justify-center gap-1">Kehadiran <component :is="sortIcon('kehadiran')" :class="['size-3', sortIconClass('kehadiran')]" /></div>
                </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading">
                <TableRow v-for="i in 6" :key="i">
                  <TableCell v-for="j in 7" :key="j"><Skeleton class="h-5 w-full" /></TableCell>
                </TableRow>
              </template>
              <template v-else>
                <TableRow v-for="k in filteredKelas" :key="k.id" class="hover:bg-muted/30 transition-colors">
                  <TableCell class="font-semibold text-sm">{{ k.kelas }}</TableCell>
                  <TableCell class="text-sm text-muted-foreground">{{ k.waliKelas }}</TableCell>
                  <TableCell class="text-center font-medium">{{ k.totalSiswa }}</TableCell>
                  <TableCell class="text-center font-semibold text-green-600 dark:text-green-400">{{ k.lulus }}</TableCell>
                  <TableCell class="text-center font-semibold text-red-600 dark:text-red-400">{{ k.tidakLulus }}</TableCell>
                  <TableCell class="text-center">
                    <Badge :class="k.rataNilai >= 80 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : k.rataNilai >= 75 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'">
                      {{ k.rataNilai }}
                    </Badge>
                  </TableCell>
                  <TableCell class="text-center">
                    <Badge :class="k.kehadiran >= 90 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'">
                      {{ k.kehadiran }}%
                    </Badge>
                  </TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>

      <!-- Per Mapel -->
      <TabsContent value="mapel" class="mt-4">
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold">Mata Pelajaran</TableHead>
                <TableHead class="font-semibold text-center">Rata-rata</TableHead>
                <TableHead class="font-semibold text-center">Nilai Tertinggi</TableHead>
                <TableHead class="font-semibold text-center">Nilai Terendah</TableHead>
                <TableHead class="font-semibold text-center">% Tuntas KKM</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading">
                <TableRow v-for="i in 6" :key="i">
                  <TableCell v-for="j in 5" :key="j"><Skeleton class="h-5 w-full" /></TableCell>
                </TableRow>
              </template>
              <template v-else>
                <TableRow v-for="m in mapelData" :key="m.mapel" class="hover:bg-muted/30 transition-colors">
                  <TableCell class="font-medium text-sm">{{ m.mapel }}</TableCell>
                  <TableCell class="text-center font-bold" :class="m.avg >= 80 ? 'text-green-600 dark:text-green-400' : m.avg >= 75 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400'">{{ m.avg }}</TableCell>
                  <TableCell class="text-center text-green-600 dark:text-green-400 font-semibold">{{ m.tertinggi }}</TableCell>
                  <TableCell class="text-center text-red-600 dark:text-red-400 font-semibold">{{ m.terendah }}</TableCell>
                  <TableCell class="text-center">
                    <div class="flex items-center justify-center gap-2">
                      <div class="w-20 h-1.5 bg-muted rounded-full overflow-hidden">
                        <div class="h-full rounded-full" :class="m.tuntas >= 80 ? 'bg-green-500' : m.tuntas >= 70 ? 'bg-yellow-500' : 'bg-red-500'" :style="`width:${m.tuntas}%`" />
                      </div>
                      <span class="text-sm font-semibold">{{ m.tuntas }}%</span>
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
