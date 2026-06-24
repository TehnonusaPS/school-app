<script setup>
import { ref, computed, onMounted } from 'vue'
import { Wallet, Download, Printer, TrendingUp, TrendingDown, ArrowUpRight, ArrowDownRight, Building2 } from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

const mockData = [
  { id: 1, sekolah: 'SDN Tunas Bangsa', jenjang: 'SD', spp: 85000000, bos: 45000000, donasi: 10000000, gaji: 62000000, operasional: 15000000, pemeliharaan: 8000000 },
  { id: 2, sekolah: 'SMPN Harapan Ilmu', jenjang: 'SMP', spp: 112000000, bos: 62000000, donasi: 8000000, gaji: 98000000, operasional: 22000000, pemeliharaan: 12000000 },
  { id: 3, sekolah: 'SMAN Bina Prestasi', jenjang: 'SMA', spp: 156000000, bos: 82000000, donasi: 15000000, gaji: 148000000, operasional: 35000000, pemeliharaan: 18000000 },
  { id: 4, sekolah: 'SMK Teknologi Maju', jenjang: 'SMK', spp: 98000000, bos: 54000000, donasi: 6000000, gaji: 85000000, operasional: 28000000, pemeliharaan: 14000000 },
]

const isLoading = ref(true)
const data = ref([])
const selectedBulan = ref('5')
const selectedTahun = ref('2026')

onMounted(() => { setTimeout(() => { data.value = mockData; isLoading.value = false }, 500) })

function formatRp(v) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v) }

const getPemasukan = (s) => s.spp + s.bos + s.donasi
const getPengeluaran = (s) => s.gaji + s.operasional + s.pemeliharaan
const getSaldo = (s) => getPemasukan(s) - getPengeluaran(s)

const totalPemasukan = computed(() => data.value.reduce((s, k) => s + getPemasukan(k), 0))
const totalPengeluaran = computed(() => data.value.reduce((s, k) => s + getPengeluaran(k), 0))
const totalSaldo = computed(() => totalPemasukan.value - totalPengeluaran.value)

