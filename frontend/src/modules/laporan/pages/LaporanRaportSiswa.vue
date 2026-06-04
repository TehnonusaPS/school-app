<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  FileBarChart,
  Search,
  Download,
  Printer,
  BookOpen,
  User,
  Calendar,
  GraduationCap,
  ChevronRight,
  Eye,
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
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from '@/components/ui/dialog'
import { Separator } from '@/components/ui/separator'
import { ScrollArea } from '@/components/ui/scroll-area'

const mataPelajaranList = ['Matematika', 'Fisika', 'Kimia', 'Biologi', 'Bahasa Indonesia', 'Bahasa Inggris', 'Sejarah', 'PJOK']

const mockRaportData = [
  {
    id: 1, nisn: '0051234567', nama: 'Ahmad Fadil', kelas: 'XI IPA 1', waliKelas: 'Bu Sari Dewi, S.Pd',
    tp: '2025/2026', semester: '1',
    nilai: { Matematika: 88, Fisika: 82, Kimia: 90, Biologi: 85, 'Bahasa Indonesia': 88, 'Bahasa Inggris': 79, Sejarah: 85, PJOK: 90 },
    kehadiran: { hadir: 95, terlambat: 4, izin: 2, sakit: 1, alpa: 0 },
    catatan: 'Ahmad menunjukkan kemajuan yang baik di bidang sains. Perlu meningkatkan kemampuan bahasa Inggris.'
  },
  {
    id: 2, nisn: '0069876543', nama: 'Bunga Citra', kelas: 'XI IPA 1', waliKelas: 'Bu Sari Dewi, S.Pd',
    tp: '2025/2026', semester: '1',
    nilai: { Matematika: 92, Fisika: 88, Kimia: 85, Biologi: 91, 'Bahasa Indonesia': 90, 'Bahasa Inggris': 87, Sejarah: 88, PJOK: 85 },
    kehadiran: { hadir: 100, terlambat: 1, izin: 1, sakit: 0, alpa: 0 },
    catatan: 'Siswa berprestasi dengan nilai konsisten di semua mata pelajaran. Direkomendasikan untuk program pengembangan bakat.'
  },
  {
    id: 3, nisn: '0054321987', nama: 'Cakra Khan', kelas: 'XI IPA 1', waliKelas: 'Bu Sari Dewi, S.Pd',
    tp: '2025/2026', semester: '1',
    nilai: { Matematika: 72, Fisika: 68, Kimia: 74, Biologi: 70, 'Bahasa Indonesia': 75, 'Bahasa Inggris': 65, Sejarah: 73, PJOK: 80 },
    kehadiran: { hadir: 78, terlambat: 8, izin: 5, sakit: 4, alpa: 7 },
    catatan: 'Perlu peningkatan di banyak aspek terutama kedisiplinan. Disarankan untuk mengikuti program remedial.'
  },
  {
    id: 4, nisn: '0061122334', nama: 'Dian Sastro', kelas: 'XI IPA 1', waliKelas: 'Bu Sari Dewi, S.Pd',
    tp: '2025/2026', semester: '1',
    nilai: { Matematika: 95, Fisika: 93, Kimia: 88, Biologi: 92, 'Bahasa Indonesia': 89, 'Bahasa Inggris': 91, Sejarah: 90, PJOK: 88 },
    kehadiran: { hadir: 100, terlambat: 0, izin: 2, sakit: 0, alpa: 0 },
    catatan: 'Siswa terbaik di kelas dengan dedikasi tinggi. Kandidat kuat untuk beasiswa berprestasi.'
  },
  {
    id: 5, nisn: '0055566778', nama: 'Elsa Novita', kelas: 'XI IPA 2', waliKelas: 'Pak Rahmat, M.Pd',
    tp: '2025/2026', semester: '1',
    nilai: { Matematika: 80, Fisika: 75, Kimia: 82, Biologi: 78, 'Bahasa Indonesia': 82, 'Bahasa Inggris': 70, Sejarah: 79, PJOK: 85 },
    kehadiran: { hadir: 100, terlambat: 0, izin: 0, sakit: 2, alpa: 0 },
    catatan: 'Siswa yang rajin dan disiplin. Nilai konsisten di atas KKM.'
  },
  {
    id: 6, nisn: '0068899001', nama: 'Farhan Ramdan', kelas: 'XI IPA 2', waliKelas: 'Pak Rahmat, M.Pd',
    tp: '2025/2026', semester: '1',
    nilai: { Matematika: 65, Fisika: 60, Kimia: 68, Biologi: 63, 'Bahasa Indonesia': 70, 'Bahasa Inggris': 55, Sejarah: 67, PJOK: 78 },
    kehadiran: { hadir: 68, terlambat: 10, izin: 5, sakit: 8, alpa: 11 },
    catatan: 'Perlu perhatian khusus. Kehadiran dan nilai jauh di bawah standar. Disarankan konsultasi dengan orang tua.'
  },
]

