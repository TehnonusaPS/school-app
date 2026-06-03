<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  LayoutDashboard,
  Download,
  Printer,
  Building2,
  Users,
  Wallet,
  BookOpen,
  TrendingUp,
  TrendingDown,
  ChevronRight,
  ExternalLink,
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
import { Separator } from '@/components/ui/separator'

const mockSekolah = [
  { id: 1, nama: 'SDN Tunas Bangsa', jenjang: 'SD', totalSiswa: 412, guru: 24, rataNilai: 82.3, kehadiran: 94, pemasukan: 185000000, pengeluaran: 162000000 },
  { id: 2, nama: 'SMPN Harapan Ilmu', jenjang: 'SMP', totalSiswa: 356, guru: 31, rataNilai: 79.8, kehadiran: 91, pemasukan: 220000000, pengeluaran: 198000000 },
  { id: 3, nama: 'SMAN Bina Prestasi', jenjang: 'SMA', totalSiswa: 487, guru: 42, rataNilai: 81.5, kehadiran: 93, pemasukan: 345000000, pengeluaran: 298000000 },
  { id: 4, nama: 'SMK Teknologi Maju', jenjang: 'SMK', totalSiswa: 298, guru: 27, rataNilai: 77.9, kehadiran: 89, pemasukan: 210000000, pengeluaran: 195000000 },
]

const isLoading = ref(true)
const sekolahData = ref([])
const selectedTahun = ref('2025/2026')
const selectedSemester = ref('1')

onMounted(() => {
  setTimeout(() => {
    sekolahData.value = mockSekolah
    isLoading.value = false
  }, 600)
})

function formatRp(v) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v)
}

const totalSiswa = computed(() => sekolahData.value.reduce((s, k) => s + k.totalSiswa, 0))
const totalGuru = computed(() => sekolahData.value.reduce((s, k) => s + k.guru, 0))
const totalPemasukan = computed(() => sekolahData.value.reduce((s, k) => s + k.pemasukan, 0))
const totalPengeluaran = computed(() => sekolahData.value.reduce((s, k) => s + k.pengeluaran, 0))
const avgNilai = computed(() => sekolahData.value.length ? (sekolahData.value.reduce((s, k) => s + k.rataNilai, 0) / sekolahData.value.length).toFixed(1) : 0)
const avgKehadiran = computed(() => sekolahData.value.length ? Math.round(sekolahData.value.reduce((s, k) => s + k.kehadiran, 0) / sekolahData.value.length) : 0)

