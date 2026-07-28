<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  FileCheck2,
  Download,
  Printer,
  CheckCircle2,
  Clock,
  AlertTriangle,
  Search,
  Upload,
  Eye,
  Building2,
  ChevronUp,
  ChevronDown,
} from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { getSchoolAccountability } from '@/services/api/reports'

const isLoading = ref(true)
const data = ref([])
const searchQuery = ref('')
const selectedStatus = ref('semua')
const selectedTahun = ref('2026')
const sortField = ref('tglLapor')
const sortDir = ref('desc')

onMounted(async () => {
  try {
    const res = await getSchoolAccountability()
    data.value = res || []
  } catch (error) {
    console.error('Failed to fetch school accountability data:', error)
  } finally {
    isLoading.value = false
  }
})

function formatRp(v) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v) }

const totalKegiatan = computed(() => data.value.length)
const totalDisetujui = computed(() => data.value.filter(d => d.status === 'Disetujui').length)
const totalMenunggu = computed(() => data.value.filter(d => d.status === 'Menunggu Review' || d.status === 'Belum Lapor').length)
const totalRevisi = computed(() => data.value.filter(d => d.status === 'Revisi').length)

const filteredData = computed(() => {
  let d = data.value.filter(k => {
    const matchStatus = selectedStatus.value === 'semua' || k.status === selectedStatus.value
    const matchSearch = !searchQuery.value || k.kegiatan.toLowerCase().includes(searchQuery.value.toLowerCase()) || k.unit.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchStatus && matchSearch
  })
  return [...d].sort((a,b) => {
    let va = a[sortField.value], vb = b[sortField.value]
    if (typeof va==='string') va=va.toLowerCase()
    if (typeof vb==='string') vb=vb.toLowerCase()
    return sortDir.value==='asc'?(va>vb?1:-1):(va<vb?1:-1)
  })
})

function toggleSort(f) {
  if (sortField.value===f) sortDir.value=sortDir.value==='asc'?'desc':'asc'
  else { sortField.value=f; sortDir.value='asc' }
}

