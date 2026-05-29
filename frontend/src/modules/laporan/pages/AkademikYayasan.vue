<script setup>
import { ref, computed, onMounted } from 'vue'
import { BookOpen, Download, Printer, Award, TrendingUp, ChevronUp, ChevronDown } from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'

const mockData = [
  { id: 1, sekolah: 'SDN Tunas Bangsa', jenjang: 'SD', siswa: 412, rataNilai: 82.3, kelulusan: 96, ekskul: 8, prestasi: 12 },
  { id: 2, sekolah: 'SMPN Harapan Ilmu', jenjang: 'SMP', siswa: 356, rataNilai: 79.8, kelulusan: 94, ekskul: 11, prestasi: 8 },
  { id: 3, sekolah: 'SMAN Bina Prestasi', jenjang: 'SMA', siswa: 487, rataNilai: 81.5, kelulusan: 97, ekskul: 15, prestasi: 21 },
  { id: 4, sekolah: 'SMK Teknologi Maju', jenjang: 'SMK', siswa: 298, rataNilai: 77.9, kelulusan: 92, ekskul: 7, prestasi: 6 },
]

const mockPrestasi = [
  { id: 1, nama: 'Olimpiade Matematika Nasional', sekolah: 'SMAN Bina Prestasi', tingkat: 'Nasional', hasil: 'Juara 1', tgl: 'Mar 2026' },
  { id: 2, nama: 'Lomba Karya Ilmiah Remaja', sekolah: 'SMAN Bina Prestasi', tingkat: 'Provinsi', hasil: 'Juara 2', tgl: 'Feb 2026' },
  { id: 3, nama: 'Futsal Pelajar Kota', sekolah: 'SMPN Harapan Ilmu', tingkat: 'Kota', hasil: 'Juara 1', tgl: 'Apr 2026' },
  { id: 4, nama: 'Lomba Debat Bahasa Inggris', sekolah: 'SMAN Bina Prestasi', tingkat: 'Provinsi', hasil: 'Juara 3', tgl: 'Jan 2026' },
  { id: 5, nama: 'Festival Seni Pelajar', sekolah: 'SDN Tunas Bangsa', tingkat: 'Kota', hasil: 'Juara 2', tgl: 'Mei 2026' },
]

const isLoading = ref(true)
const data = ref([])
const prestasiData = ref([])
const selectedTahun = ref('2025/2026')
const selectedSemester = ref('1')

onMounted(() => { setTimeout(() => { data.value = mockData; prestasiData.value = mockPrestasi; isLoading.value = false }, 500) })

const avgNilai = computed(() => data.value.length ? (data.value.reduce((s,k)=>s+k.rataNilai,0)/data.value.length).toFixed(1) : 0)
const avgKelulusan = computed(() => data.value.length ? Math.round(data.value.reduce((s,k)=>s+k.kelulusan,0)/data.value.length) : 0)
const totalPrestasi = computed(() => data.value.reduce((s,k)=>s+k.prestasi,0))

