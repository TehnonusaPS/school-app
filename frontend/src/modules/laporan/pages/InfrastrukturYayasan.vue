<script setup>
import { ref, computed, onMounted } from 'vue'
import { Building2, Download, Printer, Package, Wrench, CheckCircle2, AlertTriangle, Search } from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Progress } from '@/components/ui/progress'

const mockSarana = [
  { id: 1, sekolah: 'SDN Tunas Bangsa', jenjang: 'SD', ruangKelas: 14, perpustakaan: 1, lab: 1, toilet: 8, kondisiBaik: 85, kondisiRusak: 15 },
  { id: 2, sekolah: 'SMPN Harapan Ilmu', jenjang: 'SMP', ruangKelas: 18, perpustakaan: 1, lab: 3, toilet: 10, kondisiBaik: 78, kondisiRusak: 22 },
  { id: 3, sekolah: 'SMAN Bina Prestasi', jenjang: 'SMA', ruangKelas: 24, perpustakaan: 2, lab: 5, toilet: 14, kondisiBaik: 92, kondisiRusak: 8 },
  { id: 4, sekolah: 'SMK Teknologi Maju', jenjang: 'SMK', ruangKelas: 16, perpustakaan: 1, lab: 8, toilet: 10, kondisiBaik: 71, kondisiRusak: 29 },
]

const mockInventaris = [
  { id: 1, nama: 'Komputer / Laptop', sekolah: 'SMAN Bina Prestasi', jumlah: 45, kondisi: 'Baik', tahun: '2024' },
  { id: 2, nama: 'Proyektor', sekolah: 'SMPN Harapan Ilmu', jumlah: 18, kondisi: 'Baik', tahun: '2023' },
  { id: 3, nama: 'Meja Siswa', sekolah: 'SDN Tunas Bangsa', jumlah: 412, kondisi: 'Perlu Perbaikan', tahun: '2019' },
  { id: 4, nama: 'Peralatan Lab Kimia', sekolah: 'SMAN Bina Prestasi', jumlah: 5, kondisi: 'Baik', tahun: '2025' },
  { id: 5, nama: 'Mesin CNC', sekolah: 'SMK Teknologi Maju', jumlah: 3, kondisi: 'Perlu Perbaikan', tahun: '2020' },
  { id: 6, nama: 'Kursi Kantor', sekolah: 'SMPN Harapan Ilmu', jumlah: 50, kondisi: 'Baik', tahun: '2022' },
]

const isLoading = ref(true)
const saranaData = ref([])
const inventarisData = ref([])
const searchQuery = ref('')
const selectedTahun = ref('2026')

onMounted(() => { setTimeout(() => { saranaData.value = mockSarana; inventarisData.value = mockInventaris; isLoading.value = false }, 500) })

const totalRuangKelas = computed(() => saranaData.value.reduce((s,k)=>s+k.ruangKelas,0))
const avgKondisiBaik = computed(() => saranaData.value.length ? Math.round(saranaData.value.reduce((s,k)=>s+k.kondisiBaik,0)/saranaData.value.length) : 0)
const needRepair = computed(() => saranaData.value.filter(s => s.kondisiRusak > 20).length)

const filteredInventaris = computed(() => inventarisData.value.filter(i => !searchQuery.value || i.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) || i.sekolah.toLowerCase().includes(searchQuery.value.toLowerCase())))