const KKM = 75
const isLoading = ref(true)
const raportData = ref([])
const searchQuery = ref('')
const selectedKelas = ref('semua')
const selectedSemester = ref('1')
const currentPage = ref(1)
const itemsPerPage = 8
const selectedRaport = ref(null)
const isDetailOpen = ref(false)

onMounted(() => {
  setTimeout(() => {
    raportData.value = mockRaportData
    isLoading.value = false
  }, 600)
})

const kelasList = computed(() => {
  const set = new Set(raportData.value.map(d => d.kelas))
  return Array.from(set).sort()
})

function getRataRata(siswa) {
  const vals = mataPelajaranList.map(m => siswa.nilai[m] ?? 0).filter(v => v > 0)
  if (!vals.length) return 0
  return (vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(1)
}

function getPredikat(rata) {
  const r = parseFloat(rata)
  if (r >= 90) return { label: 'A', desc: 'Sangat Baik' }
  if (r >= 80) return { label: 'B', desc: 'Baik' }
  if (r >= 70) return { label: 'C', desc: 'Cukup' }
  if (r >= 60) return { label: 'D', desc: 'Kurang' }
  return { label: 'E', desc: 'Sangat Kurang' }
}

function getPredikatClass(rata) {
  const r = parseFloat(rata)
  if (r >= 90) return 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
  if (r >= 80) return 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400'
  if (r >= 70) return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'
  return 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'
}

function getNilaiClass(val) {
  if (!val) return 'text-muted-foreground'
  if (val >= 90) return 'text-green-600 dark:text-green-400'
  if (val >= KKM) return 'text-foreground'
  return 'text-red-600 dark:text-red-400'
}

function getKehadiranPct(siswa) {
  const total = Object.values(siswa.kehadiran).reduce((a, b) => a + b, 0)
  if (!total) return 0
  return Math.round(((siswa.kehadiran.hadir + siswa.kehadiran.terlambat) / total) * 100)
}

const filteredData = computed(() => {
  return raportData.value.filter(item => {
    const matchKelas = selectedKelas.value === 'semua' || item.kelas === selectedKelas.value
    const matchSemester = item.semester === selectedSemester.value
    const matchSearch =
      !searchQuery.value ||
      item.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.nisn.includes(searchQuery.value)
    return matchKelas && matchSearch && matchSemester
  })
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredData.value.slice(start, start + itemsPerPage)
})

function openDetail(siswa) {
  selectedRaport.value = siswa
  isDetailOpen.value = true
}

function handlePrint() {
  window.print()
}
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
          <FileBarChart class="size-6 text-primary" />
          Laporan Raport Siswa
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">
          Rekapitulasi nilai akhir semester dan pencetakan rapor siswa perwalian.
        </p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <Button id="btn-print-raport" variant="outline" size="sm" class="gap-2" @click="handlePrint">
          <Printer class="size-4" />
          Cetak Semua
        </Button>
        <Button id="btn-export-raport" size="sm" class="gap-2">
          <Download class="size-4" />
          Ekspor PDF
        </Button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Siswa</span>
          <div class="p-1.5 bg-muted rounded-lg">
            <User class="size-4 text-muted-foreground" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight">{{ raportData.length }}</div>
        <p class="text-xs text-muted-foreground mt-1">Siswa terdaftar</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Naik Kelas</span>
          <div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg">
            <GraduationCap class="size-4 text-green-600" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold tracking-tight text-green-600 dark:text-green-400">
          {{ raportData.filter(s => parseFloat(getRataRata(s)) >= KKM).length }}
        </div>
        <p class="text-xs text-muted-foreground mt-1">Di atas rata-rata KKM</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Rata-rata Kelas</span>
          <div class="p-1.5 bg-primary/10 rounded-lg">
            <BookOpen class="size-4 text-primary" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold tracking-tight text-primary">
          {{ raportData.length ? (raportData.reduce((sum, s) => sum + parseFloat(getRataRata(s)), 0) / raportData.length).toFixed(1) : '-' }}
        </div>
        <p class="text-xs text-muted-foreground mt-1">Nilai rata-rata</p>
      </Card>

      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Tahun Pelajaran</span>
          <div class="p-1.5 bg-muted rounded-lg">
            <Calendar class="size-4 text-muted-foreground" />
          </div>
        </div>
        <div class="text-xl font-bold tracking-tight mt-2">2025/2026</div>
        <p class="text-xs text-muted-foreground mt-1">Semester {{ selectedSemester }}</p>
      </Card>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap items-end gap-3">
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Semester</Label>
        <Select v-model="selectedSemester">
          <SelectTrigger id="select-semester-raport" class="w-[140px]">
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
          <SelectTrigger id="select-kelas-raport" class="w-[140px]">
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
            id="search-raport"
            v-model="searchQuery"
            type="text"
            placeholder="Nama / NISN..."
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
            <TableHead class="font-semibold">Nama Siswa</TableHead>
            <TableHead class="font-semibold">Kelas</TableHead>
            <TableHead class="font-semibold">Wali Kelas</TableHead>
            <TableHead class="font-semibold text-center">Rata-rata</TableHead>
            <TableHead class="font-semibold text-center">Kehadiran</TableHead>
            <TableHead class="font-semibold text-center">Predikat</TableHead>
            <TableHead class="font-semibold text-center">Status</TableHead>
            <TableHead class="font-semibold w-[80px]">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="isLoading">
            <TableRow v-for="(i, index) in 6" :key="`skel-${i}`">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
              <TableCell v-for="j in 10" :key="j"><Skeleton class="h-5 w-full" /></TableCell>
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
              <TableCell class="text-sm text-muted-foreground">{{ siswa.waliKelas }}</TableCell>
              <TableCell class="text-center font-bold text-sm" :class="getNilaiClass(parseFloat(getRataRata(siswa)))">
                {{ getRataRata(siswa) }}
              </TableCell>
              <TableCell class="text-center">
                <Badge :class="[
                  'text-xs',
                  getKehadiranPct(siswa) >= 90 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' :
                  getKehadiranPct(siswa) >= 75 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400' :
                  'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'
                ]">
                  {{ getKehadiranPct(siswa) }}%
                </Badge>
              </TableCell>
              <TableCell class="text-center">
                <Badge :class="getPredikatClass(getRataRata(siswa))">
                  {{ getPredikat(getRataRata(siswa)).label }} — {{ getPredikat(getRataRata(siswa)).desc }}
                </Badge>
              </TableCell>
              <TableCell class="text-center">
                <Badge :class="parseFloat(getRataRata(siswa)) >= KKM
                  ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
                  : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'
                ">
                  {{ parseFloat(getRataRata(siswa)) >= KKM ? 'Naik Kelas' : 'Perlu Remedial' }}
                </Badge>
              </TableCell>
              <TableCell>
                <div class="flex gap-1">
                  <Button
                    :id="`btn-view-raport-${siswa.id}`"
                    variant="ghost"
                    size="icon"
                    class="h-8 w-8"
                    @click="openDetail(siswa)"
                    title="Lihat Detail Raport"
                  >
                    <Eye class="size-4" />
                  </Button>
                  <Button
                    :id="`btn-print-raport-${siswa.id}`"
                    variant="ghost"
                    size="icon"
                    class="h-8 w-8"
                    title="Cetak Raport"
                    @click="handlePrint"
                  >
                    <Printer class="size-4" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>

            <TableRow v-if="paginatedData.length === 0">
              <TableCell colspan="10" class="h-32 text-center text-muted-foreground">
                <div class="flex flex-col items-center justify-center gap-2">
                  <FileBarChart class="size-8 text-muted-foreground/40" />
                  <p class="text-sm">Tidak ada data raport ditemukan.</p>
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
        <Button id="btn-prev-raport" variant="outline" size="sm" :disabled="currentPage === 1" @click="currentPage--">Prev</Button>
        <Button v-for="p in totalPages" :key="p" :variant="p === currentPage ? 'default' : 'outline'" size="sm" @click="currentPage = p">{{ p }}</Button>
        <Button id="btn-next-raport" variant="outline" size="sm" :disabled="currentPage === totalPages" @click="currentPage++">Next</Button>
      </div>
    </div>
  </div>

  <!-- Detail Raport Dialog -->
  <Dialog v-model:open="isDetailOpen">
    <DialogContent class="max-w-2xl">
      <DialogHeader>
        <DialogTitle class="flex items-center gap-2">
          <FileBarChart class="size-5 text-primary" />
          Detail Raport Siswa
        </DialogTitle>
        <DialogDescription v-if="selectedRaport">
          {{ selectedRaport.nama }} — {{ selectedRaport.kelas }} | Semester {{ selectedRaport.semester }} TP {{ selectedRaport.tp }}
        </DialogDescription>
      </DialogHeader>

      <ScrollArea class="max-h-[65vh]">
        <div v-if="selectedRaport" class="space-y-5 pr-2">
          <!-- Info Siswa -->
          <div class="grid grid-cols-2 gap-3 p-4 bg-muted/30 rounded-xl border">
            <div>
              <p class="text-xs text-muted-foreground">Nama Lengkap</p>
              <p class="font-semibold text-sm mt-0.5">{{ selectedRaport.nama }}</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground">NISN</p>
              <p class="font-semibold text-sm mt-0.5">{{ selectedRaport.nisn }}</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground">Kelas</p>
              <p class="font-semibold text-sm mt-0.5">{{ selectedRaport.kelas }}</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground">Wali Kelas</p>
              <p class="font-semibold text-sm mt-0.5">{{ selectedRaport.waliKelas }}</p>
            </div>
          </div>

          <!-- Tabel Nilai -->
          <div>
            <h3 class="text-sm font-semibold mb-2 flex items-center gap-1.5">
              <GraduationCap class="size-4 text-primary" />
              Nilai Per Mata Pelajaran
            </h3>
            <div class="rounded-xl border overflow-hidden">
              <Table>
                <TableHeader>
                  <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
                <TableHead class="font-semibold">Mata Pelajaran</TableHead>
                    <TableHead class="font-semibold text-center">Nilai</TableHead>
                    <TableHead class="font-semibold text-center">Status</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="(mapel, index) in mataPelajaranList" :key="mapel">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                    <TableCell class="text-sm">{{ mapel }}</TableCell>
                    <TableCell class="text-center font-bold" :class="getNilaiClass(selectedRaport.nilai[mapel])">
                      {{ selectedRaport.nilai[mapel] ?? '-' }}
                    </TableCell>
                    <TableCell class="text-center">
                      <Badge :class="selectedRaport.nilai[mapel] >= KKM
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
                        : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'
                      " class="text-xs">
                        {{ selectedRaport.nilai[mapel] >= KKM ? 'Tuntas' : 'Remedial' }}
                      </Badge>
                    </TableCell>
                  </TableRow>
                  <TableRow class="bg-muted/50">
                    <TableCell class="font-bold">Rata-rata</TableCell>
                    <TableCell class="text-center font-bold text-primary">{{ getRataRata(selectedRaport) }}</TableCell>
                    <TableCell class="text-center">
                      <Badge :class="getPredikatClass(getRataRata(selectedRaport))">
                        Predikat {{ getPredikat(getRataRata(selectedRaport)).label }}
                      </Badge>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>
          </div>

          <Separator />

          <!-- Rekap Kehadiran -->
          <div>
            <h3 class="text-sm font-semibold mb-3 flex items-center gap-1.5">
              <Calendar class="size-4 text-primary" />
              Rekap Kehadiran
            </h3>
            <div class="grid grid-cols-5 gap-2">
              <div class="text-center p-3 bg-green-50 dark:bg-green-950/20 rounded-xl border border-green-100 dark:border-green-900/30">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ selectedRaport.kehadiran.hadir }}</div>
                <p class="text-xs text-muted-foreground mt-1">Hadir</p>
              </div>
              <div class="text-center p-3 bg-yellow-50 dark:bg-yellow-950/20 rounded-xl border border-yellow-100 dark:border-yellow-900/30">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ selectedRaport.kehadiran.terlambat }}</div>
                <p class="text-xs text-muted-foreground mt-1">Terlambat</p>
              </div>
              <div class="text-center p-3 bg-indigo-50 dark:bg-indigo-950/20 rounded-xl border border-indigo-100 dark:border-indigo-900/30">
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ selectedRaport.kehadiran.izin }}</div>
                <p class="text-xs text-muted-foreground mt-1">Izin</p>
              </div>
              <div class="text-center p-3 bg-blue-50 dark:bg-blue-950/20 rounded-xl border border-blue-100 dark:border-blue-900/30">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ selectedRaport.kehadiran.sakit }}</div>
                <p class="text-xs text-muted-foreground mt-1">Sakit</p>
              </div>
              <div class="text-center p-3 bg-red-50 dark:bg-red-950/20 rounded-xl border border-red-100 dark:border-red-900/30">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ selectedRaport.kehadiran.alpa }}</div>
                <p class="text-xs text-muted-foreground mt-1">Alpa</p>
              </div>
            </div>
          </div>

          <Separator />

          <!-- Catatan -->
          <div>
            <h3 class="text-sm font-semibold mb-2">Catatan Wali Kelas</h3>
            <p class="text-sm text-muted-foreground bg-muted/30 p-3 rounded-xl border italic">
              "{{ selectedRaport.catatan }}"
            </p>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-2 pt-2">
            <Button variant="outline" class="gap-2" @click="handlePrint">
              <Printer class="size-4" />
              Cetak Raport
            </Button>
            <Button class="gap-2">
              <Download class="size-4" />
              Unduh PDF
            </Button>
          </div>
        </div>
      </ScrollArea>
    </DialogContent>
  </Dialog>
</template>
