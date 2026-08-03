<script setup>
import { ref, computed, onMounted } from 'vue'
import { Users, Download, Printer, UserCheck, Briefcase, Star, GraduationCap, Search } from 'lucide-vue-next'
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
import { getFoundationHR } from '@/services/api/reports'

const isLoading = ref(true)
const guruData = ref([])
const absensiData = ref([])
const selectedTahun = ref('2025/2026')
const searchQuery = ref('')

onMounted(async () => {
  try {
    const res = await getFoundationHR()
    guruData.value = res.guru || []
    absensiData.value = res.absensi || []
  } catch (error) {
    console.error('Failed to fetch HR data:', error)
  } finally {
    isLoading.value = false
  }
})

const totalGuru = computed(() => guruData.value.reduce((s,k)=>s+k.totalGuru,0))
const totalStaf = computed(() => guruData.value.reduce((s,k)=>s+k.totalStaf,0))
const totalSertifikasi = computed(() => guruData.value.reduce((s,k)=>s+k.sertifikasi,0))

const filteredAbsensi = computed(() => absensiData.value.filter(g => !searchQuery.value || g.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) || g.sekolah.toLowerCase().includes(searchQuery.value.toLowerCase())))

const jenjangColor = { SD: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400', SMP: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400', SMA: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400', SMK: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400' }
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <Users class="size-6 text-primary" />
          Laporan SDM Yayasan
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Data dan laporan kinerja pendidik dan tenaga kependidikan di bawah yayasan.</p>
      </div>
      <div class="flex gap-2">
        <Button variant="outline" size="sm" class="gap-2"><Printer class="size-4" />Cetak</Button>
        <Button size="sm" class="gap-2"><Download class="size-4" />Ekspor</Button>
      </div>
    </div>

    <div class="flex flex-wrap gap-3 items-end">
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Tahun Ajaran</Label>
        <Select v-model="selectedTahun"><SelectTrigger class="w-[140px]"><SelectValue /></SelectTrigger><SelectContent><SelectItem value="2025/2026">2025/2026</SelectItem></SelectContent></Select>
      </div>
    </div>

    <StatCardGrid cols="4">
      <StatCard
        label="Total Guru"
        :value="totalGuru"
        sub="Pendidik aktif"
        :icon="GraduationCap"
        variant="primary"
        :delay="100"
      />
      <StatCard
        label="Tenaga Kependidikan"
        :value="totalStaf"
        sub="Staff aktif"
        :icon="Briefcase"
        variant="default"
        color="slate"
        :delay="200"
      />
      <StatCard
        label="Tersertifikasi"
        :value="totalSertifikasi"
        sub="Guru bersertifikat"
        :icon="Star"
        variant="emerald"
        :delay="300"
      />
      <StatCard
        label="Unit Sekolah"
        :value="guruData.length"
        sub="Aktif dalam yayasan"
        :icon="UserCheck"
        variant="default"
        color="slate"
        :delay="400"
      />
    </StatCardGrid>

    <Tabs default-value="sdm">
      <TabsList>
        <TabsTrigger value="sdm">Data SDM per Sekolah</TabsTrigger>
        <TabsTrigger value="absensi">Kehadiran Guru</TabsTrigger>
      </TabsList>
      <TabsContent value="sdm" class="mt-4">
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
                <TableHead class="font-semibold">Unit Sekolah</TableHead>
                <TableHead class="font-semibold">Jenjang</TableHead>
                <TableHead class="font-semibold text-center">Guru</TableHead>
                <TableHead class="font-semibold text-center">Staff</TableHead>
                <TableHead class="font-semibold text-center">S1</TableHead>
                <TableHead class="font-semibold text-center">S2+</TableHead>
                <TableHead class="font-semibold text-center">Tetap</TableHead>
                <TableHead class="font-semibold text-center">Honorer</TableHead>
                <TableHead class="font-semibold text-center">Sertifikasi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading"><TableRow v-for="(i, index) in 4" :key="i">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell><TableCell v-for="j in 10" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow></template>
              <template v-else>
                <TableRow v-for="(s, index) in guruData" :key="s.id" class="hover:bg-muted/30 transition-colors">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                  <TableCell class="font-semibold text-sm">{{ s.sekolah }}</TableCell>
                  <TableCell><Badge :class="jenjangColor[s.jenjang]">{{ s.jenjang }}</Badge></TableCell>
                  <TableCell class="text-center font-medium">{{ s.totalGuru }}</TableCell>
                  <TableCell class="text-center font-medium">{{ s.totalStaf }}</TableCell>
                  <TableCell class="text-center">{{ s.s1 }}</TableCell>
                  <TableCell class="text-center">{{ s.s2 }}</TableCell>
                  <TableCell class="text-center text-blue-600 dark:text-blue-400 font-medium">{{ s.tetap }}</TableCell>
                  <TableCell class="text-center text-orange-600 dark:text-orange-400 font-medium">{{ s.honorer }}</TableCell>
                  <TableCell class="text-center"><Badge class="bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400">{{ s.sertifikasi }}</Badge></TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>
      <TabsContent value="absensi" class="mt-4 space-y-3">
        <div class="relative max-w-xs">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
          <Input v-model="searchQuery" placeholder="Cari guru / sekolah..." class="pl-9 h-9" />
        </div>
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
                <TableHead class="font-semibold">Nama Guru</TableHead>
                <TableHead class="font-semibold">Sekolah</TableHead>
                <TableHead class="font-semibold">Mata Pelajaran</TableHead>
                <TableHead class="font-semibold text-center">Hadir</TableHead>
                <TableHead class="font-semibold text-center">Terlambat</TableHead>
                <TableHead class="font-semibold text-center">Izin</TableHead>
                <TableHead class="font-semibold text-center">Alpa</TableHead>
                <TableHead class="font-semibold text-center">% Hadir</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading"><TableRow v-for="(i, index) in 5" :key="i">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell><TableCell v-for="j in 9" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow></template>
              <template v-else>
                <TableRow v-for="(g, index) in filteredAbsensi" :key="g.id" class="hover:bg-muted/30 transition-colors">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                  <TableCell class="font-medium text-sm">{{ g.nama }}</TableCell>
                  <TableCell class="text-sm text-muted-foreground">{{ g.sekolah }}</TableCell>
                  <TableCell><Badge variant="outline" class="text-xs">{{ g.mapel }}</Badge></TableCell>
                  <TableCell class="text-center font-semibold text-green-600 dark:text-green-400">{{ g.hadir }}</TableCell>
                  <TableCell class="text-center font-semibold text-yellow-600 dark:text-yellow-400">{{ g.terlambat }}</TableCell>
                  <TableCell class="text-center font-semibold text-blue-600 dark:text-blue-400">{{ g.izin }}</TableCell>
                  <TableCell class="text-center font-semibold text-red-600 dark:text-red-400">{{ g.alpa }}</TableCell>
                  <TableCell class="text-center"><Badge :class="g.persen>=95?'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400':g.persen>=80?'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400':'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'">{{ g.persen }}%</Badge></TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>
    </Tabs>
  </div>
</template>
