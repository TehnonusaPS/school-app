<script setup>
import { ref, computed, onMounted } from 'vue'
import { Users, Download, Printer, UserPlus, UserMinus, TrendingUp, Search, ChevronUp, ChevronDown } from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Separator } from '@/components/ui/separator'

const mockData = [
  { id: 1, sekolah: 'SDN Tunas Bangsa', jenjang: 'SD', total: 412, laki: 210, perempuan: 202, baru: 95, keluar: 12, naik: 88, tinggal: 7 },
  { id: 2, sekolah: 'SMPN Harapan Ilmu', jenjang: 'SMP', total: 356, laki: 182, perempuan: 174, baru: 88, keluar: 8, naik: 80, tinggal: 8 },
  { id: 3, sekolah: 'SMAN Bina Prestasi', jenjang: 'SMA', total: 487, laki: 245, perempuan: 242, baru: 110, keluar: 15, naik: 100, tinggal: 10 },
  { id: 4, sekolah: 'SMK Teknologi Maju', jenjang: 'SMK', total: 298, laki: 195, perempuan: 103, baru: 72, keluar: 10, naik: 65, tinggal: 7 },
]

const isLoading = ref(true)
const data = ref([])
const searchQuery = ref('')
const selectedJenjang = ref('semua')
const selectedTahun = ref('2025/2026')
const sortField = ref('sekolah')
const sortDir = ref('asc')

onMounted(() => { setTimeout(() => { data.value = mockData; isLoading.value = false }, 500) })

const totalSiswa = computed(() => data.value.reduce((s, k) => s + k.total, 0))
const totalBaru = computed(() => data.value.reduce((s, k) => s + k.baru, 0))
const totalKeluar = computed(() => data.value.reduce((s, k) => s + k.keluar, 0))

const filteredData = computed(() => {
  let d = data.value.filter(k => {
    const matchJenjang = selectedJenjang.value === 'semua' || k.jenjang === selectedJenjang.value
    const matchSearch = !searchQuery.value || k.sekolah.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchJenjang && matchSearch
  })
  return [...d].sort((a, b) => {
    let va = a[sortField.value], vb = b[sortField.value]
    if (typeof va === 'string') va = va.toLowerCase()
    if (typeof vb === 'string') vb = vb.toLowerCase()
    return sortDir.value === 'asc' ? (va > vb ? 1 : -1) : (va < vb ? 1 : -1)
  })
})

function toggleSort(f) {
  if (sortField.value === f) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  else { sortField.value = f; sortDir.value = 'asc' }
}