function getStatusColor(status) {
  if (status === 'Disetujui') return 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
  if (status === 'Menunggu Review') return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'
  if (status === 'Revisi') return 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'
  return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400'
}
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <FileCheck2 class="size-6 text-primary" />
          Laporan Pertanggungjawaban (LPJ)
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Dokumen pelaporan dan realisasi anggaran kegiatan operasional sekolah.</p>
      </div>
      <div class="flex gap-2 shrink-0">
        <Button variant="outline" size="sm" class="gap-2"><Printer class="size-4" />Cetak</Button>
        <Button size="sm" class="gap-2"><Download class="size-4" />Ekspor</Button>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 items-end">
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Tahun</Label>
        <Select v-model="selectedTahun">
          <SelectTrigger class="w-[100px]"><SelectValue /></SelectTrigger>
          <SelectContent><SelectItem value="2026">2026</SelectItem><SelectItem value="2025">2025</SelectItem></SelectContent>
        </Select>
      </div>
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Status LPJ</Label>
        <Select v-model="selectedStatus">
          <SelectTrigger class="w-[150px]"><SelectValue placeholder="Semua" /></SelectTrigger>
          <SelectContent>
            <SelectItem value="semua">Semua</SelectItem>
            <SelectItem value="Disetujui">Disetujui</SelectItem>
            <SelectItem value="Menunggu Review">Menunggu Review</SelectItem>
            <SelectItem value="Revisi">Perlu Revisi</SelectItem>
            <SelectItem value="Belum Lapor">Belum Lapor</SelectItem>
          </SelectContent>
        </Select>
      </div>
      <div class="flex-1 min-w-[180px]">
        <Label class="text-xs text-muted-foreground">Cari Kegiatan / Unit</Label>
        <div class="relative mt-1.5">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
          <Input v-model="searchQuery" placeholder="Nama kegiatan..." class="pl-9 h-9" />
        </div>
      </div>
    </div>

    <!-- Stats -->
    <StatCardGrid cols="4">
      <StatCard
        label="Total Kegiatan"
        :value="totalKegiatan"
        sub="Dalam tahun berjalan"
        :icon="Building2"
        variant="primary"
        :delay="100"
      />
      <StatCard
        label="LPJ Disetujui"
        :value="totalDisetujui"
        sub="Selesai & ditutup"
        :icon="CheckCircle2"
        variant="emerald"
        :delay="200"
      />
      <StatCard
        label="Menunggu Lapor"
        :value="totalMenunggu"
        sub="Belum dikonfirmasi"
        :icon="Clock"
        variant="amber"
        :delay="300"
      />
      <StatCard
        label="Perlu Revisi"
        :value="totalRevisi"
        sub="Dokumen ditolak/kurang"
        :icon="AlertTriangle"
        variant="destructive"
        :delay="400"
      />
    </StatCardGrid>

    <Card class="overflow-hidden">
      <Table>
        <TableHeader>
          <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
                <TableHead class="font-semibold cursor-pointer select-none" @click="toggleSort('kegiatan')">
              <div class="flex items-center gap-1">Nama Kegiatan <component :is="sortField==='kegiatan'&&sortDir==='desc'?ChevronDown:ChevronUp" :class="['size-3',sortField==='kegiatan'?'text-primary':'text-muted-foreground/40']" /></div>
            </TableHead>
            <TableHead class="font-semibold">Unit Sekolah</TableHead>
            <TableHead class="font-semibold">Penanggung Jawab</TableHead>
            <TableHead class="font-semibold text-right">Anggaran</TableHead>
            <TableHead class="font-semibold text-right">Realisasi</TableHead>
            <TableHead class="font-semibold text-center cursor-pointer select-none" @click="toggleSort('tglLapor')">
              <div class="flex items-center justify-center gap-1">Tgl Lapor <component :is="sortField==='tglLapor'&&sortDir==='desc'?ChevronDown:ChevronUp" :class="['size-3',sortField==='tglLapor'?'text-primary':'text-muted-foreground/40']" /></div>
            </TableHead>
            <TableHead class="font-semibold text-center">Status</TableHead>
            <TableHead class="font-semibold text-center">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="isLoading"><TableRow v-for="(i, index) in 6" :key="i">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell><TableCell v-for="j in 9" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow></template>
          <template v-else>
            <TableRow v-for="(item, index) in filteredData" :key="item.id" class="hover:bg-muted/30 transition-colors">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
              <TableCell class="font-medium text-sm">{{ item.kegiatan }}</TableCell>
              <TableCell class="text-sm text-muted-foreground">{{ item.unit }}</TableCell>
              <TableCell class="text-sm">{{ item.pic }}</TableCell>
              <TableCell class="text-right font-medium">{{ formatRp(item.anggaran) }}</TableCell>
              <TableCell class="text-right font-semibold" :class="item.realisasi > item.anggaran ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                {{ item.realisasi === 0 ? '-' : formatRp(item.realisasi) }}
              </TableCell>
              <TableCell class="text-center text-sm text-muted-foreground">{{ item.tglLapor }}</TableCell>
              <TableCell class="text-center"><Badge :class="getStatusColor(item.status)">{{ item.status }}</Badge></TableCell>
              <TableCell>
                <div class="flex items-center justify-center gap-1">
                  <Button variant="ghost" size="icon" class="h-8 w-8 text-primary" title="Lihat Dokumen"><Eye class="size-4" /></Button>
                  <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground" title="Upload Berkas"><Upload class="size-4" /></Button>
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-if="filteredData.length === 0">
              <TableCell colspan="9" class="h-32 text-center text-muted-foreground">
                <div class="flex flex-col items-center justify-center gap-2">
                  <Search class="size-8 text-muted-foreground/40" />
                  <p class="text-sm">Tidak ada LPJ ditemukan.</p>
                </div>
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </Card>
  </div>
</template>
