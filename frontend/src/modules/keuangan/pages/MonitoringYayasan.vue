<script setup>
import {
  Calendar,
  ChevronDown,
  Download,
  Filter,
  TrendingUp,
  FileText,
  CheckCircle2,
  ExternalLink,
  History,
  ReceiptText,
  CheckSquare,
  AlertTriangle,
  ShieldCheck
} from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'

const kpiData = [
  {
    label: 'Total Pendapatan Konsolidasi',
    value: 'Rp 4.285 Jt',
    trend: '12.5% dari tahun lalu',
    trendDirection: 'up',
    icon: TrendingUp,
    variant: 'blue'
  },
  {
    label: 'Total Beban Operasional',
    value: 'Rp 2.140 Jt',
    trend: '50% Rasio Pengeluaran',
    trendDirection: 'up',
    icon: FileText,
    variant: 'amber'
  },
  {
    label: 'Posisi Kas Bersih',
    value: 'Rp 2.145 Jt',
    trend: 'Likuiditas Sehat',
    trendDirection: 'up',
    icon: CheckCircle2,
    variant: 'emerald'
  },
  {
    label: 'Rerata Penagihan SPP',
    value: '92.4%',
    progress: 92.4,
    variant: 'primary'
  }
]
</script>

<template>
  <div class="space-y-6 w-full mx-auto px-0">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex items-center gap-3 bg-muted/50 border border-border rounded-lg px-4 py-2 cursor-pointer hover:bg-muted/80 transition">
        <Calendar class="w-4 h-4 text-muted-foreground" />
        <div class="flex flex-col">
          <span class="text-[10px] font-semibold text-muted-foreground uppercase leading-tight tracking-wider">Rentang Waktu</span>
          <span class="text-sm font-medium leading-tight text-foreground">Januari 2024 - Juni 2024</span>
        </div>
        <ChevronDown class="w-4 h-4 text-muted-foreground ml-2" />
      </div>
      
      <div class="flex items-center gap-2">
        <Button class="bg-foreground text-background hover:bg-foreground/90">
          <Download class="w-4 h-4 mr-2" />
          Export Laporan
        </Button>
        <Button variant="outline" class="px-3 bg-muted/50 border-border">
          <Filter class="w-4 h-4 text-foreground" />
        </Button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <StatCard
        v-for="(stat, index) in kpiData"
        :key="index"
        :label="stat.label"
        :value="stat.value"
        :trend="stat.trend"
        :trendDirection="stat.trendDirection"
        :icon="stat.icon"
        :progress="stat.progress"
        :variant="stat.variant"
      />
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Bar Chart -->
      <Card class="lg:col-span-2 shadow-sm border-border">
        <CardHeader class="flex flex-row items-center justify-between pb-2">
          <CardTitle class="text-base font-bold">Pendapatan vs Beban Bulanan</CardTitle>
          <div class="flex items-center gap-4 text-xs font-medium text-muted-foreground">
            <div class="flex items-center gap-2">
              <div class="w-2.5 h-2.5 bg-foreground rounded-sm"></div> Pendapatan
            </div>
            <div class="flex items-center gap-2">
              <div class="w-2.5 h-2.5 bg-secondary rounded-sm"></div> Beban
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <!-- Custom pseudo bar chart -->
          <div class="h-[220px] mt-6 flex items-end justify-between px-2 sm:px-8 border-b border-border pb-2">
            <!-- Jan -->
            <div class="flex flex-col items-center gap-3 flex-1">
              <div class="flex items-end gap-1.5 w-full justify-center h-[180px]">
                <div class="w-3.5 sm:w-6 bg-foreground rounded-t-[2px]" style="height: 45%;"></div>
                <div class="w-3.5 sm:w-6 bg-secondary rounded-t-[2px]" style="height: 25%;"></div>
              </div>
              <span class="text-[11px] font-medium text-foreground">Jan</span>
            </div>
            <!-- Feb -->
            <div class="flex flex-col items-center gap-3 flex-1">
              <div class="flex items-end gap-1.5 w-full justify-center h-[180px]">
                <div class="w-3.5 sm:w-6 bg-foreground rounded-t-[2px]" style="height: 55%;"></div>
                <div class="w-3.5 sm:w-6 bg-secondary rounded-t-[2px]" style="height: 30%;"></div>
              </div>
              <span class="text-[11px] font-medium text-foreground">Feb</span>
            </div>
            <!-- Mar -->
            <div class="flex flex-col items-center gap-3 flex-1">
              <div class="flex items-end gap-1.5 w-full justify-center h-[180px]">
                <div class="w-3.5 sm:w-6 bg-foreground rounded-t-[2px]" style="height: 65%;"></div>
                <div class="w-3.5 sm:w-6 bg-secondary rounded-t-[2px]" style="height: 35%;"></div>
              </div>
              <span class="text-[11px] font-medium text-foreground">Mar</span>
            </div>
            <!-- Apr -->
            <div class="flex flex-col items-center gap-3 flex-1">
              <div class="flex items-end gap-1.5 w-full justify-center h-[180px]">
                <div class="w-3.5 sm:w-6 bg-foreground rounded-t-[2px]" style="height: 50%;"></div>
                <div class="w-3.5 sm:w-6 bg-secondary rounded-t-[2px]" style="height: 32%;"></div>
              </div>
              <span class="text-[11px] font-medium text-foreground">Apr</span>
            </div>
            <!-- Mei -->
            <div class="flex flex-col items-center gap-3 flex-1">
              <div class="flex items-end gap-1.5 w-full justify-center h-[180px]">
                <div class="w-3.5 sm:w-6 bg-foreground rounded-t-[2px]" style="height: 75%;"></div>
                <div class="w-3.5 sm:w-6 bg-secondary rounded-t-[2px]" style="height: 40%;"></div>
              </div>
              <span class="text-[11px] font-medium text-foreground">Mei</span>
            </div>
            <!-- Jun -->
            <div class="flex flex-col items-center gap-3 flex-1">
              <div class="flex items-end gap-1.5 w-full justify-center h-[180px]">
                <div class="w-3.5 sm:w-6 bg-foreground rounded-t-[2px]" style="height: 60%;"></div>
                <div class="w-3.5 sm:w-6 bg-secondary rounded-t-[2px]" style="height: 38%;"></div>
              </div>
              <span class="text-[11px] font-medium text-foreground">Jun</span>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Donut Chart -->
      <Card class="lg:col-span-1 shadow-sm border-border">
        <CardHeader class="pb-0">
          <CardTitle class="text-base font-bold">Distribusi Pendapatan per Unit</CardTitle>
        </CardHeader>
        <CardContent class="flex flex-col items-center justify-center pt-6">
          <div class="relative w-48 h-48 flex items-center justify-center">
            <!-- CSS Donut ring using conic-gradient and mask -->
            <div class="absolute inset-0 rounded-full border-[20px] border-muted" style="background: conic-gradient(hsl(var(--foreground)) 0% 45%, hsl(var(--muted-foreground)) 45% 75%, hsl(var(--secondary)) 75% 100%); -webkit-mask: radial-gradient(transparent 58%, black 59%); mask: radial-gradient(transparent 58%, black 59%);"></div>
            <!-- Center Text -->
            <div class="text-center z-10 flex flex-col items-center justify-center">
              <div class="text-[11px] text-muted-foreground font-semibold">SMA Terpadu</div>
              <div class="text-3xl font-bold mt-1">45%</div>
            </div>
          </div>
          
          <div class="w-full mt-8 space-y-3 text-xs">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-sm bg-foreground"></div>
                <span class="font-medium text-foreground">SMA Terpadu</span>
              </div>
              <span class="font-bold">Rp 1.93B</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-sm bg-muted-foreground"></div>
                <span class="font-medium text-muted-foreground">SMP IT Dharma</span>
              </div>
              <span class="font-bold">Rp 1.28B</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-sm bg-secondary"></div>
                <span class="font-medium text-muted-foreground">SD Dharma</span>
              </div>
              <span class="font-bold">Rp 1.07B</span>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Table -->
    <Card class="shadow-sm border-border">
      <div class="p-5 flex justify-between items-center border-b border-border">
        <h3 class="font-bold text-lg">Performa Finansial Antar Unit</h3>
        <Button variant="ghost" class="text-sm font-semibold p-0 h-auto hover:bg-transparent hover:text-primary">Lihat Semua Unit</Button>
      </div>
      <Table>
        <TableHeader class="bg-muted/30">
          <TableRow class="hover:bg-transparent">
            <TableHead class="font-semibold text-muted-foreground w-[250px] py-4">Nama Unit</TableHead>
            <TableHead class="font-semibold text-muted-foreground py-4">Total Pendapatan</TableHead>
            <TableHead class="font-semibold text-muted-foreground py-4">Expense Ratio</TableHead>
            <TableHead class="font-semibold text-muted-foreground py-4">Status Koleksi</TableHead>
            <TableHead class="font-semibold text-muted-foreground text-right py-4">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow>
            <TableCell class="py-4">
              <div class="font-bold text-foreground text-sm">SMA Terpadu</div>
              <div class="text-[11px] text-muted-foreground mt-0.5">Jakarta Selatan</div>
            </TableCell>
            <TableCell class="font-medium text-muted-foreground py-4">Rp 1.928.250.000</TableCell>
            <TableCell class="py-4">
              <div class="flex items-center gap-3">
                <div class="w-24 h-1.5 bg-secondary rounded-full overflow-hidden">
                  <div class="bg-primary h-full" style="width: 42%"></div>
                </div>
                <span class="text-xs font-semibold text-muted-foreground">42%</span>
              </div>
            </TableCell>
            <TableCell class="py-4">
              <Badge class="bg-primary/20 hover:bg-primary/20 text-primary border-none px-2.5 py-0.5 rounded-[4px] font-bold text-[10px] tracking-wide">HEALTHY</Badge>
            </TableCell>
            <TableCell class="text-right py-4">
              <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-foreground">
                <ExternalLink class="w-4 h-4" />
              </Button>
            </TableCell>
          </TableRow>
          
          <TableRow>
            <TableCell class="py-4">
              <div class="font-bold text-foreground text-sm">SMP IT Dharma</div>
              <div class="text-[11px] text-muted-foreground mt-0.5">Tangerang</div>
            </TableCell>
            <TableCell class="font-medium text-muted-foreground py-4">Rp 1.285.500.000</TableCell>
            <TableCell class="py-4">
              <div class="flex items-center gap-3">
                <div class="w-24 h-1.5 bg-secondary rounded-full overflow-hidden">
                  <div class="bg-foreground h-full" style="width: 68%"></div>
                </div>
                <span class="text-xs font-semibold text-muted-foreground">68%</span>
              </div>
            </TableCell>
            <TableCell class="py-4">
              <Badge class="bg-destructive/20 hover:bg-destructive/20 text-destructive border-none px-2.5 py-0.5 rounded-[4px] font-bold text-[10px] tracking-wide">WARNING</Badge>
            </TableCell>
            <TableCell class="text-right py-4">
              <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-foreground">
                <ExternalLink class="w-4 h-4" />
              </Button>
            </TableCell>
          </TableRow>

          <TableRow>
            <TableCell class="py-4">
              <div class="font-bold text-foreground text-sm">SD Dharma</div>
              <div class="text-[11px] text-muted-foreground mt-0.5">Bekasi</div>
            </TableCell>
            <TableCell class="font-medium text-muted-foreground py-4">Rp 1.071.250.000</TableCell>
            <TableCell class="py-4">
              <div class="flex items-center gap-3">
                <div class="w-24 h-1.5 bg-secondary rounded-full overflow-hidden">
                  <div class="bg-primary h-full" style="width: 35%"></div>
                </div>
                <span class="text-xs font-semibold text-muted-foreground">35%</span>
              </div>
            </TableCell>
            <TableCell class="py-4">
              <Badge class="bg-primary/20 hover:bg-primary/20 text-primary border-none px-2.5 py-0.5 rounded-[4px] font-bold text-[10px] tracking-wide">HEALTHY</Badge>
            </TableCell>
            <TableCell class="text-right py-4">
              <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-foreground">
                <ExternalLink class="w-4 h-4" />
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </Card>

    <!-- Bottom Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Aktivitas Finansial -->
      <Card class="shadow-sm border-border">
        <CardHeader class="flex flex-row items-center justify-between pb-5">
          <CardTitle class="text-lg font-bold">Aktivitas Finansial Terkini</CardTitle>
          <History class="w-5 h-5 text-foreground" />
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="flex gap-4">
            <div class="w-11 h-11 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 border border-primary/20">
              <ReceiptText class="w-5 h-5 text-primary" />
            </div>
            <div class="flex-1 space-y-1.5 mt-0.5">
              <div class="flex items-center justify-between">
                <p class="font-bold text-sm">Hibah Diterima - SMP IT</p>
                <span class="text-[10px] text-muted-foreground font-medium">Baru saja</span>
              </div>
              <p class="text-[13px] text-muted-foreground leading-relaxed">Penerimaan dana hibah sarana prasarana sebesar Rp 150.000.000 untuk laboratorium komputer.</p>
            </div>
          </div>
          
          <div class="w-full h-px bg-border/60"></div>
          
          <div class="flex gap-4">
            <div class="w-11 h-11 rounded-lg bg-secondary flex items-center justify-center shrink-0 border border-border">
              <CheckSquare class="w-5 h-5 text-secondary-foreground" />
            </div>
            <div class="flex-1 space-y-1.5 mt-0.5">
              <div class="flex items-center justify-between">
                <p class="font-bold text-sm">Persetujuan Anggaran - SMA</p>
                <span class="text-[10px] text-muted-foreground font-medium">2 jam lalu</span>
              </div>
              <p class="text-[13px] text-muted-foreground leading-relaxed">Ketua Yayasan telah menyetujui anggaran pengadaan alat lab kimia senilai Rp 85.000.000.</p>
            </div>
          </div>

          <div class="w-full h-px bg-border/60"></div>
          
          <div class="flex gap-4">
            <div class="w-11 h-11 rounded-lg bg-destructive/10 flex items-center justify-center shrink-0 border border-destructive/20">
              <AlertTriangle class="w-5 h-5 text-destructive" />
            </div>
            <div class="flex-1 space-y-1.5 mt-0.5">
              <div class="flex items-center justify-between">
                <p class="font-bold text-sm">Notifikasi Tunggakan - SD</p>
                <span class="text-[10px] text-muted-foreground font-medium">Kemarin</span>
              </div>
              <p class="text-[13px] text-muted-foreground leading-relaxed">Laporan bulanan menunjukkan adanya peningkatan tunggakan SPP sebesar 15% pada unit SD Dharma.</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Kepatuhan Audit -->
      <Card class="bg-primary relative overflow-hidden flex flex-col justify-between p-7 shadow-md border-border/80">
        <!-- Giant faint watermark icon (Shield) -->
        <ShieldCheck class="absolute -right-10 -top-10 w-64 h-64 text-slate-900/5 dark:text-white/5 pointer-events-none" />
        
        <div class="relative z-10 space-y-4">
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kepatuhan Audit & Transparansi</h2>
          <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed max-w-sm">
            Sistem EduFinance menjamin integritas data keuangan yayasan melalui enkripsi audit trail real-time di semua unit sekolah.
          </p>
        </div>
        
        <div class="relative z-10 flex items-end justify-between mt-16">
          <div>
            <p class="text-[10px] uppercase text-slate-500 dark:text-slate-400 font-bold tracking-widest">STATUS AUDIT TERAKHIR</p>
            <p class="text-base font-bold mt-1.5 text-slate-900 dark:text-white">Lulus Tanpa Pengecualian</p>
          </div>
          <div class="text-right">
            <p class="text-[10px] uppercase text-slate-500 dark:text-slate-400 font-bold tracking-widest">SKOR KESEHATAN FINANSIAL</p>
            <p class="text-3xl font-extrabold mt-1 tracking-tighter text-slate-900 dark:text-white">98<span class="text-slate-500 dark:text-slate-400 text-xl font-bold">/100</span></p>
          </div>
        </div>
      </Card>
    </div>
  </div>
</template>
