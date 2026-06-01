<script setup>
import {
  Wallet,
  Banknote,
  PiggyBank,
  PlusCircle,
  ChevronRight,
  CheckCircle2,
  Download,
  MoreHorizontal,
  Pencil
} from 'lucide-vue-next'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
</script>

<template>
  <div class="space-y-6 w-full mx-auto px-0">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold tracking-tight text-foreground">Manajemen Dana Yayasan</h1>
        <p class="text-sm text-muted-foreground mt-1">Unit Sekolah: SMA Unggul Bangsa</p>
      </div>
      <Button class="bg-foreground text-background hover:bg-foreground/90 font-semibold px-4">
        <PlusCircle class="w-4 h-4 mr-2" /> Input Pengeluaran Baru
      </Button>
    </div>

    <!-- Top Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Card 1 -->
      <Card class="shadow-sm border-border p-5 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-lg bg-secondary flex items-center justify-center">
            <Wallet class="w-5 h-5 text-foreground" />
          </div>
          <span class="text-xs font-semibold text-muted-foreground mt-1">Tahun Ajaran 2024/2025</span>
        </div>
        <div>
          <p class="text-[11px] font-bold text-muted-foreground uppercase tracking-widest mb-1">Total Alokasi Dana</p>
          <p class="text-2xl lg:text-3xl font-extrabold tracking-tight text-foreground">Rp 2.450.000.000</p>
        </div>
      </Card>

      <!-- Card 2 -->
      <Card class="shadow-sm border-border p-5 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-lg bg-secondary flex items-center justify-center">
            <Banknote class="w-5 h-5 text-foreground" />
          </div>
          <Badge class="bg-destructive/15 text-destructive hover:bg-destructive/25 border-none font-bold shadow-none px-2.5 py-0.5 rounded-[4px]">- 42%</Badge>
        </div>
        <div>
          <p class="text-[11px] font-bold text-muted-foreground uppercase tracking-widest mb-1">Dana Terpakai</p>
          <p class="text-2xl lg:text-3xl font-extrabold tracking-tight text-foreground">Rp 1.029.000.000</p>
          <div class="h-[3px] w-full bg-secondary mt-3 rounded-full overflow-hidden">
             <div class="h-full bg-foreground" style="width: 42%;"></div>
          </div>
        </div>
      </Card>

      <!-- Card 3 -->
      <Card class="shadow-sm border-border p-5 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
            <PiggyBank class="w-5 h-5 text-primary" />
          </div>
          <Badge class="bg-primary/20 text-primary hover:bg-primary/30 border-none font-bold shadow-none px-2.5 py-0.5 rounded-[4px]">Tersedia</Badge>
        </div>
        <div>
          <p class="text-[11px] font-bold text-muted-foreground uppercase tracking-widest mb-1">Sisa Anggaran</p>
          <p class="text-2xl lg:text-3xl font-extrabold tracking-tight text-primary">Rp 1.421.000.000</p>
        </div>
      </Card>
    </div>

    <!-- Middle Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Monitoring Pengeluaran (Left, span 2) -->
      <Card class="lg:col-span-2 shadow-sm border-border">
        <div class="p-5 flex justify-between items-center border-b border-border">
          <h3 class="font-bold text-lg text-foreground">Monitoring Pengeluaran Terbaru</h3>
          <Button variant="ghost" class="text-sm font-semibold p-0 h-auto hover:bg-transparent hover:text-primary">
            Lihat Semua <ChevronRight class="w-4 h-4 ml-1" />
          </Button>
        </div>
        <Table>
          <TableHeader class="bg-muted/30">
            <TableRow class="hover:bg-transparent">
              <TableHead class="font-semibold text-muted-foreground py-4 w-[100px]">Tanggal</TableHead>
              <TableHead class="font-semibold text-muted-foreground py-4">Deskripsi</TableHead>
              <TableHead class="font-semibold text-muted-foreground py-4">Kategori</TableHead>
              <TableHead class="font-semibold text-muted-foreground py-4">Jumlah</TableHead>
              <TableHead class="font-semibold text-muted-foreground text-right py-4">Status</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <!-- Row 1 -->
            <TableRow>
              <TableCell class="py-4 text-sm text-foreground">
                14 Mar<br/>2024
              </TableCell>
              <TableCell class="py-4">
                <p class="font-bold text-foreground text-sm">Renovasi Lab<br/>Komputer</p>
              </TableCell>
              <TableCell class="py-4">
                <Badge variant="secondary" class="font-medium rounded text-[10px] bg-secondary text-secondary-foreground shadow-none">Infrastruktur</Badge>
              </TableCell>
              <TableCell class="py-4">
                <p class="text-[10px] text-muted-foreground font-semibold">Rp</p>
                <p class="font-bold text-foreground">45.000.000</p>
              </TableCell>
              <TableCell class="text-right py-4">
                <Badge class="bg-primary/15 hover:bg-primary/15 text-primary border-none px-2 rounded-[4px] font-bold text-[9px] tracking-widest uppercase">APPROVED</Badge>
              </TableCell>
            </TableRow>
            
            <!-- Row 2 -->
            <TableRow>
              <TableCell class="py-4 text-sm text-foreground">
                12 Mar<br/>2024
              </TableCell>
              <TableCell class="py-4">
                <p class="font-bold text-foreground text-sm">Gaji Guru<br/>Honorer Mar</p>
              </TableCell>
              <TableCell class="py-4">
                <Badge variant="secondary" class="font-medium rounded text-[10px] bg-secondary text-secondary-foreground shadow-none">Salaries</Badge>
              </TableCell>
              <TableCell class="py-4">
                <p class="text-[10px] text-muted-foreground font-semibold">Rp</p>
                <p class="font-bold text-foreground">128.500.000</p>
              </TableCell>
              <TableCell class="text-right py-4">
                <Badge class="bg-primary/15 hover:bg-primary/15 text-primary border-none px-2 rounded-[4px] font-bold text-[9px] tracking-widest uppercase">APPROVED</Badge>
              </TableCell>
            </TableRow>

            <!-- Row 3 -->
            <TableRow>
              <TableCell class="py-4 text-sm text-foreground">
                10 Mar<br/>2024
              </TableCell>
              <TableCell class="py-4">
                <p class="font-bold text-foreground text-sm">Seminar<br/>Pendidikan<br/>Karakter</p>
              </TableCell>
              <TableCell class="py-4">
                <Badge variant="secondary" class="font-medium rounded text-[10px] bg-secondary text-secondary-foreground shadow-none">Events</Badge>
              </TableCell>
              <TableCell class="py-4">
                <p class="text-[10px] text-muted-foreground font-semibold">Rp</p>
                <p class="font-bold text-foreground">12.000.000</p>
              </TableCell>
              <TableCell class="text-right py-4">
                <Badge class="bg-blue-500/15 hover:bg-blue-500/15 text-blue-600 border-none px-2.5 py-0.5 rounded-[4px] font-bold text-[9px] tracking-widest uppercase">PENDING</Badge>
              </TableCell>
            </TableRow>

            <!-- Row 4 -->
            <TableRow>
              <TableCell class="py-4 text-sm text-foreground">
                08 Mar<br/>2024
              </TableCell>
              <TableCell class="py-4">
                <p class="font-bold text-foreground text-sm">Pengadaan<br/>Buku<br/>Perpustakaan</p>
              </TableCell>
              <TableCell class="py-4">
                <Badge variant="secondary" class="font-medium rounded text-[10px] bg-secondary text-secondary-foreground shadow-none">Infrastruktur</Badge>
              </TableCell>
              <TableCell class="py-4">
                <p class="text-[10px] text-muted-foreground font-semibold">Rp</p>
                <p class="font-bold text-foreground">35.200.000</p>
              </TableCell>
              <TableCell class="text-right py-4">
                <Badge class="bg-primary/15 hover:bg-primary/15 text-primary border-none px-2 rounded-[4px] font-bold text-[9px] tracking-widest uppercase">APPROVED</Badge>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </Card>

      <!-- Right Column (Alokasi & Status LPJ) -->
      <div class="flex flex-col gap-6">
        <!-- Alokasi Anggaran -->
        <Card class="shadow-sm border-border">
          <CardHeader class="pb-4">
            <CardTitle class="text-base font-bold">Alokasi Anggaran</CardTitle>
          </CardHeader>
          <CardContent class="space-y-6">
            <div>
              <div class="flex justify-between items-end mb-2">
                <span class="text-sm font-bold text-foreground">Gaji & Tunjangan</span>
                <span class="text-sm font-bold text-foreground">55%</span>
              </div>
              <div class="h-2.5 w-full bg-secondary rounded-full overflow-hidden">
                <div class="h-full bg-foreground" style="width: 55%"></div>
              </div>
            </div>
            
            <div>
              <div class="flex justify-between items-end mb-2">
                <span class="text-sm font-bold text-foreground">Infrastruktur</span>
                <span class="text-sm font-bold text-foreground">30%</span>
              </div>
              <div class="h-2.5 w-full bg-secondary rounded-full overflow-hidden">
                <div class="h-full bg-muted-foreground" style="width: 30%"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between items-end mb-2">
                <span class="text-sm font-bold text-foreground">Kegiatan Siswa</span>
                <span class="text-sm font-bold text-foreground">15%</span>
              </div>
              <div class="h-2.5 w-full bg-secondary rounded-full overflow-hidden">
                <div class="h-full bg-muted-foreground" style="width: 15%"></div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Status Laporan LPJ -->
        <Card class="shadow-sm border-border flex-1 flex flex-col">
          <CardHeader class="pb-4">
            <CardTitle class="text-base font-bold">Status Laporan (LPJ)</CardTitle>
          </CardHeader>
          <CardContent class="flex-1 flex flex-col space-y-4">
            <!-- Selesai -->
            <div class="bg-secondary/60 rounded-lg p-4 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center shrink-0">
                  <CheckCircle2 class="w-4 h-4 text-primary-foreground" />
                </div>
                <div>
                  <p class="text-sm font-bold text-foreground">LPJ Februari 2024</p>
                  <p class="text-[9px] uppercase font-bold text-muted-foreground tracking-widest mt-0.5">Selesai & Diverifikasi</p>
                </div>
              </div>
              <Button variant="ghost" size="icon" class="h-8 w-8 text-foreground hover:bg-muted">
                <Download class="w-4 h-4" />
              </Button>
            </div>

            <!-- Dalam Penyusunan -->
            <div class="bg-background border border-dashed border-border rounded-lg p-4 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-full border border-muted-foreground flex items-center justify-center shrink-0 text-muted-foreground">
                  <MoreHorizontal class="w-4 h-4" />
                </div>
                <div>
                  <p class="text-sm font-bold text-foreground">LPJ Maret 2024</p>
                  <p class="text-[9px] uppercase font-bold text-muted-foreground tracking-widest mt-0.5">Dalam Penyusunan (75%)</p>
                </div>
              </div>
              <Button size="icon" class="h-8 w-8 bg-foreground text-background hover:bg-foreground/90 rounded-md">
                <Pencil class="w-3.5 h-3.5" />
              </Button>
            </div>

            <!-- Spacer -->
            <div class="flex-1"></div>
            
            <div class="w-full h-px bg-border/60 my-4"></div>

            <!-- Deadline -->
            <div>
              <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-muted-foreground">Deadline LPJ Triwulan I</span>
                <span class="text-xs font-bold text-destructive">10 Hari Lagi</span>
              </div>
              <div class="h-1 w-full bg-secondary overflow-hidden">
                <div class="h-full bg-destructive" style="width: 75%"></div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- Audit Trail -->
    <Card class="shadow-sm border-border p-6">
      <h3 class="text-xs font-bold text-muted-foreground tracking-widest uppercase mb-6">Audit Trail Aktivitas Dana</h3>
      <div class="space-y-6">
        <!-- Item 1 -->
        <div class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-8">
          <div class="w-24 shrink-0 text-xs text-muted-foreground font-medium">09:15 AM</div>
          <div>
            <p class="text-sm text-foreground">
              <span class="font-bold">Admin Utama</span> mengunggah kuitansi renovasi lab komputer.
            </p>
            <p class="text-xs text-muted-foreground mt-0.5">ID Transaksi: #TRX-992120</p>
          </div>
        </div>
        
        <div class="w-full h-px bg-border/50"></div>
        
        <!-- Item 2 -->
        <div class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-8">
          <div class="w-24 shrink-0 text-xs text-muted-foreground font-medium">Kemarin</div>
          <div>
            <p class="text-sm text-foreground">
              <span class="font-bold">Yayasan Pusat</span> menyetujui alokasi dana darurat perbaikan atap.
            </p>
            <p class="text-xs text-muted-foreground mt-0.5">Otorisasi: Dr. H. Sulaiman</p>
          </div>
        </div>

        <div class="w-full h-px bg-border/50"></div>

        <!-- Item 3 -->
        <div class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-8">
          <div class="w-24 shrink-0 text-xs text-muted-foreground font-medium">12 Mar</div>
          <div>
            <p class="text-sm text-foreground">
              <span class="font-bold">Sistem</span> melakukan pembayaran otomatis gaji guru honorer.
            </p>
            <p class="text-xs text-muted-foreground mt-0.5">Batch: Payroll-HNR-03-24</p>
          </div>
        </div>
      </div>
    </Card>
  </div>
</template>
