<script setup>
import { ref, computed, onMounted } from 'vue'
import { BookOpen, Download, Printer, Award, TrendingUp, ChevronUp, ChevronDown } from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { getFoundationAcademic } from '@/services/api/reports'

const isLoading = ref(true)
const data = ref([])
const prestasiData = ref([])
const selectedTahun = ref('2025/2026')
const selectedSemester = ref('1')

onMounted(async () => {
  try {
    const res = await getFoundationAcademic()
    data.value = res.akademik || []
    prestasiData.value = res.prestasi || []
  } catch (error) {
    console.error('Failed to fetch academic data:', error)
  } finally {
    isLoading.value = false
  }
})

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

    <StatCardGrid cols="4">
      <StatCard
        label="Rata² Nilai"
        :value="avgNilai"
        sub="Seluruh unit sekolah"
        :icon="TrendingUp"
        variant="primary"
        :delay="100"
      />
      <StatCard
        label="Kelulusan"
        :value="avgKelulusan + '%'"
        sub="Rata-rata ketuntasan"
        :icon="Award"
        variant="emerald"
        :delay="200"
      />
      <StatCard
        label="Total Ekskul"
        :value="data.reduce((s,k)=>s+k.ekskul,0)"
        sub="Ekstrakurikuler aktif"
        :icon="BookOpen"
        variant="default"
        color="slate"
        :delay="300"
      />
      <StatCard
        label="Prestasi"
        :value="totalPrestasi"
        sub="Penghargaan diraih"
        :icon="Award"
        variant="amber"
        :delay="400"
      />
    </StatCardGrid>

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
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
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
              <template v-if="isLoading"><TableRow v-for="(i, index) in 4" :key="i">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell><TableCell v-for="j in 8" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow></template>
              <template v-else>
                <TableRow v-for="(s, index) in data" :key="s.id" class="hover:bg-muted/30 transition-colors">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
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
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
                <TableHead class="font-semibold">Nama Lomba / Prestasi</TableHead>
                <TableHead class="font-semibold">Unit Sekolah</TableHead>
                <TableHead class="font-semibold text-center">Tingkat</TableHead>
                <TableHead class="font-semibold text-center">Hasil</TableHead>
                <TableHead class="font-semibold text-center">Periode</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading"><TableRow v-for="(i, index) in 4" :key="i">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell><TableCell v-for="j in 6" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow></template>
              <template v-else>
                <TableRow v-for="(p, index) in prestasiData" :key="p.id" class="hover:bg-muted/30 transition-colors">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
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
