<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Wallet,
  Download,
  Printer,
  TrendingUp,
  TrendingDown,
  ArrowUpRight,
  ArrowDownRight,
  Search,
  Filter,
  Calendar as CalendarIcon,
} from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
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
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs'
import { Separator } from '@/components/ui/separator'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Calendar } from '@/components/ui/calendar'
import { today, getLocalTimeZone, startOfMonth, endOfMonth } from '@internationalized/date'

const mockPemasukan = [
  { id: 1, tanggal: '2 Jan 2026', keterangan: 'SPP Januari - XI IPA 1', kategori: 'SPP', jumlah: 12500000, status: 'lunas' },
  { id: 2, tanggal: '3 Jan 2026', keterangan: 'SPP Januari - XI IPA 2', kategori: 'SPP', jumlah: 11800000, status: 'lunas' },
  { id: 3, tanggal: '5 Jan 2026', keterangan: 'Dana BOS Triwulan I', kategori: 'BOS', jumlah: 45000000, status: 'lunas' },
  { id: 4, tanggal: '10 Jan 2026', keterangan: 'SPP Januari - XII IPA 1', kategori: 'SPP', jumlah: 13200000, status: 'sebagian' },
  { id: 5, tanggal: '15 Jan 2026', keterangan: 'Donasi Orang Tua Siswa', kategori: 'Donasi', jumlah: 5000000, status: 'lunas' },
]

const mockPengeluaran = [
  { id: 1, tanggal: '3 Jan 2026', keterangan: 'Gaji Guru & Staf Januari', kategori: 'Gaji', jumlah: 38000000, status: 'dibayar' },
  { id: 2, tanggal: '5 Jan 2026', keterangan: 'Listrik & Air Januari', kategori: 'Operasional', jumlah: 3200000, status: 'dibayar' },
  { id: 3, tanggal: '8 Jan 2026', keterangan: 'Pembelian ATK & Bahan Ajar', kategori: 'Operasional', jumlah: 1850000, status: 'dibayar' },
  { id: 4, tanggal: '12 Jan 2026', keterangan: 'Perbaikan Fasilitas Perpustakaan', kategori: 'Pemeliharaan', jumlah: 4500000, status: 'dibayar' },
  { id: 5, tanggal: '20 Jan 2026', keterangan: 'Kegiatan Ekstrakurikuler', kategori: 'Kegiatan', jumlah: 2200000, status: 'dibayar' },
]

const isLoading = ref(true)
const pemasukan = ref([])
const pengeluaran = ref([])
const selectedBulan = ref('1')
const selectedTahun = ref('2026')
const searchQuery = ref('')

onMounted(() => {
  setTimeout(() => {
    pemasukan.value = mockPemasukan
    pengeluaran.value = mockPengeluaran
    isLoading.value = false
  }, 500)
})