const jenjangColor = { SD: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400', SMP: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400', SMA: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400', SMK: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400' }
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <Users class="size-6 text-primary" />
          Data Siswa Yayasan
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Statistik data siswa di seluruh unit sekolah di bawah yayasan.</p>
      </div>
      <div class="flex gap-2">
        <Button variant="outline" size="sm" class="gap-2"><Printer class="size-4" />Cetak</Button>
        <Button size="sm" class="gap-2"><Download class="size-4" />Ekspor</Button>
      </div>
    </div>

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
        <Label class="text-xs text-muted-foreground">Jenjang</Label>
        <Select v-model="selectedJenjang">
          <SelectTrigger class="w-[120px]"><SelectValue /></SelectTrigger>
          <SelectContent>
            <SelectItem value="semua">Semua</SelectItem>
            <SelectItem value="SD">SD</SelectItem>
            <SelectItem value="SMP">SMP</SelectItem>
            <SelectItem value="SMA">SMA</SelectItem>
            <SelectItem value="SMK">SMK</SelectItem>
          </SelectContent>
        </Select>
      </div>
      <div class="flex-1 min-w-[180px]">
        <Label class="text-xs text-muted-foreground">Cari Sekolah</Label>
        <div class="relative mt-1.5">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
          <Input v-model="searchQuery" placeholder="Nama sekolah..." class="pl-9 h-9" />
        </div>
      </div>
    </div>

    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Siswa</span><div class="p-1.5 bg-primary/10 rounded-lg"><Users class="size-4 text-primary" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-14" />
        <div v-else class="text-3xl font-bold text-primary">{{ totalSiswa }}</div>
        <p class="text-xs text-muted-foreground mt-1">Seluruh unit</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Siswa Baru</span><div class="p-1.5 bg-green-50 dark:bg-green-950/40 rounded-lg"><UserPlus class="size-4 text-green-600" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold text-green-600 dark:text-green-400">{{ totalBaru }}</div>
        <p class="text-xs text-muted-foreground mt-1">Tahun ini</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Siswa Keluar</span><div class="p-1.5 bg-red-50 dark:bg-red-950/40 rounded-lg"><UserMinus class="size-4 text-red-600" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-12" />
        <div v-else class="text-3xl font-bold text-red-600 dark:text-red-400">{{ totalKeluar }}</div>
        <p class="text-xs text-muted-foreground mt-1">Tahun ini</p>
      </Card>
      <Card class="p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2"><span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Unit Sekolah</span><div class="p-1.5 bg-muted rounded-lg"><TrendingUp class="size-4 text-muted-foreground" /></div></div>
        <Skeleton v-if="isLoading" class="h-8 w-10" />
        <div v-else class="text-3xl font-bold">{{ data.length }}</div>
        <p class="text-xs text-muted-foreground mt-1">Aktif</p>
      </Card>
    </div>

    <Card class="overflow-hidden">
      <div class="overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
                <TableHead class="font-semibold cursor-pointer select-none" @click="toggleSort('sekolah')">
                <div class="flex items-center gap-1">Unit Sekolah <component :is="sortField==='sekolah'&&sortDir==='desc'?ChevronDown:ChevronUp" :class="['size-3',sortField==='sekolah'?'text-primary':'text-muted-foreground/40']" /></div>
              </TableHead>
              <TableHead class="font-semibold">Jenjang</TableHead>
              <TableHead class="font-semibold text-center cursor-pointer select-none" @click="toggleSort('total')">
                <div class="flex items-center justify-center gap-1">Total <component :is="sortField==='total'&&sortDir==='desc'?ChevronDown:ChevronUp" :class="['size-3',sortField==='total'?'text-primary':'text-muted-foreground/40']" /></div>
              </TableHead>
              <TableHead class="font-semibold text-center">Laki-laki</TableHead>
              <TableHead class="font-semibold text-center">Perempuan</TableHead>
              <TableHead class="font-semibold text-center">Siswa Baru</TableHead>
              <TableHead class="font-semibold text-center">Keluar</TableHead>
              <TableHead class="font-semibold text-center">Naik Kelas</TableHead>
              <TableHead class="font-semibold text-center">Tinggal Kelas</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="isLoading">
              <TableRow v-for="(i, index) in 4" :key="i">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell><TableCell v-for="j in 10" :key="j"><Skeleton class="h-5 w-full" /></TableCell></TableRow>
            </template>
            <template v-else>
              <TableRow v-for="(s, index) in filteredData" :key="s.id" class="hover:bg-muted/30 transition-colors">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                <TableCell class="font-semibold text-sm">{{ s.sekolah }}</TableCell>
                <TableCell><Badge :class="jenjangColor[s.jenjang]">{{ s.jenjang }}</Badge></TableCell>
                <TableCell class="text-center font-bold text-primary">{{ s.total }}</TableCell>
                <TableCell class="text-center text-blue-600 dark:text-blue-400 font-medium">{{ s.laki }}</TableCell>
                <TableCell class="text-center text-pink-600 dark:text-pink-400 font-medium">{{ s.perempuan }}</TableCell>
                <TableCell class="text-center text-green-600 dark:text-green-400 font-semibold">+{{ s.baru }}</TableCell>
                <TableCell class="text-center text-red-600 dark:text-red-400 font-semibold">-{{ s.keluar }}</TableCell>
                <TableCell class="text-center font-medium">{{ s.naik }}</TableCell>
                <TableCell class="text-center">
                  <Badge v-if="s.tinggal > 0" class="bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400">{{ s.tinggal }}</Badge>
                  <span v-else class="text-muted-foreground text-sm">-</span>
                </TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </div>
    </Card>
  </div>
</template>
