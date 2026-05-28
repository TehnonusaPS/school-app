<script setup>
import {
  Download, Landmark, Banknote, AlertCircle, Users, Search, Filter,
  CheckCircle2, Eye
} from 'lucide-vue-next'

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
</script>

<template>
  <div class="space-y-8 pb-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Riwayat Pembayaran Siswa</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola data tagihan pendidikan dan pantau realisasi pembayaran institusi Anda.</p>
      </div>
      <Button variant="outline" class="flex items-center gap-2 dark:border-slate-800 dark:text-slate-200 dark:hover:bg-slate-800">
        <Download class="w-4 h-4" />
        Export Rekapitulasi
      </Button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <Card class="shadow-sm border-slate-200/60 dark:border-slate-800 dark:bg-slate-950">
        <CardContent class="p-5">
          <div class="flex justify-between items-start mb-4">
            <div class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-lg">
              <Landmark class="w-5 h-5 text-slate-700 dark:text-slate-300" />
            </div>
            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50/80 dark:bg-emerald-950/50 px-2 py-0.5 rounded-md">+4.2%</span>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Total Tagihan Bulan Ini</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-50">Rp 425.800.000</h3>
          </div>
        </CardContent>
      </Card>
      <Card class="shadow-sm border-slate-200/60 dark:border-slate-800 dark:bg-slate-950">
        <CardContent class="p-5">
          <div class="flex justify-between items-start mb-4">
            <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg">
              <Banknote class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50/80 dark:bg-emerald-950/50 px-2 py-0.5 rounded-md">78% Terbayar</span>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Total Terbayar</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-50">Rp 332.124.000</h3>
          </div>
        </CardContent>
      </Card>
      <Card class="shadow-sm border-slate-200/60 dark:border-slate-800 dark:bg-slate-950">
        <CardContent class="p-5">
          <div class="flex justify-between items-start mb-4">
            <div class="p-2.5 bg-red-50 dark:bg-red-950/50 rounded-lg">
              <AlertCircle class="w-5 h-5 text-red-500 dark:text-red-400" />
            </div>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Sisa Tunggakan</p>
            <h3 class="text-2xl font-bold text-red-600 dark:text-red-400">Rp 93.676.000</h3>
          </div>
        </CardContent>
      </Card>
      <Card class="shadow-sm border-slate-200/60 dark:border-slate-800 dark:bg-slate-950">
        <CardContent class="p-5">
          <div class="flex justify-between items-start mb-4">
            <div class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-lg">
              <Users class="w-5 h-5 text-slate-600 dark:text-slate-400" />
            </div>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Siswa Belum Bayar</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-50">142 Siswa</h3>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Filters -->
    <div class="flex flex-col lg:flex-row gap-3 bg-slate-50/50 dark:bg-slate-900/30 p-2 rounded-xl border border-slate-100 dark:border-slate-800">
      <div class="relative flex-1">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" />
        <Input placeholder="Cari berdasarkan Nama Siswa atau Kelas..." class="pl-9 bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800 shadow-sm dark:text-slate-200 dark:placeholder-slate-500" />
      </div>
      <div class="flex flex-wrap sm:flex-nowrap gap-3">
        <Select>
          <SelectTrigger class="w-full sm:w-[150px] bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800 shadow-sm dark:text-slate-200">
            <SelectValue placeholder="Semua Kelas" />
          </SelectTrigger>
          <SelectContent class="dark:bg-slate-950 dark:border-slate-800">
            <SelectItem value="all" class="dark:focus:bg-slate-800">Semua Kelas</SelectItem>
            <SelectItem value="10" class="dark:focus:bg-slate-800">Kelas 10</SelectItem>
            <SelectItem value="11" class="dark:focus:bg-slate-800">Kelas 11</SelectItem>
            <SelectItem value="12" class="dark:focus:bg-slate-800">Kelas 12</SelectItem>
          </SelectContent>
        </Select>
        <Select>
          <SelectTrigger class="w-full sm:w-[150px] bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800 shadow-sm dark:text-slate-200">
            <SelectValue placeholder="Status: Semua" />
          </SelectTrigger>
          <SelectContent class="dark:bg-slate-950 dark:border-slate-800">
            <SelectItem value="all" class="dark:focus:bg-slate-800">Status: Semua</SelectItem>
            <SelectItem value="lunas" class="dark:focus:bg-slate-800">Lunas</SelectItem>
            <SelectItem value="belum_lunas" class="dark:focus:bg-slate-800">Belum Lunas</SelectItem>
            <SelectItem value="cicilan" class="dark:focus:bg-slate-800">Cicilan</SelectItem>
          </SelectContent>
        </Select>
        <Select>
          <SelectTrigger class="w-full sm:w-[150px] bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800 shadow-sm dark:text-slate-200">
            <SelectValue placeholder="Oktober 2023" />
          </SelectTrigger>
          <SelectContent class="dark:bg-slate-950 dark:border-slate-800">
            <SelectItem value="okt2023" class="dark:focus:bg-slate-800">Oktober 2023</SelectItem>
            <SelectItem value="sep2023" class="dark:focus:bg-slate-800">September 2023</SelectItem>
          </SelectContent>
        </Select>
        <Button variant="outline" size="icon" class="bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800 shadow-sm shrink-0 w-10 h-10 dark:hover:bg-slate-800">
          <Filter class="w-4 h-4 text-slate-500 dark:text-slate-400" />
        </Button>
      </div>
    </div>

    <!-- Main Table -->
    <Card class="shadow-sm border-slate-200/60 dark:border-slate-800 overflow-hidden dark:bg-slate-950">
      <CardHeader class="flex flex-row items-center justify-between py-4 px-6 bg-white dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800">
        <CardTitle class="text-base font-semibold dark:text-slate-50">Daftar Tagihan Utama</CardTitle>
        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Showing 1-10 of 1,250 students</span>
      </CardHeader>
      <div class="overflow-x-auto">
        <Table>
          <TableHeader class="bg-slate-50/50 dark:bg-slate-900/50">
            <TableRow class="hover:bg-transparent dark:border-slate-800">
              <TableHead class="w-[280px] text-xs font-semibold text-slate-500 dark:text-slate-400 h-10">NAMA SISWA</TableHead>
              <TableHead class="text-xs font-semibold text-slate-500 dark:text-slate-400 h-10">KELAS</TableHead>
              <TableHead class="text-xs font-semibold text-slate-500 dark:text-slate-400 h-10">JENIS TAGIHAN</TableHead>
              <TableHead class="text-xs font-semibold text-slate-500 dark:text-slate-400 h-10">JUMLAH</TableHead>
              <TableHead class="text-xs font-semibold text-slate-500 dark:text-slate-400 h-10">JATUH TEMPO</TableHead>
              <TableHead class="text-xs font-semibold text-slate-500 dark:text-slate-400 h-10">STATUS</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <!-- Row 1 -->
            <TableRow class="dark:border-slate-800 dark:hover:bg-slate-900/50">
              <TableCell class="py-3">
                <div class="flex items-center gap-3">
                  <Avatar class="h-9 w-9 rounded-md bg-slate-100/80 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700">
                    <AvatarFallback class="rounded-md text-[11px] font-bold text-slate-700 dark:text-slate-300">AS</AvatarFallback>
                  </Avatar>
                  <div>
                    <div class="font-medium text-sm text-slate-900 dark:text-slate-50">Ahmad Sauqi</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">NIS: 2203001</div>
                  </div>
                </div>
              </TableCell>
              <TableCell class="py-3 text-sm text-slate-600 dark:text-slate-300 font-medium">XII - MIPA 1</TableCell>
              <TableCell class="py-3"><Badge variant="gray" class="font-medium text-[11px] shadow-none">SPP Okt</Badge></TableCell>
              <TableCell class="py-3 text-sm font-semibold text-slate-900 dark:text-slate-50">Rp 750.000</TableCell>
              <TableCell class="py-3 text-sm text-slate-600 dark:text-slate-400">10 Okt 2023</TableCell>
              <TableCell class="py-3">
                <Badge variant="red" class="font-bold text-[10px] tracking-wider uppercase px-2 py-0.5 shadow-none">BELUM LUNAS</Badge>
              </TableCell>
            </TableRow>
            <!-- Row 2 -->
            <TableRow class="dark:border-slate-800 dark:hover:bg-slate-900/50">
              <TableCell class="py-3">
                <div class="flex items-center gap-3">
                  <Avatar class="h-9 w-9 rounded-md bg-slate-100/80 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700">
                    <AvatarFallback class="rounded-md text-[11px] font-bold text-slate-700 dark:text-slate-300">BR</AvatarFallback>
                  </Avatar>
                  <div>
                    <div class="font-medium text-sm text-slate-900 dark:text-slate-50">Bella Rosalina</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">NIS: 2203002</div>
                  </div>
                </div>
              </TableCell>
              <TableCell class="py-3 text-sm text-slate-600 dark:text-slate-300 font-medium">XII - MIPA 1</TableCell>
              <TableCell class="py-3"><Badge variant="gray" class="font-medium text-[11px] shadow-none">SPP Okt</Badge></TableCell>
              <TableCell class="py-3 text-sm font-semibold text-slate-900 dark:text-slate-50">Rp 750.000</TableCell>
              <TableCell class="py-3 text-sm text-slate-600 dark:text-slate-400">10 Okt 2023</TableCell>
              <TableCell class="py-3">
                <Badge variant="green" class="font-bold text-[10px] tracking-wider uppercase px-2 py-0.5 shadow-none">LUNAS</Badge>
              </TableCell>
            </TableRow>
            <!-- Row 3 -->
            <TableRow class="dark:border-slate-800 dark:hover:bg-slate-900/50">
              <TableCell class="py-3">
                <div class="flex items-center gap-3">
                  <Avatar class="h-9 w-9 rounded-md bg-slate-100/80 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700">
                    <AvatarFallback class="rounded-md text-[11px] font-bold text-slate-700 dark:text-slate-300">DW</AvatarFallback>
                  </Avatar>
                  <div>
                    <div class="font-medium text-sm text-slate-900 dark:text-slate-50">Dandi Wijaya</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">NIS: 2203005</div>
                  </div>
                </div>
              </TableCell>
              <TableCell class="py-3 text-sm text-slate-600 dark:text-slate-300 font-medium">XI - IPS 2</TableCell>
              <TableCell class="py-3"><Badge variant="gray" class="font-medium text-[11px] shadow-none">Kegiatan OSIS</Badge></TableCell>
              <TableCell class="py-3 text-sm font-semibold text-slate-900 dark:text-slate-50">Rp 250.000</TableCell>
              <TableCell class="py-3 text-sm text-slate-600 dark:text-slate-400">15 Okt 2023</TableCell>
              <TableCell class="py-3">
                <Badge variant="blue" class="font-bold text-[10px] tracking-wider uppercase px-2 py-0.5 shadow-none bg-blue-100/50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">CICILAN</Badge>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <div class="flex justify-center p-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-900/30">
        <div class="flex items-center gap-1.5">
          <Button variant="outline" size="sm" class="w-8 h-8 p-0 border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-600 cursor-not-allowed dark:bg-slate-900">
            <span class="text-xs font-medium">1</span>
          </Button>
          <Button variant="default" size="sm" class="w-8 h-8 p-0 bg-slate-900 dark:bg-slate-50 text-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-200 shadow-sm">
            <span class="text-xs font-medium">2</span>
          </Button>
          <Button variant="outline" size="sm" class="w-8 h-8 p-0 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 dark:bg-slate-950">
            <span class="text-xs font-medium">3</span>
          </Button>
          <span class="text-slate-400 dark:text-slate-500 px-1 text-xs">...</span>
          <Button variant="outline" size="sm" class="w-9 h-8 p-0 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 dark:bg-slate-950">
            <span class="text-xs font-medium">125</span>
          </Button>
        </div>
      </div>
    </Card>

    <!-- Bottom Section: Tabs & Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Tabs -->
      <div class="lg:col-span-2">
        <Tabs defaultValue="terbaru" class="w-full">
          <TabsList class="w-full justify-start border-b border-slate-200 dark:border-slate-800 rounded-none bg-transparent h-auto p-0 mb-5 gap-6">
            <TabsTrigger value="terbaru" class="rounded-none border-b-2 border-transparent data-[state=active]:border-slate-900 dark:data-[state=active]:border-slate-50 data-[state=active]:text-slate-900 dark:data-[state=active]:text-slate-50 data-[state=active]:bg-transparent px-1 py-2 text-[13px] font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
              Riwayat Pembayaran Terbaru
            </TabsTrigger>
            <TabsTrigger value="tertunda" class="rounded-none border-b-2 border-transparent data-[state=active]:border-slate-900 dark:data-[state=active]:border-slate-50 data-[state=active]:text-slate-900 dark:data-[state=active]:text-slate-50 data-[state=active]:bg-transparent px-1 py-2 text-[13px] font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
              Pembayaran Tertunda
            </TabsTrigger>
            <TabsTrigger value="dibatalkan" class="rounded-none border-b-2 border-transparent data-[state=active]:border-slate-900 dark:data-[state=active]:border-slate-50 data-[state=active]:text-slate-900 dark:data-[state=active]:text-slate-50 data-[state=active]:bg-transparent px-1 py-2 text-[13px] font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
              Dibatalkan
            </TabsTrigger>
          </TabsList>
          
          <TabsContent value="terbaru" class="space-y-3">
            <Card class="shadow-sm border-slate-200/60 dark:border-slate-800 p-0 dark:bg-slate-950">
              <div class="divide-y divide-slate-100 dark:divide-slate-800">
                <!-- Transaction 1 -->
                <div class="flex items-center justify-between p-4 hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors">
                  <div class="flex items-center gap-3 w-1/3">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100/50 dark:border-emerald-900/50 flex items-center justify-center shrink-0">
                      <CheckCircle2 class="w-5 h-5 text-emerald-500 dark:text-emerald-400" />
                    </div>
                    <div>
                      <div class="font-semibold text-sm text-slate-900 dark:text-slate-50">Fahmi Ramadhan</div>
                      <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">TRX-20231002-001</div>
                    </div>
                  </div>
                  <div class="hidden sm:block w-1/4">
                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">TANGGAL</div>
                    <div class="text-[13px] font-medium text-slate-700 dark:text-slate-300">02 Okt 2023, 08:45</div>
                  </div>
                  <div class="hidden md:block w-1/4">
                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">METODE</div>
                    <div class="text-[13px] font-medium text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                      <Landmark class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" /> Transfer BNI
                    </div>
                  </div>
                  <div class="text-right w-auto sm:w-1/6">
                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">JUMLAH</div>
                    <div class="text-sm font-bold text-slate-900 dark:text-slate-50">Rp 750.000</div>
                  </div>
                  <Button variant="ghost" size="sm" class="hidden sm:flex text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 gap-1.5 ml-4 h-8 px-2 dark:hover:bg-slate-800">
                    <Eye class="w-3.5 h-3.5" /> <span class="text-xs font-medium">Lihat Detail</span>
                  </Button>
                </div>
                
                <!-- Transaction 2 -->
                <div class="flex items-center justify-between p-4 hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors">
                  <div class="flex items-center gap-3 w-1/3">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100/50 dark:border-emerald-900/50 flex items-center justify-center shrink-0">
                      <CheckCircle2 class="w-5 h-5 text-emerald-500 dark:text-emerald-400" />
                    </div>
                    <div>
                      <div class="font-semibold text-sm text-slate-900 dark:text-slate-50">Gisella Putri</div>
                      <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">TRX-20231002-005</div>
                    </div>
                  </div>
                  <div class="hidden sm:block w-1/4">
                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">TANGGAL</div>
                    <div class="text-[13px] font-medium text-slate-700 dark:text-slate-300">02 Okt 2023, 10:12</div>
                  </div>
                  <div class="hidden md:block w-1/4">
                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">METODE</div>
                    <div class="text-[13px] font-medium text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                      <Banknote class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" /> Tunai / Cash
                    </div>
                  </div>
                  <div class="text-right w-auto sm:w-1/6">
                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">JUMLAH</div>
                    <div class="text-sm font-bold text-slate-900 dark:text-slate-50">Rp 1.500.000</div>
                  </div>
                  <Button variant="ghost" size="sm" class="hidden sm:flex text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 gap-1.5 ml-4 h-8 px-2 dark:hover:bg-slate-800">
                    <Eye class="w-3.5 h-3.5" /> <span class="text-xs font-medium">Lihat Detail</span>
                  </Button>
                </div>
              </div>
            </Card>
            
            <div class="flex justify-center mt-4">
              <Button variant="ghost" class="text-[13px] font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 h-9">
                Lihat Semua Riwayat Transaksi
              </Button>
            </div>
          </TabsContent>
          <TabsContent value="tertunda">
            <Card class="shadow-sm border-slate-200/60 dark:border-slate-800 dark:bg-slate-950 p-12 flex flex-col items-center justify-center text-center">
              <div class="w-12 h-12 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center mb-3">
                <AlertCircle class="w-6 h-6 text-slate-300 dark:text-slate-600" />
              </div>
              <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Tidak ada transaksi tertunda.</p>
              <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Pembayaran yang masih dalam proses akan muncul di sini.</p>
            </Card>
          </TabsContent>
          <TabsContent value="dibatalkan">
             <Card class="shadow-sm border-slate-200/60 dark:border-slate-800 dark:bg-slate-950 p-12 flex flex-col items-center justify-center text-center">
              <div class="w-12 h-12 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center mb-3">
                <AlertCircle class="w-6 h-6 text-slate-300 dark:text-slate-600" />
              </div>
              <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Tidak ada transaksi yang dibatalkan.</p>
            </Card>
          </TabsContent>
        </Tabs>
      </div>

      <!-- Activity Log -->
      <div>
        <h3 class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-5">LOG AKTIVITAS TERBARU</h3>
        <div class="space-y-5">
          <div class="flex gap-4 group">
            <div class="text-[11px] font-medium text-slate-400 dark:text-slate-500 w-16 shrink-0 mt-0.5 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors">10:45 AM</div>
            <div class="text-[13px] leading-relaxed">
              <span class="font-semibold text-slate-900 dark:text-slate-50">Admin-Siti</span> <span class="text-slate-600 dark:text-slate-400">Memvalidasi pembayaran SPP Dandi Wijaya (Tunai)</span>
            </div>
          </div>
          <div class="flex gap-4 group">
            <div class="text-[11px] font-medium text-slate-400 dark:text-slate-500 w-16 shrink-0 mt-0.5 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors">09:30 AM</div>
            <div class="text-[13px] leading-relaxed">
              <span class="font-semibold text-slate-900 dark:text-slate-50">System</span> <span class="text-slate-600 dark:text-slate-400">Generate tagihan otomatis periode Oktober untuk 1,250 siswa</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
