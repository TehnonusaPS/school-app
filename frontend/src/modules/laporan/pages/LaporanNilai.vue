<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  GraduationCap,
  Search,
  Download,
  Trophy,
  TrendingUp,
  AlertCircle,
  ChevronUp,
  ChevronDown,
  BookOpen,
  Printer,
  Star,
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
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs'
import { Separator } from '@/components/ui/separator'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip'

// Mock Data
const mataPelajaranList = ['Matematika', 'Fisika', 'Kimia', 'Biologi', 'Bahasa Indonesia', 'Bahasa Inggris', 'Sejarah', 'Ekonomi']

const mockNilaiSiswa = [
  { id: 1, nisn: '0051234567', nama: 'Ahmad Fadil', kelas: 'XI IPA 1', nilai: { Matematika: 88, Fisika: 82, Kimia: 90, Biologi: 85, 'Bahasa Indonesia': 88, 'Bahasa Inggris': 79 } },
  { id: 2, nisn: '0069876543', nama: 'Bunga Citra', kelas: 'XI IPA 1', nilai: { Matematika: 92, Fisika: 88, Kimia: 85, Biologi: 91, 'Bahasa Indonesia': 90, 'Bahasa Inggris': 87 } },
  { id: 3, nisn: '0054321987', nama: 'Cakra Khan', kelas: 'XI IPA 1', nilai: { Matematika: 72, Fisika: 68, Kimia: 74, Biologi: 70, 'Bahasa Indonesia': 75, 'Bahasa Inggris': 65 } },
  { id: 4, nisn: '0061122334', nama: 'Dian Sastro', kelas: 'XI IPA 1', nilai: { Matematika: 95, Fisika: 93, Kimia: 88, Biologi: 92, 'Bahasa Indonesia': 89, 'Bahasa Inggris': 91 } },
  { id: 5, nisn: '0055566778', nama: 'Elsa Novita', kelas: 'XI IPA 2', nilai: { Matematika: 80, Fisika: 75, Kimia: 82, Biologi: 78, 'Bahasa Indonesia': 82, 'Bahasa Inggris': 70 } },
  { id: 6, nisn: '0068899001', nama: 'Farhan Ramdan', kelas: 'XI IPA 2', nilai: { Matematika: 65, Fisika: 60, Kimia: 68, Biologi: 63, 'Bahasa Indonesia': 70, 'Bahasa Inggris': 55 } },
  { id: 7, nisn: '0052233445', nama: 'Gita Nirmala', kelas: 'XI IPA 2', nilai: { Matematika: 85, Fisika: 80, Kimia: 83, Biologi: 87, 'Bahasa Indonesia': 86, 'Bahasa Inggris': 82 } },
  { id: 8, nisn: '0067788990', nama: 'Hendra Saputra', kelas: 'XI IPA 1', nilai: { Matematika: 78, Fisika: 73, Kimia: 76, Biologi: 80, 'Bahasa Indonesia': 77, 'Bahasa Inggris': 72 } },
]

const KKM = 75 // Kriteria Ketuntasan Minimal

const isLoading = ref(true)
const nilaiData = ref([])
const searchQuery = ref('')
const selectedKelas = ref('semua')
const selectedMapel = ref('semua')
const selectedSemester = ref('1')
const sortField = ref('nama')
const sortDir = ref('asc')
const currentPage = ref(1)
const itemsPerPage = 8

onMounted(() => {
  setTimeout(() => {
    nilaiData.value = mockNilaiSiswa
    isLoading.value = false
  }, 700)
})

function getRataRata(siswa) {
  const mapelList = selectedMapel.value === 'semua'
    ? mataPelajaranList
    : [selectedMapel.value]
  const vals = mapelList.map(m => siswa.nilai[m] ?? 0).filter(v => v > 0)
  if (vals.length === 0) return 0
  return Math.round(vals.reduce((a, b) => a + b, 0) / vals.length)
}

function getPredikat(rata) {
  if (rata >= 90) return 'A'
  if (rata >= 80) return 'B'
  if (rata >= 70) return 'C'
  if (rata >= 60) return 'D'
  return 'E'
}

function getPredikatClass(rata) {
  if (rata >= 90) return 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
  if (rata >= 80) return 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400'
  if (rata >= 70) return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'
  return 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'
}

function getNilaiClass(val) {
  if (val === undefined || val === null) return 'text-muted-foreground'
  if (val >= 90) return 'text-green-600 dark:text-green-400 font-semibold'
  if (val >= KKM) return 'text-foreground'
  return 'text-red-600 dark:text-red-400 font-semibold'
}