function formatRp(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

const totalPemasukan = computed(() => pemasukan.value.reduce((s, i) => s + i.jumlah, 0))
const totalPengeluaran = computed(() => pengeluaran.value.reduce((s, i) => s + i.jumlah, 0))
const saldo = computed(() => totalPemasukan.value - totalPengeluaran.value)

const filteredPemasukan = computed(() =>
  pemasukan.value.filter(i => !searchQuery.value || i.keterangan.toLowerCase().includes(searchQuery.value.toLowerCase()))
)
const filteredPengeluaran = computed(() =>
  pengeluaran.value.filter(i => !searchQuery.value || i.keterangan.toLowerCase().includes(searchQuery.value.toLowerCase()))
)

const bulanList = [
  { v: '1', l: 'Januari' }, { v: '2', l: 'Februari' }, { v: '3', l: 'Maret' },
  { v: '4', l: 'April' }, { v: '5', l: 'Mei' }, { v: '6', l: 'Juni' },
  { v: '7', l: 'Juli' }, { v: '8', l: 'Agustus' }, { v: '9', l: 'September' },
  { v: '10', l: 'Oktober' }, { v: '11', l: 'November' }, { v: '12', l: 'Desember' },
]
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <Wallet class="size-6 text-primary" />
          Laporan Keuangan
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Rekap pemasukan, pengeluaran, dan neraca keuangan sekolah.</p>
      </div>
      <div class="flex gap-2 shrink-0">
        <Button variant="outline" size="sm" class="gap-2"><Printer class="size-4" />Cetak</Button>
        <Button size="sm" class="gap-2"><Download class="size-4" />Ekspor Excel</Button>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 items-end">
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Bulan</Label>
        <Select v-model="selectedBulan">
          <SelectTrigger class="w-[130px]"><SelectValue /></SelectTrigger>
          <SelectContent>
            <SelectItem v-for="b in bulanList" :key="b.v" :value="b.v">{{ b.l }}</SelectItem>
          </SelectContent>
        </Select>
      </div>
      <div class="flex flex-col gap-1.5">
        <Label class="text-xs text-muted-foreground">Tahun</Label>
        <Select v-model="selectedTahun">
          <SelectTrigger class="w-[100px]"><SelectValue /></SelectTrigger>
          <SelectContent>
            <SelectItem value="2026">2026</SelectItem>
            <SelectItem value="2025">2025</SelectItem>
          </SelectContent>
        </Select>
      </div>
      <div class="flex-1 min-w-[180px]">
        <Label class="text-xs text-muted-foreground">Cari Transaksi</Label>
        <div class="relative mt-1.5">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
          <Input v-model="searchQuery" placeholder="Keterangan transaksi..." class="pl-9 h-9" />
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid gap-4 grid-cols-1 md:grid-cols-3">
      <Card class="p-5 border-green-200 dark:border-green-900/40 bg-green-50/30 dark:bg-green-950/10 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-green-700 dark:text-green-400">Total Pemasukan</span>
          <div class="p-2 bg-green-100 dark:bg-green-900/40 rounded-xl">
            <ArrowUpRight class="size-5 text-green-600 dark:text-green-400" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-9 w-40" />
        <div v-else class="text-2xl font-bold text-green-700 dark:text-green-400">{{ formatRp(totalPemasukan) }}</div>
        <p class="text-xs text-green-600 dark:text-green-500 mt-1">{{ pemasukan.length }} transaksi</p>
      </Card>

      <Card class="p-5 border-red-200 dark:border-red-900/40 bg-red-50/30 dark:bg-red-950/10 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-red-700 dark:text-red-400">Total Pengeluaran</span>
          <div class="p-2 bg-red-100 dark:bg-red-900/40 rounded-xl">
            <ArrowDownRight class="size-5 text-red-600 dark:text-red-400" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-9 w-40" />
        <div v-else class="text-2xl font-bold text-red-700 dark:text-red-400">{{ formatRp(totalPengeluaran) }}</div>
        <p class="text-xs text-red-600 dark:text-red-500 mt-1">{{ pengeluaran.length }} transaksi</p>
      </Card>

      <Card class="p-5 hover:shadow-md transition-shadow" :class="saldo >= 0 ? 'border-blue-200 dark:border-blue-900/40 bg-blue-50/30 dark:bg-blue-950/10' : 'border-red-200 dark:border-red-900/40 bg-red-50/30 dark:bg-red-950/10'">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium" :class="saldo >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400'">Saldo Bersih</span>
          <div class="p-2 rounded-xl" :class="saldo >= 0 ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-red-100 dark:bg-red-900/40'">
            <component :is="saldo >= 0 ? TrendingUp : TrendingDown" class="size-5" :class="saldo >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400'" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-9 w-40" />
        <div v-else class="text-2xl font-bold" :class="saldo >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400'">{{ formatRp(Math.abs(saldo)) }}</div>
        <p class="text-xs mt-1" :class="saldo >= 0 ? 'text-blue-600 dark:text-blue-500' : 'text-red-600 dark:text-red-500'">{{ saldo >= 0 ? 'Surplus' : 'Defisit' }}</p>
      </Card>
    </div>

    <!-- Tabs -->
    <Tabs default-value="pemasukan">
      <TabsList>
        <TabsTrigger value="pemasukan">Pemasukan</TabsTrigger>
        <TabsTrigger value="pengeluaran">Pengeluaran</TabsTrigger>
      </TabsList>

      <TabsContent value="pemasukan" class="mt-4">
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
                <TableHead class="font-semibold">Tanggal</TableHead>
                <TableHead class="font-semibold">Keterangan</TableHead>
                <TableHead class="font-semibold">Kategori</TableHead>
                <TableHead class="font-semibold text-right">Jumlah</TableHead>
                <TableHead class="font-semibold text-center">Status</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading">
                <TableRow v-for="(i, index) in 5" :key="i">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                  <TableCell v-for="j in 6" :key="j"><Skeleton class="h-5 w-full" /></TableCell>
                </TableRow>
              </template>
              <template v-else>
                <TableRow v-for="(item, index) in filteredPemasukan" :key="item.id" class="hover:bg-muted/30 transition-colors">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                  <TableCell class="text-sm text-muted-foreground">{{ item.tanggal }}</TableCell>
                  <TableCell class="font-medium text-sm">{{ item.keterangan }}</TableCell>
                  <TableCell><Badge variant="outline" class="text-xs">{{ item.kategori }}</Badge></TableCell>
                  <TableCell class="text-right font-semibold text-green-600 dark:text-green-400">{{ formatRp(item.jumlah) }}</TableCell>
                  <TableCell class="text-center">
                    <Badge :class="item.status === 'lunas' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'">
                      {{ item.status === 'lunas' ? 'Lunas' : 'Sebagian' }}
                    </Badge>
                  </TableCell>
                </TableRow>
                <TableRow class="bg-muted/30 font-bold">
                  <TableCell colspan="4" class="font-bold">Total Pemasukan</TableCell>
                  <TableCell class="text-right font-bold text-green-700 dark:text-green-400">{{ formatRp(totalPemasukan) }}</TableCell>
                  <TableCell />
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>

      <TabsContent value="pengeluaran" class="mt-4">
        <Card class="overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
                <TableHead class="font-semibold">Tanggal</TableHead>
                <TableHead class="font-semibold">Keterangan</TableHead>
                <TableHead class="font-semibold">Kategori</TableHead>
                <TableHead class="font-semibold text-right">Jumlah</TableHead>
                <TableHead class="font-semibold text-center">Status</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="isLoading">
                <TableRow v-for="(i, index) in 5" :key="i">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                  <TableCell v-for="j in 6" :key="j"><Skeleton class="h-5 w-full" /></TableCell>
                </TableRow>
              </template>
              <template v-else>
                <TableRow v-for="(item, index) in filteredPengeluaran" :key="item.id" class="hover:bg-muted/30 transition-colors">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                  <TableCell class="text-sm text-muted-foreground">{{ item.tanggal }}</TableCell>
                  <TableCell class="font-medium text-sm">{{ item.keterangan }}</TableCell>
                  <TableCell><Badge variant="outline" class="text-xs">{{ item.kategori }}</Badge></TableCell>
                  <TableCell class="text-right font-semibold text-red-600 dark:text-red-400">{{ formatRp(item.jumlah) }}</TableCell>
                  <TableCell class="text-center">
                    <Badge class="bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400">Dibayar</Badge>
                  </TableCell>
                </TableRow>
                <TableRow class="bg-muted/30 font-bold">
                  <TableCell colspan="4" class="font-bold">Total Pengeluaran</TableCell>
                  <TableCell class="text-right font-bold text-red-700 dark:text-red-400">{{ formatRp(totalPengeluaran) }}</TableCell>
                  <TableCell />
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>
    </Tabs>
  </div>
</template>
