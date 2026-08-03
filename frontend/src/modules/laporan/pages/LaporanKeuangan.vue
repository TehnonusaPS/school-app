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
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import { getSchoolFinance } from '@/services/api/reports'

const isLoading = ref(true)
const pemasukan = ref([])
const pengeluaran = ref([])
const selectedBulan = ref('1')
const selectedTahun = ref('2026')
const searchQuery = ref('')

onMounted(async () => {
  try {
    const res = await getSchoolFinance()
    pemasukan.value = res.pemasukan || []
    pengeluaran.value = res.pengeluaran || []
  } catch (error) {
    console.error('Failed to fetch school finance data:', error)
  } finally {
    isLoading.value = false
  }
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
    <StatCardGrid cols="3">
      <StatCard
        label="Total Pemasukan"
        :value="isLoading ? '' : formatRp(totalPemasukan)"
        :sub="`${pemasukan.length} transaksi`"
        :icon="ArrowUpRight"
        variant="emerald"
        :loading="isLoading"
      />
      <StatCard
        label="Total Pengeluaran"
        :value="isLoading ? '' : formatRp(totalPengeluaran)"
        :sub="`${pengeluaran.length} transaksi`"
        :icon="ArrowDownRight"
        variant="destructive"
        :loading="isLoading"
      />
      <StatCard
        label="Saldo Bersih"
        :value="isLoading ? '' : formatRp(Math.abs(saldo))"
        :sub="saldo >= 0 ? 'Surplus' : 'Defisit'"
        :icon="saldo >= 0 ? TrendingUp : TrendingDown"
        :variant="saldo >= 0 ? 'primary' : 'destructive'"
        :loading="isLoading"
      />
    </StatCardGrid>

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
