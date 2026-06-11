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
import StatCard from '@/components/stat-card/StatCard.vue'
</script>

<template>
  <div class="space-y-8 pb-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Riwayat Pembayaran Siswa</h1>
        <p class="text-sm text-muted-foreground mt-1">Kelola data tagihan pendidikan dan pantau realisasi pembayaran institusi Anda.</p>
      </div>
      <Button variant="outline" class="flex items-center gap-2">
        <Download class="w-4 h-4" />
        Export Rekapitulasi
      </Button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard
        label="Total Tagihan Bulan Ini"
        value="Rp 425.800.000"
        variant="blue"
        :icon="Landmark"
        trend="+4.2%"
        trendDirection="up"
        :delay="0"
      />
      <StatCard
        label="Total Terbayar"
        value="Rp 332.124.000"
        variant="emerald"
        :icon="Banknote"
        :progress="78"
        sub="Realisasi pembayaran"
        :delay="100"
      />
      <StatCard
        label="Sisa Tunggakan"
        value="Rp 93.676.000"
        variant="amber"
        :icon="AlertCircle"
        sub="Tunggakan berjalan"
        :delay="200"
      />
      <StatCard
        label="Siswa Belum Bayar"
        value="142 Siswa"
        variant="primary"
        :icon="Users"
        sub="Bulan berjalan"
        :delay="300"
      />
    </div>

    <!-- Filters -->
    <div class="flex flex-col lg:flex-row gap-3 bg-muted/30 p-2 rounded-xl border border-border">
      <div class="relative flex-1">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
        <Input placeholder="Cari berdasarkan Nama Siswa atau Kelas..." class="pl-9" />
      </div>
      <div class="flex flex-wrap sm:flex-nowrap gap-3">
        <Select>
          <SelectTrigger class="w-full sm:w-[150px]">
            <SelectValue placeholder="Semua Kelas" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Semua Kelas</SelectItem>
            <SelectItem value="10">Kelas 10</SelectItem>
            <SelectItem value="11">Kelas 11</SelectItem>
            <SelectItem value="12">Kelas 12</SelectItem>
          </SelectContent>
        </Select>
        <Select>
          <SelectTrigger class="w-full sm:w-[150px]">
            <SelectValue placeholder="Status: Semua" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Status: Semua</SelectItem>
            <SelectItem value="lunas">Lunas</SelectItem>
            <SelectItem value="belum_lunas">Belum Lunas</SelectItem>
            <SelectItem value="cicilan">Cicilan</SelectItem>
          </SelectContent>
        </Select>
        <Select>
          <SelectTrigger class="w-full sm:w-[150px]">
            <SelectValue placeholder="Oktober 2023" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="okt2023">Oktober 2023</SelectItem>
            <SelectItem value="sep2023">September 2023</SelectItem>
          </SelectContent>
        </Select>
        <Button variant="outline" size="icon" class="shrink-0 size-10">
          <Filter class="w-4 h-4 text-muted-foreground" />
        </Button>
      </div>
    </div>

    <!-- Main Table -->
    <Card class="shadow-sm border-border overflow-hidden">
      <CardHeader class="flex flex-row items-center justify-between py-4 px-6 bg-card border-b border-border">
        <CardTitle class="text-base font-semibold">Daftar Tagihan Utama</CardTitle>
        <span class="text-xs text-muted-foreground font-medium">Showing 1-3 of 1,250 students</span>
      </CardHeader>
      <div class="overflow-x-auto">
        <Table>
          <TableHeader class="bg-muted/50">
            <TableRow class="hover:bg-transparent border-border">
              <TableHead class="w-[280px] text-xs font-semibold text-muted-foreground h-10">NAMA SISWA</TableHead>
              <TableHead class="text-xs font-semibold text-muted-foreground h-10">KELAS</TableHead>
              <TableHead class="text-xs font-semibold text-muted-foreground h-10">JENIS TAGIHAN</TableHead>
              <TableHead class="text-xs font-semibold text-muted-foreground h-10">JUMLAH</TableHead>
              <TableHead class="text-xs font-semibold text-muted-foreground h-10">JATUH TEMPO</TableHead>
              <TableHead class="text-xs font-semibold text-muted-foreground h-10">STATUS</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <!-- Row 1 -->
            <TableRow class="border-border hover:bg-muted/30">
              <TableCell class="py-3">
                <div class="flex items-center gap-3">
                  <Avatar class="h-9 w-9 rounded-md bg-secondary border border-border">
                    <AvatarFallback class="rounded-md text-[11px] font-bold text-secondary-foreground">AS</AvatarFallback>
                  </Avatar>
                  <div>
                    <div class="font-medium text-sm text-foreground">Ahmad Sauqi</div>
                    <div class="text-[11px] text-muted-foreground mt-0.5">NIS: 2203001</div>
                  </div>
                </div>
              </TableCell>
              <TableCell class="py-3 text-sm text-foreground font-medium">XII - MIPA 1</TableCell>
              <TableCell class="py-3">
                <Badge variant="secondary" class="font-medium text-[11px] shadow-none bg-muted text-muted-foreground">SPP Okt</Badge>
              </TableCell>
              <TableCell class="py-3 text-sm font-semibold text-foreground">Rp 750.000</TableCell>
              <TableCell class="py-3 text-sm text-muted-foreground">10 Okt 2023</TableCell>
              <TableCell class="py-3">
                <Badge variant="destructive" class="font-bold text-[10px] tracking-wider uppercase px-2 py-0.5 shadow-none bg-destructive/10 text-destructive border-destructive/20 hover:bg-destructive/10">BELUM LUNAS</Badge>
              </TableCell>
            </TableRow>
            <!-- Row 2 -->
            <TableRow class="border-border hover:bg-muted/30">
              <TableCell class="py-3">
                <div class="flex items-center gap-3">
                  <Avatar class="h-9 w-9 rounded-md bg-secondary border border-border">
                    <AvatarFallback class="rounded-md text-[11px] font-bold text-secondary-foreground">BR</AvatarFallback>
                  </Avatar>
                  <div>
                    <div class="font-medium text-sm text-foreground">Bella Rosalina</div>
                    <div class="text-[11px] text-muted-foreground mt-0.5">NIS: 2203002</div>
                  </div>
                </div>
              </TableCell>
              <TableCell class="py-3 text-sm text-foreground font-medium">XII - MIPA 1</TableCell>
              <TableCell class="py-3">
                <Badge variant="secondary" class="font-medium text-[11px] shadow-none bg-muted text-muted-foreground">SPP Okt</Badge>
              </TableCell>
              <TableCell class="py-3 text-sm font-semibold text-foreground">Rp 750.000</TableCell>
              <TableCell class="py-3 text-sm text-muted-foreground">10 Okt 2023</TableCell>
              <TableCell class="py-3">
                <Badge variant="outline" class="font-bold text-[10px] tracking-wider uppercase px-2 py-0.5 shadow-none bg-primary/10 text-primary border-primary/20 hover:bg-primary/10">LUNAS</Badge>
              </TableCell>
            </TableRow>
            <!-- Row 3 -->
            <TableRow class="border-border hover:bg-muted/30">
              <TableCell class="py-3">
                <div class="flex items-center gap-3">
                  <Avatar class="h-9 w-9 rounded-md bg-secondary border border-border">
                    <AvatarFallback class="rounded-md text-[11px] font-bold text-secondary-foreground">DW</AvatarFallback>
                  </Avatar>
                  <div>
                    <div class="font-medium text-sm text-foreground">Dandi Wijaya</div>
                    <div class="text-[11px] text-muted-foreground mt-0.5">NIS: 2203005</div>
                  </div>
                </div>
              </TableCell>
              <TableCell class="py-3 text-sm text-foreground font-medium">XI - IPS 2</TableCell>
              <TableCell class="py-3">
                <Badge variant="secondary" class="font-medium text-[11px] shadow-none bg-muted text-muted-foreground">Kegiatan OSIS</Badge>
              </TableCell>
              <TableCell class="py-3 text-sm font-semibold text-foreground">Rp 250.000</TableCell>
              <TableCell class="py-3 text-sm text-muted-foreground">15 Okt 2023</TableCell>
              <TableCell class="py-3">
                <Badge variant="outline" class="font-bold text-[10px] tracking-wider uppercase px-2 py-0.5 shadow-none bg-amber-500/10 text-amber-500 border-amber-200 hover:bg-amber-500/10">CICILAN</Badge>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <div class="flex justify-center p-3 border-t border-border bg-muted/10">
        <div class="flex items-center gap-1.5">
          <Button variant="outline" size="sm" class="w-8 h-8 p-0 border-border text-muted-foreground/50 cursor-not-allowed bg-muted/10">
            <span class="text-xs font-medium">1</span>
          </Button>
          <Button variant="default" size="sm" class="w-8 h-8 p-0 bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm">
            <span class="text-xs font-medium">2</span>
          </Button>
          <Button variant="outline" size="sm" class="w-8 h-8 p-0 border-border text-foreground hover:bg-muted">
            <span class="text-xs font-medium">3</span>
          </Button>
          <span class="text-muted-foreground px-1 text-xs">...</span>
          <Button variant="outline" size="sm" class="w-9 h-8 p-0 border-border text-foreground hover:bg-muted">
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
          <TabsList variant="line" class="w-full justify-start border-b border-border rounded-none bg-transparent h-auto p-0 mb-5 gap-6">
            <TabsTrigger value="terbaru" class="rounded-none bg-transparent data-[state=active]:text-foreground data-[state=active]:bg-transparent px-1 py-2 text-[13px] font-semibold text-muted-foreground hover:text-foreground">
              Riwayat Pembayaran Terbaru
            </TabsTrigger>
            <TabsTrigger value="tertunda" class="rounded-none bg-transparent data-[state=active]:text-foreground data-[state=active]:bg-transparent px-1 py-2 text-[13px] font-semibold text-muted-foreground hover:text-foreground">
              Pembayaran Tertunda
            </TabsTrigger>
            <TabsTrigger value="dibatalkan" class="rounded-none bg-transparent data-[state=active]:text-foreground data-[state=active]:bg-transparent px-1 py-2 text-[13px] font-semibold text-muted-foreground hover:text-foreground">
              Dibatalkan
            </TabsTrigger>
          </TabsList>
          
          <TabsContent value="terbaru" class="space-y-3 animate-in fade-in-50 slide-in-from-bottom-2 duration-300">
            <Card class="shadow-sm border-border p-0 bg-card text-card-foreground">
              <div class="divide-y divide-border">
                <!-- Transaction 1 -->
                <div class="flex items-center justify-between p-4 hover:bg-muted/30 transition-colors">
                  <div class="flex items-center gap-3 w-1/3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0">
                      <CheckCircle2 class="w-5 h-5 text-emerald-500" />
                    </div>
                    <div>
                      <div class="font-semibold text-sm text-foreground">Fahmi Ramadhan</div>
                      <div class="text-[11px] text-muted-foreground mt-0.5">TRX-20231002-001</div>
                    </div>
                  </div>
                  <div class="hidden sm:block w-1/4">
                    <div class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider mb-1">TANGGAL</div>
                    <div class="text-[13px] font-medium text-foreground">02 Okt 2023, 08:45</div>
                  </div>
                  <div class="hidden md:block w-1/4">
                    <div class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider mb-1">METODE</div>
                    <div class="text-[13px] font-medium text-foreground flex items-center gap-1.5">
                      <Landmark class="w-3.5 h-3.5 text-muted-foreground" /> Transfer BNI
                    </div>
                  </div>
                  <div class="text-right w-auto sm:w-1/6">
                    <div class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider mb-1">JUMLAH</div>
                    <div class="text-sm font-bold text-foreground">Rp 750.000</div>
                  </div>
                  <Button variant="ghost" size="sm" class="hidden sm:flex text-muted-foreground hover:text-foreground gap-1.5 ml-4 h-8 px-2">
                    <Eye class="w-3.5 h-3.5" /> <span class="text-xs font-medium">Lihat Detail</span>
                  </Button>
                </div>
                
                <!-- Transaction 2 -->
                <div class="flex items-center justify-between p-4 hover:bg-muted/30 transition-colors">
                  <div class="flex items-center gap-3 w-1/3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0">
                      <CheckCircle2 class="w-5 h-5 text-emerald-500" />
                    </div>
                    <div>
                      <div class="font-semibold text-sm text-foreground">Gisella Putri</div>
                      <div class="text-[11px] text-muted-foreground mt-0.5">TRX-20231002-005</div>
                    </div>
                  </div>
                  <div class="hidden sm:block w-1/4">
                    <div class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider mb-1">TANGGAL</div>
                    <div class="text-[13px] font-medium text-foreground">02 Okt 2023, 10:12</div>
                  </div>
                  <div class="hidden md:block w-1/4">
                    <div class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider mb-1">METODE</div>
                    <div class="text-[13px] font-medium text-foreground flex items-center gap-1.5">
                      <Banknote class="w-3.5 h-3.5 text-muted-foreground" /> Tunai / Cash
                    </div>
                  </div>
                  <div class="text-right w-auto sm:w-1/6">
                    <div class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider mb-1">JUMLAH</div>
                    <div class="text-sm font-bold text-foreground">Rp 1.500.000</div>
                  </div>
                  <Button variant="ghost" size="sm" class="hidden sm:flex text-muted-foreground hover:text-foreground gap-1.5 ml-4 h-8 px-2">
                    <Eye class="w-3.5 h-3.5" /> <span class="text-xs font-medium">Lihat Detail</span>
                  </Button>
                </div>
              </div>
            </Card>
            
            <div class="flex justify-center mt-4">
              <Button variant="ghost" class="text-[13px] font-medium text-muted-foreground hover:text-foreground h-9">
                Lihat Semua Riwayat Transaksi
              </Button>
            </div>
          </TabsContent>
          <TabsContent value="tertunda" class="animate-in fade-in-50 slide-in-from-bottom-2 duration-300">
            <Card class="shadow-sm border-border p-12 flex flex-col items-center justify-center text-center">
              <div class="w-12 h-12 rounded-full bg-muted flex items-center justify-center mb-3">
                <AlertCircle class="w-6 h-6 text-muted-foreground" />
              </div>
              <p class="text-sm font-medium text-muted-foreground">Tidak ada transaksi tertunda.</p>
              <p class="text-xs text-muted-foreground/60 mt-1">Pembayaran yang masih dalam proses akan muncul di sini.</p>
            </Card>
          </TabsContent>
          <TabsContent value="dibatalkan" class="animate-in fade-in-50 slide-in-from-bottom-2 duration-300">
             <Card class="shadow-sm border-border p-12 flex flex-col items-center justify-center text-center">
              <div class="w-12 h-12 rounded-full bg-muted flex items-center justify-center mb-3">
                <AlertCircle class="w-6 h-6 text-muted-foreground" />
              </div>
              <p class="text-sm font-medium text-muted-foreground">Tidak ada transaksi yang dibatalkan.</p>
            </Card>
          </TabsContent>
        </Tabs>
      </div>

      <!-- Activity Log -->
      <Card class="p-6 h-fit border-border bg-card text-card-foreground">
        <h3 class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider mb-5">LOG AKTIVITAS TERBARU</h3>
        <div class="space-y-5">
          <div class="flex gap-4 group">
            <div class="text-[11px] font-medium text-muted-foreground w-16 shrink-0 mt-0.5 group-hover:text-foreground transition-colors">10:45 AM</div>
            <div class="text-[13px] leading-relaxed">
              <span class="font-semibold text-foreground">Admin-Siti</span> <span class="text-muted-foreground">Memvalidasi pembayaran SPP Dandi Wijaya (Tunai)</span>
            </div>
          </div>
          <div class="flex gap-4 group">
            <div class="text-[11px] font-medium text-muted-foreground w-16 shrink-0 mt-0.5 group-hover:text-foreground transition-colors">09:30 AM</div>
            <div class="text-[13px] leading-relaxed">
              <span class="font-semibold text-foreground">System</span> <span class="text-muted-foreground">Generate tagihan otomatis periode Oktober untuk 1,250 siswa</span>
            </div>
          </div>
        </div>
      </Card>
    </div>
  </div>
</template>
