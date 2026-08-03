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
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
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
import { getFoundationConsolidation } from '@/services/api/reports'

const isLoading = ref(true)
const sekolahData = ref([])
const selectedTahun = ref('2025/2026')
const selectedSemester = ref('1')

onMounted(async () => {
  try {
    const data = await getFoundationConsolidation()
    sekolahData.value = data
  } catch (error) {
    console.error('Failed to fetch consolidation data:', error)
  } finally {
    isLoading.value = false
  }
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
    <StatCardGrid cols="3">
      <StatCard
        label="Unit Sekolah"
        :value="sekolahData.length"
        sub="SD, SMP, SMA, SMK"
        :icon="Building2"
        variant="violet"
        :delay="100"
      />
      <StatCard
        label="Total Siswa"
        :value="totalSiswa"
        sub="Di seluruh unit"
        :icon="Users"
        variant="primary"
        :delay="200"
      />
      <StatCard
        label="Total Pendidik"
        :value="totalGuru"
        sub="Guru & tenaga kependidikan"
        :icon="Users"
        variant="blue"
        :delay="300"
      />
      <StatCard
        label="Rata-rata Nilai"
        :value="avgNilai"
        sub="Akademik seluruh sekolah"
        :icon="BookOpen"
        variant="emerald"
        :delay="400"
      />
      <StatCard
        label="Total Pemasukan"
        :value="formatRp(totalPemasukan)"
        sub="Periode berjalan"
        :icon="TrendingUp"
        variant="emerald"
        :delay="500"
      />
      <StatCard
        label="Total Pengeluaran"
        :value="formatRp(totalPengeluaran)"
        sub="Periode berjalan"
        :icon="TrendingDown"
        variant="amber"
        :delay="600"
      />
    </StatCardGrid>

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