const kelasList = computed(() => {
  const set = new Set(nilaiData.value.map(d => d.kelas))
  return Array.from(set).sort()
})

const filteredData = computed(() => {
  let data = nilaiData.value.filter(item => {
    const matchKelas = selectedKelas.value === 'semua' || item.kelas === selectedKelas.value
    const matchSearch =
      !searchQuery.value ||
      item.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.nisn.includes(searchQuery.value)
    return matchKelas && matchSearch
  })

  data = [...data].sort((a, b) => {
    let valA = sortField.value === 'rata' ? getRataRata(a) : (a[sortField.value] ?? '')
    let valB = sortField.value === 'rata' ? getRataRata(b) : (b[sortField.value] ?? '')
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

// Tampilkan kolom mata pelajaran yang relevan
const activeMapelList = computed(() =>
  selectedMapel.value === 'semua' ? mataPelajaranList : [selectedMapel.value]
)

// Stats
const avgNilai = computed(() => {
  if (nilaiData.value.length === 0) return 0
  return Math.round(nilaiData.value.reduce((sum, s) => sum + getRataRata(s), 0) / nilaiData.value.length)
})

const siswaLulus = computed(() => nilaiData.value.filter(s => getRataRata(s) >= KKM).length)
const siswaTidakLulus = computed(() => nilaiData.value.filter(s => getRataRata(s) < KKM).length)

// Peringkat
const peringkat = computed(() => {
  return [...nilaiData.value]
    .map(s => ({ ...s, rata: getRataRata(s) }))
    .sort((a, b) => b.rata - a.rata)
    .slice(0, 3)
})
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
          <GraduationCap class="size-6 text-primary" />
          Laporan Nilai Siswa
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">
          Rekapitulasi nilai akademis per mata pelajaran beserta rata-rata dan predikat ketuntasan.
        </p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <Button id="btn-print-nilai" variant="outline" size="sm" class="gap-2">
          <Printer class="size-4" />
          Cetak
        </Button>
        <Button id="btn-export-nilai" size="sm" class="gap-2">
          <Download class="size-4" />
          Ekspor Excel
        </Button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Rata-rata Kelas</span>
          <div class="p-1.5 bg-primary/10 rounded-lg">
            <TrendingUp class="size-4 text-primary" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold tracking-tight text-primary">{{ avgNilai }}</div>
        <p class="text-xs text-muted-foreground mt-1">Nilai rata-rata semua mapel</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Siswa</span>
          <div class="p-1.5 bg-muted rounded-lg">
            <BookOpen class="size-4 text-muted-foreground" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight">{{ nilaiData.length }}</div>
        <p class="text-xs text-muted-foreground mt-1">Terdaftar semester ini</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Tuntas KKM</span>
          <div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg">
            <Trophy class="size-4 text-green-600" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight text-green-600 dark:text-green-400">{{ siswaLulus }}</div>
        <p class="text-xs text-muted-foreground mt-1">Di atas KKM ({{ KKM }})</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Belum Tuntas</span>
          <div class="p-1.5 bg-red-50 dark:bg-red-950/40 rounded-lg">
            <AlertCircle class="size-4 text-red-600" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight text-red-600 dark:text-red-400">{{ siswaTidakLulus }}</div>
        <p class="text-xs text-muted-foreground mt-1">Memerlukan remedial</p>
      </Card>
    </div>

    <!-- Tabs -->
    <Tabs default-value="nilai">
      <TabsList class="w-full justify-start">
        <TabsTrigger value="nilai">Tabel Nilai</TabsTrigger>
        <TabsTrigger value="peringkat">Peringkat Kelas</TabsTrigger>
      </TabsList>

      <!-- Nilai Tab -->
      <TabsContent value="nilai" class="mt-4 space-y-4">
        <!-- Filters -->
        <div class="flex flex-wrap items-end gap-3">
          <div class="flex flex-col gap-1.5">
            <Label class="text-xs text-muted-foreground">Semester</Label>
            <Select v-model="selectedSemester">
              <SelectTrigger id="select-semester-nilai" class="w-[140px]">
                <SelectValue placeholder="Semester" />
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
              <SelectTrigger id="select-kelas-nilai" class="w-[140px]">
                <SelectValue placeholder="Semua Kelas" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Kelas</SelectItem>
                <SelectItem v-for="k in kelasList" :key="k" :value="k">{{ k }}</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="flex flex-col gap-1.5">
            <Label class="text-xs text-muted-foreground">Mata Pelajaran</Label>
            <Select v-model="selectedMapel">
              <SelectTrigger id="select-mapel-nilai" class="w-[180px]">
                <SelectValue placeholder="Semua Mapel" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Mapel</SelectItem>
                <SelectItem v-for="m in mataPelajaranList" :key="m" :value="m">{{ m }}</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="flex-1 min-w-[180px] relative">
            <Label class="text-xs text-muted-foreground">Cari</Label>
            <div class="relative mt-1.5">
              <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
              <Input
                id="search-nilai"
                v-model="searchQuery"
                type="text"
                placeholder="Nama / NISN..."
                class="pl-9 h-9"
                @input="currentPage = 1"
              />
            </div>
          </div>
        </div>

        <!-- Info KKM -->
        <div class="flex items-center gap-2 text-xs text-muted-foreground bg-muted/40 px-3 py-2 rounded-lg border">
          <AlertCircle class="size-3.5 shrink-0" />
          <span>KKM: <strong>{{ KKM }}</strong> — Nilai di bawah KKM ditampilkan dalam warna merah.</span>
        </div>

        <Card class="overflow-hidden">
          <div class="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow class="bg-muted/50">
                  <TableHead class="font-semibold w-8">#</TableHead>
                  <TableHead class="font-semibold min-w-[160px] cursor-pointer select-none" @click="toggleSort('nama')">
                    <div class="flex items-center gap-1">
                      Nama Siswa
                      <component :is="sortField === 'nama' && sortDir === 'desc' ? ChevronDown : ChevronUp"
                        :class="['size-3', sortField === 'nama' ? 'text-primary' : 'text-muted-foreground/50']" />
                    </div>
                  </TableHead>
                  <TableHead class="font-semibold">Kelas</TableHead>
                  <TableHead
                    v-for="mapel in activeMapelList"
                    :key="mapel"
                    class="font-semibold text-center min-w-[90px]"
                  >
                    <TooltipProvider>
                      <Tooltip>
                        <TooltipTrigger as-child>
                          <span class="cursor-default">
                            {{ mapel.length > 7 ? mapel.substring(0, 6) + '.' : mapel }}
                          </span>
                        </TooltipTrigger>
                        <TooltipContent>{{ mapel }}</TooltipContent>
                      </Tooltip>
                    </TooltipProvider>
                  </TableHead>
                  <TableHead class="font-semibold text-center cursor-pointer select-none min-w-[80px]" @click="toggleSort('rata')">
                    <div class="flex items-center justify-center gap-1">
                      Rata²
                      <component :is="sortField === 'rata' && sortDir === 'desc' ? ChevronDown : ChevronUp"
                        :class="['size-3', sortField === 'rata' ? 'text-primary' : 'text-muted-foreground/50']" />
                    </div>
                  </TableHead>
                  <TableHead class="font-semibold text-center">Predikat</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <template v-if="isLoading">
                  <TableRow v-for="i in 8" :key="`skel-${i}`">
                    <TableCell v-for="j in (activeMapelList.length + 5)" :key="j">
                      <Skeleton class="h-5 w-full" />
                    </TableCell>
                  </TableRow>
                </template>

                <template v-else>
                  <TableRow
                    v-for="(siswa, idx) in paginatedData"
                    :key="siswa.id"
                    class="hover:bg-muted/30 transition-colors"
                  >
                    <TableCell class="text-muted-foreground text-xs">{{ (currentPage - 1) * itemsPerPage + idx + 1 }}</TableCell>
                    <TableCell>
                      <div class="font-medium text-sm">{{ siswa.nama }}</div>
                      <div class="text-xs text-muted-foreground">{{ siswa.nisn }}</div>
                    </TableCell>
                    <TableCell>
                      <Badge variant="outline" class="text-xs font-normal">{{ siswa.kelas }}</Badge>
                    </TableCell>
                    <TableCell
                      v-for="mapel in activeMapelList"
                      :key="mapel"
                      class="text-center text-sm"
                      :class="getNilaiClass(siswa.nilai[mapel])"
                    >
                      {{ siswa.nilai[mapel] ?? '-' }}
                    </TableCell>
                    <TableCell class="text-center font-bold text-sm" :class="getNilaiClass(getRataRata(siswa))">
                      {{ getRataRata(siswa) }}
                    </TableCell>
                    <TableCell class="text-center">
                      <Badge :class="getPredikatClass(getRataRata(siswa))">
                        {{ getPredikat(getRataRata(siswa)) }}
                      </Badge>
                    </TableCell>
                  </TableRow>

                  <TableRow v-if="paginatedData.length === 0">
                    <TableCell :colspan="activeMapelList.length + 5" class="h-32 text-center text-muted-foreground">
                      <div class="flex flex-col items-center justify-center gap-2">
                        <Search class="size-8 text-muted-foreground/40" />
                        <p class="text-sm">Tidak ada data nilai ditemukan.</p>
                      </div>
                    </TableCell>
                  </TableRow>
                </template>
              </TableBody>
            </Table>
          </div>
        </Card>

        <!-- Pagination -->
        <div class="flex items-center justify-between text-sm text-muted-foreground" v-if="filteredData.length > 0">
          <span>Menampilkan {{ paginatedData.length }} dari {{ filteredData.length }} siswa</span>
          <div class="flex items-center gap-1">
            <Button id="btn-prev-nilai" variant="outline" size="sm" :disabled="currentPage === 1" @click="currentPage--">Prev</Button>
            <Button v-for="p in totalPages" :key="p" :variant="p === currentPage ? 'default' : 'outline'" size="sm" @click="currentPage = p">{{ p }}</Button>
            <Button id="btn-next-nilai" variant="outline" size="sm" :disabled="currentPage === totalPages" @click="currentPage++">Next</Button>
          </div>
        </div>
      </TabsContent>

      <!-- Peringkat Tab -->
      <TabsContent value="peringkat" class="mt-4">
        <div class="grid gap-4 grid-cols-1 md:grid-cols-3">
          <Card
            v-for="(siswa, idx) in peringkat"
            :key="siswa.id"
            :class="[
              'p-5 text-center hover:shadow-md transition-shadow',
              idx === 0 ? 'border-yellow-300 dark:border-yellow-700 bg-yellow-50/30 dark:bg-yellow-950/10' : '',
              idx === 1 ? 'border-slate-300 dark:border-slate-600' : '',
              idx === 2 ? 'border-amber-300 dark:border-amber-700 bg-amber-50/30 dark:bg-amber-950/10' : '',
            ]"
          >
            <Skeleton v-if="isLoading" class="h-24 w-full" />
            <template v-else>
              <div class="flex justify-center mb-3">
                <div :class="[
                  'w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold',
                  idx === 0 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400' :
                  idx === 1 ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' :
                  'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400'
                ]">
                  {{ idx + 1 }}
                </div>
              </div>
              <div class="flex items-center justify-center gap-1 mb-1">
                <Star v-if="idx === 0" class="size-4 text-yellow-500 fill-yellow-400" />
                <h3 class="font-bold text-base">{{ siswa.nama }}</h3>
              </div>
              <p class="text-sm text-muted-foreground mb-3">{{ siswa.kelas }}</p>
              <div class="text-3xl font-bold tracking-tight" :class="[
                idx === 0 ? 'text-yellow-600 dark:text-yellow-400' :
                idx === 1 ? 'text-slate-600 dark:text-slate-300' :
                'text-amber-600 dark:text-amber-400'
              ]">
                {{ siswa.rata }}
              </div>
              <p class="text-xs text-muted-foreground mt-1">Rata-rata Nilai</p>
              <Badge :class="['mt-3', getPredikatClass(siswa.rata)]">
                Predikat {{ getPredikat(siswa.rata) }}
              </Badge>
            </template>
          </Card>
        </div>

        <Separator class="my-4" />

        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="w-10 font-semibold">Peringkat</TableHead>
                <TableHead class="font-semibold">Nama Siswa</TableHead>
                <TableHead class="font-semibold">Kelas</TableHead>
                <TableHead class="font-semibold text-center">Rata-rata</TableHead>
                <TableHead class="font-semibold text-center">Predikat</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="(siswa, idx) in [...nilaiData].map(s => ({ ...s, rata: getRataRata(s) })).sort((a, b) => b.rata - a.rata)"
                :key="siswa.id"
                class="hover:bg-muted/30 transition-colors"
              >
                <TableCell class="text-center font-bold text-muted-foreground">{{ idx + 1 }}</TableCell>
                <TableCell class="font-medium text-sm">{{ siswa.nama }}</TableCell>
                <TableCell>
                  <Badge variant="outline" class="text-xs font-normal">{{ siswa.kelas }}</Badge>
                </TableCell>
                <TableCell class="text-center font-bold" :class="getNilaiClass(siswa.rata)">{{ siswa.rata }}</TableCell>
                <TableCell class="text-center">
                  <Badge :class="getPredikatClass(siswa.rata)">{{ getPredikat(siswa.rata) }}</Badge>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>
    </Tabs>
  </div>
</template>