const jenjangColor = { SD: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400', SMP: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400', SMA: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400', SMK: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400' }
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <LayoutDashboard class="size-6 text-primary" />
          Laporan Konsolidasi Yayasan
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Integrasi laporan keuangan & akademik seluruh sekolah di bawah yayasan.</p>
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
        <Select v-model="selectedTahun">
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
    </div>

    <!-- Ringkasan Yayasan -->
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-3">
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Unit Sekolah</span>
          <div class="p-1.5 bg-muted rounded-lg"><Building2 class="size-4 text-muted-foreground" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold">{{ sekolahData.length }}</div>
        <p class="text-xs text-muted-foreground mt-1">SD, SMP, SMA, SMK</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Siswa</span>
          <div class="p-1.5 bg-primary/10 rounded-lg"><Users class="size-4 text-primary" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold text-primary">{{ totalSiswa }}</div>
        <p class="text-xs text-muted-foreground mt-1">Di seluruh unit</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Pendidik</span>
          <div class="p-1.5 bg-muted rounded-lg"><Users class="size-4 text-muted-foreground" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold">{{ totalGuru }}</div>
        <p class="text-xs text-muted-foreground mt-1">Guru & tenaga kependidikan</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Rata-rata Nilai</span>
          <div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg"><BookOpen class="size-4 text-green-600" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold text-green-600 dark:text-green-400">{{ avgNilai }}</div>
        <p class="text-xs text-muted-foreground mt-1">Akademik seluruh sekolah</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow border-green-200 dark:border-green-900/40 bg-green-50/30 dark:bg-green-950/10">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-green-700 dark:text-green-400 uppercase tracking-wider">Total Pemasukan</span>
          <div class="p-1.5 bg-green-100 dark:bg-green-900/40 rounded-lg"><TrendingUp class="size-4 text-green-600" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-32" />
        <div v-else class="text-xl font-bold text-green-700 dark:text-green-400">{{ formatRp(totalPemasukan) }}</div>
        <p class="text-xs text-muted-foreground mt-1">Periode berjalan</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow border-red-200 dark:border-red-900/40 bg-red-50/30 dark:bg-red-950/10">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-medium text-red-700 dark:text-red-400 uppercase tracking-wider">Total Pengeluaran</span>
          <div class="p-1.5 bg-red-100 dark:bg-red-900/40 rounded-lg"><TrendingDown class="size-4 text-red-600" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-8 w-32" />
        <div v-else class="text-xl font-bold text-red-700 dark:text-red-400">{{ formatRp(totalPengeluaran) }}</div>
        <p class="text-xs text-muted-foreground mt-1">Periode berjalan</p>
      </Card>
    </div>

    <!-- Tabel per Sekolah -->
    <Card class="overflow-hidden">
      <CardHeader class="py-3 px-4 border-b bg-muted/30">
        <CardTitle class="text-sm font-semibold">Detail per Unit Sekolah</CardTitle>
      </CardHeader>
      <div class="overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
                <TableHead class="font-semibold">Unit Sekolah</TableHead>
              <TableHead class="font-semibold">Jenjang</TableHead>
              <TableHead class="font-semibold text-center">Siswa</TableHead>
              <TableHead class="font-semibold text-center">Guru</TableHead>
              <TableHead class="font-semibold text-center">Rata² Nilai</TableHead>
              <TableHead class="font-semibold text-center">Kehadiran</TableHead>
              <TableHead class="font-semibold text-right">Pemasukan</TableHead>
              <TableHead class="font-semibold text-right">Pengeluaran</TableHead>
              <TableHead class="font-semibold text-right">Saldo</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="isLoading">
              <TableRow v-for="(i, index) in 4" :key="i">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                <TableCell v-for="j in 10" :key="j"><Skeleton class="h-5 w-full" /></TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow v-for="(s, index) in sekolahData" :key="s.id" class="hover:bg-muted/30 transition-colors">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                <TableCell class="font-semibold text-sm">{{ s.nama }}</TableCell>
                <TableCell><Badge :class="jenjangColor[s.jenjang]">{{ s.jenjang }}</Badge></TableCell>
                <TableCell class="text-center font-medium">{{ s.totalSiswa }}</TableCell>
                <TableCell class="text-center font-medium">{{ s.guru }}</TableCell>
                <TableCell class="text-center">
                  <Badge :class="s.rataNilai >= 80 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'">{{ s.rataNilai }}</Badge>
                </TableCell>
                <TableCell class="text-center">
                  <Badge :class="s.kehadiran >= 90 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'">{{ s.kehadiran }}%</Badge>
                </TableCell>
                <TableCell class="text-right text-sm text-green-600 dark:text-green-400 font-medium">{{ formatRp(s.pemasukan) }}</TableCell>
                <TableCell class="text-right text-sm text-red-600 dark:text-red-400 font-medium">{{ formatRp(s.pengeluaran) }}</TableCell>
                <TableCell class="text-right text-sm font-bold" :class="(s.pemasukan - s.pengeluaran) >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400'">{{ formatRp(s.pemasukan - s.pengeluaran) }}</TableCell>
              </TableRow>
              <TableRow class="bg-muted/50 font-bold">
                <TableCell colspan="3" class="font-bold">TOTAL YAYASAN</TableCell>
                <TableCell class="text-center font-bold">{{ totalSiswa }}</TableCell>
                <TableCell class="text-center font-bold">{{ totalGuru }}</TableCell>
                <TableCell class="text-center font-bold">{{ avgNilai }}</TableCell>
                <TableCell class="text-center font-bold">{{ avgKehadiran }}%</TableCell>
                <TableCell class="text-right font-bold text-green-700 dark:text-green-400">{{ formatRp(totalPemasukan) }}</TableCell>
                <TableCell class="text-right font-bold text-red-700 dark:text-red-400">{{ formatRp(totalPengeluaran) }}</TableCell>
                <TableCell class="text-right font-bold text-blue-700 dark:text-blue-400">{{ formatRp(totalPemasukan - totalPengeluaran) }}</TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </div>
    </Card>
  </div>
</template>