const bulanList = [
  { v: '1', l: 'Januari' }, { v: '2', l: 'Februari' }, { v: '3', l: 'Maret' },
  { v: '4', l: 'April' }, { v: '5', l: 'Mei' }, { v: '6', l: 'Juni' },
  { v: '7', l: 'Juli' }, { v: '8', l: 'Agustus' }, { v: '9', l: 'September' },
  { v: '10', l: 'Oktober' }, { v: '11', l: 'November' }, { v: '12', l: 'Desember' },
]
const jenjangColor = { SD: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400', SMP: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400', SMA: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400', SMK: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400' }
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between border-b pb-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <Wallet class="size-6 text-primary" />
          Laporan Keuangan Yayasan
        </h1>
        <p class="text-muted-foreground mt-1 text-sm">Laporan komprehensif pemasukan dan transaksi keuangan seluruh unit sekolah.</p>
      </div>
      <div class="flex gap-2">
        <Button variant="outline" size="sm" class="gap-2"><Printer class="size-4" />Cetak</Button>
        <Button size="sm" class="gap-2"><Download class="size-4" />Ekspor</Button>
      </div>
    </div>

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
    </div>

    <div class="grid gap-4 grid-cols-1 md:grid-cols-3">
      <Card class="p-5 border-green-200 dark:border-green-900/40 bg-green-50/30 dark:bg-green-950/10 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-green-700 dark:text-green-400">Total Pemasukan</span>
          <div class="p-2 bg-green-100 dark:bg-green-900/40 rounded-xl"><ArrowUpRight class="size-5 text-green-600" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-9 w-40" />
        <div v-else class="text-xl font-bold text-green-700 dark:text-green-400">{{ formatRp(totalPemasukan) }}</div>
        <p class="text-xs text-muted-foreground mt-1">SPP + BOS + Donasi</p>
      </Card>
      <Card class="p-5 border-red-200 dark:border-red-900/40 bg-red-50/30 dark:bg-red-950/10 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-red-700 dark:text-red-400">Total Pengeluaran</span>
          <div class="p-2 bg-red-100 dark:bg-red-900/40 rounded-xl"><ArrowDownRight class="size-5 text-red-600" /></div>
        </div>
        <Skeleton v-if="isLoading" class="h-9 w-40" />
        <div v-else class="text-xl font-bold text-red-700 dark:text-red-400">{{ formatRp(totalPengeluaran) }}</div>
        <p class="text-xs text-muted-foreground mt-1">Gaji + Operasional + Pemeliharaan</p>
      </Card>
      <Card class="p-5 hover:shadow-md transition-shadow" :class="totalSaldo >= 0 ? 'border-blue-200 dark:border-blue-900/40 bg-blue-50/30 dark:bg-blue-950/10' : 'border-red-200 dark:border-red-900/40'">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium" :class="totalSaldo >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400'">Saldo Bersih Yayasan</span>
          <div class="p-2 rounded-xl" :class="totalSaldo >= 0 ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-red-100 dark:bg-red-900/40'">
            <component :is="totalSaldo >= 0 ? TrendingUp : TrendingDown" class="size-5" :class="totalSaldo >= 0 ? 'text-blue-600' : 'text-red-600'" />
          </div>
        </div>
        <Skeleton v-if="isLoading" class="h-9 w-40" />
        <div v-else class="text-xl font-bold" :class="totalSaldo >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400'">{{ formatRp(Math.abs(totalSaldo)) }}</div>
        <p class="text-xs mt-1 text-muted-foreground">{{ totalSaldo >= 0 ? 'Surplus' : 'Defisit' }}</p>
      </Card>
    </div>

    <Card class="overflow-hidden">
      <div class="overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow class="bg-muted/50">
                <TableHead class="font-semibold w-[50px] text-center">No</TableHead>
                <TableHead class="font-semibold">Unit Sekolah</TableHead>
              <TableHead class="font-semibold">Jenjang</TableHead>
              <TableHead class="font-semibold text-right">SPP</TableHead>
              <TableHead class="font-semibold text-right">BOS</TableHead>
              <TableHead class="font-semibold text-right">Donasi</TableHead>
              <TableHead class="font-semibold text-right bg-green-50/50 dark:bg-green-950/10">Total Masuk</TableHead>
              <TableHead class="font-semibold text-right">Gaji</TableHead>
              <TableHead class="font-semibold text-right">Operasional</TableHead>
              <TableHead class="font-semibold text-right">Pemeliharaan</TableHead>
              <TableHead class="font-semibold text-right bg-red-50/50 dark:bg-red-950/10">Total Keluar</TableHead>
              <TableHead class="font-semibold text-right">Saldo</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="isLoading">
              <TableRow v-for="(i, index) in 4" :key="i">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                <TableCell v-for="j in 12" :key="j"><Skeleton class="h-5 w-full" /></TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow v-for="(s, index) in data" :key="s.id" class="hover:bg-muted/30 transition-colors">
                <TableCell class="text-center text-muted-foreground text-xs">{{ index + 1 }}</TableCell>
                <TableCell class="font-semibold text-sm">{{ s.sekolah }}</TableCell>
                <TableCell><Badge :class="jenjangColor[s.jenjang]">{{ s.jenjang }}</Badge></TableCell>
                <TableCell class="text-right text-sm">{{ formatRp(s.spp) }}</TableCell>
                <TableCell class="text-right text-sm">{{ formatRp(s.bos) }}</TableCell>
                <TableCell class="text-right text-sm">{{ formatRp(s.donasi) }}</TableCell>
                <TableCell class="text-right font-semibold text-green-600 dark:text-green-400 bg-green-50/30 dark:bg-green-950/10">{{ formatRp(getPemasukan(s)) }}</TableCell>
                <TableCell class="text-right text-sm">{{ formatRp(s.gaji) }}</TableCell>
                <TableCell class="text-right text-sm">{{ formatRp(s.operasional) }}</TableCell>
                <TableCell class="text-right text-sm">{{ formatRp(s.pemeliharaan) }}</TableCell>
                <TableCell class="text-right font-semibold text-red-600 dark:text-red-400 bg-red-50/30 dark:bg-red-950/10">{{ formatRp(getPengeluaran(s)) }}</TableCell>
                <TableCell class="text-right font-bold" :class="getSaldo(s) >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400'">{{ formatRp(getSaldo(s)) }}</TableCell>
              </TableRow>
              <TableRow class="bg-muted/50 font-bold border-t-2">
                <TableCell colspan="3" class="font-bold">TOTAL</TableCell>
                <TableCell class="text-right font-bold">{{ formatRp(data.reduce((s,k)=>s+k.spp,0)) }}</TableCell>
                <TableCell class="text-right font-bold">{{ formatRp(data.reduce((s,k)=>s+k.bos,0)) }}</TableCell>
                <TableCell class="text-right font-bold">{{ formatRp(data.reduce((s,k)=>s+k.donasi,0)) }}</TableCell>
                <TableCell class="text-right font-bold text-green-700 dark:text-green-400 bg-green-50/30">{{ formatRp(totalPemasukan) }}</TableCell>
                <TableCell class="text-right font-bold">{{ formatRp(data.reduce((s,k)=>s+k.gaji,0)) }}</TableCell>
                <TableCell class="text-right font-bold">{{ formatRp(data.reduce((s,k)=>s+k.operasional,0)) }}</TableCell>
                <TableCell class="text-right font-bold">{{ formatRp(data.reduce((s,k)=>s+k.pemeliharaan,0)) }}</TableCell>
                <TableCell class="text-right font-bold text-red-700 dark:text-red-400 bg-red-50/30">{{ formatRp(totalPengeluaran) }}</TableCell>
                <TableCell class="text-right font-bold text-blue-700 dark:text-blue-400">{{ formatRp(totalSaldo) }}</TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </div>
    </Card>
  </div>
</template>