const jenjangColor = { SD: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400', SMP: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400', SMA: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400', SMK: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400' }
const tingkatColor = { Nasional: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400', Provinsi: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400', Kota: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400' }
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <BookOpen class="size-6 text-primary" />
          Laporan Akademik Yayasan
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Rekapitulasi kurikulum dan prestasi belajar per unit sekolah.</p>
      </div>
      <div class="flex gap-2">
        <Button variant="outline" size="sm" class="gap-2"><Printer class="size-4" />Cetak</Button>
        <Button size="sm" class="gap-2"><Download class="size-4" />Ekspor</Button>
      </div>
    </div>

    <div class="flex flex-wrap gap-3 items-end">
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Tahun Ajaran</Label>
        <Select v-model="selectedTahun"><SelectTrigger class="w-[140px]"><SelectValue /></SelectTrigger><SelectContent><SelectItem value="2025/2026">2025/2026</SelectItem><SelectItem value="2024/2025">2024/2025</SelectItem></SelectContent></Select>
      </div>
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Semester</Label>
        <Select v-model="selectedSemester"><SelectTrigger class="w-[130px]"><SelectValue /></SelectTrigger><SelectContent><SelectItem value="1">Semester 1</SelectItem><SelectItem value="2">Semester 2</SelectItem></SelectContent></Select>
      </div>
    </div>

    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Rata² Nilai</span><div class="p-1.5 bg-primary/10 rounded-lg"><TrendingUp class="size-4 text-primary" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold text-primary">{{ avgNilai }}</div>
        <p class="text-xs text-muted-foreground mt-1">Seluruh unit sekolah</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Kelulusan</span><div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg"><Award class="size-4 text-green-600" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold text-green-600 dark:text-green-400">{{ avgKelulusan }}%</div>
        <p class="text-xs text-muted-foreground mt-1">Rata-rata ketuntasan</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Ekskul</span><div class="p-1.5 bg-muted rounded-lg"><BookOpen class="size-4 text-muted-foreground" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold">{{ data.reduce((s,k)=>s+k.ekskul,0) }}</div>
        <p class="text-xs text-muted-foreground mt-1">Ekstrakurikuler aktif</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Prestasi</span><div class="p-1.5 bg-yellow-50 dark:bg-yellow-950/40 rounded-lg"><Award class="size-4 text-yellow-600" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ totalPrestasi }}</div>
        <p class="text-xs text-muted-foreground mt-1">Penghargaan diraih</p>
      </Card>
    </div>

    <Tabs default-value="akademik">
      <TabsList>
        <TabsTrigger value="akademik">Rekap Akademik</TabsTrigger>
        <TabsTrigger value="prestasi">Daftar Prestasi</TabsTrigger>
      </TabsList>
      <TabsContent value="akademik" class="mt-4">
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold">Unit Sekolah</TableHead>
                <TableHead class="font-semibold">Jenjang</TableHead>
                <TableHead class="font-semibold text-center">Siswa</TableHead>
                <TableHead class="font-semibold text-center">Rata² Nilai</TableHead>
                <TableHead class="font-semibold text-center">% Kelulusan</TableHead>
                <TableHead class="font-semibold text-center">Ekskul Aktif</TableHead>
                <TableHead class="font-semibold text-center">Prestasi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading"><TableRow v-for="i in 4" :key="i"><TableCell v-for="j in 7" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow></template>
              <template v-else>
                <TableRow v-for="s in data" :key="s.id" class="hover:bg-muted/30 transition-colors">
                  <TableCell class="font-semibold text-sm">{{ s.sekolah }}</TableCell>
                  <TableCell><Badge :class="jenjangColor[s.jenjang]">{{ s.jenjang }}</Badge></TableCell>
                  <TableCell class="text-center font-medium">{{ s.siswa }}</TableCell>
                  <TableCell class="text-center"><Badge :class="s.rataNilai>=80?'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400':'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'">{{ s.rataNilai }}</Badge></TableCell>
                  <TableCell class="text-center"><Badge :class="s.kelulusan>=95?'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400':s.kelulusan>=90?'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400':'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'">{{ s.kelulusan }}%</Badge></TableCell>
                  <TableCell class="text-center font-medium">{{ s.ekskul }}</TableCell>
                  <TableCell class="text-center"><Badge class="bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400">{{ s.prestasi }}</Badge></TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>
      <TabsContent value="prestasi" class="mt-4">
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold">Nama Lomba / Prestasi</TableHead>
                <TableHead class="font-semibold">Unit Sekolah</TableHead>
                <TableHead class="font-semibold text-center">Tingkat</TableHead>
                <TableHead class="font-semibold text-center">Hasil</TableHead>
                <TableHead class="font-semibold text-center">Periode</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading"><TableRow v-for="i in 4" :key="i"><TableCell v-for="j in 5" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow></template>
              <template v-else>
                <TableRow v-for="p in prestasiData" :key="p.id" class="hover:bg-muted/30 transition-colors">
                  <TableCell class="font-medium text-sm">{{ p.nama }}</TableCell>
                  <TableCell class="text-sm text-muted-foreground">{{ p.sekolah }}</TableCell>
                  <TableCell class="text-center"><Badge :class="tingkatColor[p.tingkat]">{{ p.tingkat }}</Badge></TableCell>
                  <TableCell class="text-center"><Badge class="bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400">🏆 {{ p.hasil }}</Badge></TableCell>
                  <TableCell class="text-center text-sm text-muted-foreground">{{ p.tgl }}</TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>
    </Tabs>
  </div>
</template>