const jenjangColor = { SD: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400', SMP: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400', SMA: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400', SMK: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400' }
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <Building2 class="size-6 text-primary" />
          Laporan Infrastruktur Yayasan
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Rekapitulasi inventaris sarana dan prasarana per unit sekolah.</p>
      </div>
      <div class="flex gap-2">
        <Button variant="outline" size="sm" class="gap-2"><Printer class="size-4" />Cetak</Button>
        <Button size="sm" class="gap-2"><Download class="size-4" />Ekspor</Button>
      </div>
    </div>

    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Ruang Kelas</span><div class="p-1.5 bg-primary/10 rounded-lg"><Building2 class="size-4 text-primary" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold text-primary">{{ totalRuangKelas }}</div>
        <p class="text-xs text-muted-foreground mt-1">Di seluruh unit</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Kondisi Baik</span><div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg"><CheckCircle2 class="size-4 text-green-600" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold text-green-600 dark:text-green-400">{{ avgKondisiBaik }}%</div>
        <p class="text-xs text-muted-foreground mt-1">Rata-rata kondisi</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Butuh Perbaikan</span><div class="p-1.5 bg-red-50 dark:bg-red-950/40 rounded-lg"><Wrench class="size-4 text-red-600" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-10" />
        <div v-else class="text-3xl font-bold text-red-600 dark:text-red-400">{{ needRepair }}</div>
        <p class="text-xs text-muted-foreground mt-1">Unit perlu perhatian</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Inventaris</span><div class="p-1.5 bg-muted rounded-lg"><Package class="size-4 text-muted-foreground" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold">{{ inventarisData.length }}</div>
        <p class="text-xs text-muted-foreground mt-1">Jenis barang tercatat</p>
      </Card>
    </div>

    <Tabs default-value="sarana">
      <TabsList>
        <TabsTrigger value="sarana">Sarana & Prasarana</TabsTrigger>
        <TabsTrigger value="inventaris">Inventaris Barang</TabsTrigger>
      </TabsList>

      <TabsContent value="sarana" class="mt-4">
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold">Unit Sekolah</TableHead>
                <TableHead class="font-semibold">Jenjang</TableHead>
                <TableHead class="font-semibold text-center">Ruang Kelas</TableHead>
                <TableHead class="font-semibold text-center">Perpustakaan</TableHead>
                <TableHead class="font-semibold text-center">Lab</TableHead>
                <TableHead class="font-semibold text-center">Toilet</TableHead>
                <TableHead class="font-semibold">Kondisi Sarana</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading"><TableRow v-for="i in 4" :key="i"><TableCell v-for="j in 7" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow></template>
              <template v-else>
                <TableRow v-for="s in saranaData" :key="s.id" class="hover:bg-muted/30 transition-colors">
                  <TableCell class="font-semibold text-sm">{{ s.sekolah }}</TableCell>
                  <TableCell><Badge :class="jenjangColor[s.jenjang]">{{ s.jenjang }}</Badge></TableCell>
                  <TableCell class="text-center font-medium">{{ s.ruangKelas }}</TableCell>
                  <TableCell class="text-center font-medium">{{ s.perpustakaan }}</TableCell>
                  <TableCell class="text-center font-medium">{{ s.lab }}</TableCell>
                  <TableCell class="text-center font-medium">{{ s.toilet }}</TableCell>
                  <TableCell>
                    <div class="flex items-center gap-2">
                      <div class="flex-1 h-1.5 rounded-full bg-muted overflow-hidden">
                        <div class="h-full rounded-full" :class="s.kondisiBaik>=80?'bg-green-500':s.kondisiBaik>=70?'bg-yellow-500':'bg-red-500'" :style="`width:${s.kondisiBaik}%`" />
                      </div>
                      <span class="text-xs font-semibold w-10 text-right" :class="s.kondisiBaik>=80?'text-green-600 dark:text-green-400':s.kondisiBaik>=70?'text-yellow-600 dark:text-yellow-400':'text-red-600 dark:text-red-400'">{{ s.kondisiBaik }}%</span>
                    </div>
                    <div class="flex items-center gap-1 mt-1">
                      <AlertTriangle v-if="s.kondisiRusak > 20" class="size-3 text-red-500 shrink-0" />
                      <span class="text-xs text-muted-foreground">{{ s.kondisiRusak }}% perlu perbaikan</span>
                    </div>
                  </TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>

      <TabsContent value="inventaris" class="mt-4 space-y-3">
        <div class="relative max-w-xs">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
          <Input v-model="searchQuery" placeholder="Cari barang / sekolah..." class="pl-9 h-9" />
        </div>
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold">Nama Barang</TableHead>
                <TableHead class="font-semibold">Unit Sekolah</TableHead>
                <TableHead class="font-semibold text-center">Jumlah</TableHead>
                <TableHead class="font-semibold text-center">Kondisi</TableHead>
                <TableHead class="font-semibold text-center">Tahun Pengadaan</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading"><TableRow v-for="i in 5" :key="i"><TableCell v-for="j in 5" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow></template>
              <template v-else>
                <TableRow v-for="item in filteredInventaris" :key="item.id" class="hover:bg-muted/30 transition-colors">
                  <TableCell class="font-medium text-sm">{{ item.nama }}</TableCell>
                  <TableCell class="text-sm text-muted-foreground">{{ item.sekolah }}</TableCell>
                  <TableCell class="text-center font-semibold">{{ item.jumlah }}</TableCell>
                  <TableCell class="text-center">
                    <Badge :class="item.kondisi==='Baik'?'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400':'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'">{{ item.kondisi }}</Badge>
                  </TableCell>
                  <TableCell class="text-center text-muted-foreground text-sm">{{ item.tahun }}</TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>
    </Tabs>
  </div>
</template>
