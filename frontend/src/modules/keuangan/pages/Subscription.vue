<script setup>
import {
  Banknote,
  Building2,
  UserMinus,
  Cloud,
  Plus,
  Download,
  Pencil,
  Ban,
  MoreVertical,
  ShieldCheck,
  RefreshCw,
  Upload,
  TrendingUp,
  PlusCircle,
  TrendingDown
} from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'

import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

// Data for StatCard
const kpiData = [
  {
    label: 'TOTAL MRR',
    value: 'Rp 485.200k',
    trend: '+8.4% bln ini',
    trendDirection: 'up',
    icon: Banknote,
    variant: 'emerald'
  },
  {
    label: 'TENANT AKTIF',
    value: '142 Institusi',
    trend: '+12 Baru',
    trendDirection: 'up',
    icon: Building2,
    variant: 'blue'
  },
  {
    label: 'CHURN RATE',
    value: '1.2%',
    trend: '-0.5% (Sehat)',
    trendDirection: 'down',
    icon: UserMinus,
    variant: 'amber'
  },
  {
    label: 'PLATFORM HEALTH',
    value: '99.9%',
    progress: 99.9,
    icon: Cloud,
    variant: 'primary'
  }
]
</script>

<template>
<<<<<<< Updated upstream
  <div class="space-y-6 w-full mx-auto px-0">
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
    
    <Card>
      <div class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center border-b gap-4">
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-muted-foreground">Filter Paket:</span>
            <Select>
              <SelectTrigger class="w-[140px] h-8">
                <SelectValue placeholder="Semua Paket" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Paket</SelectItem>
                <SelectItem value="enterprise">Enterprise</SelectItem>
                <SelectItem value="professional">Professional</SelectItem>
                <SelectItem value="basic">Basic</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-muted-foreground">Status:</span>
            <Select>
              <SelectTrigger class="w-[140px] h-8">
                <SelectValue placeholder="Semua Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Status</SelectItem>
                <SelectItem value="aktif">Aktif</SelectItem>
                <SelectItem value="overdue">Overdue</SelectItem>
                <SelectItem value="trialing">Trialing</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <Button class="h-8">
            <Plus class="w-4 h-4 mr-1" /> Tambah Langganan Baru
          </Button>
          <Button variant="outline" class="h-8">
            <Download class="w-4 h-4 mr-1" /> Ekspor Data
          </Button>
        </div>
      </div>
      
      <Table>
        <TableHeader class="bg-muted/50">
          <TableRow>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">INSTITUSI (TENANT)</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">PAKET</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">STATUS</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground text-center">TGL AKTIVASI</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground text-center">JATUH TEMPO</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">NILAI KONTRAK</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">AKSI</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow>
            <TableCell>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded bg-primary text-primary-foreground flex items-center justify-center font-bold text-xs">ST</div>
                <div>
                  <div class="font-bold text-foreground">SMA Terpadu Harapan</div>
                  <div class="text-xs text-muted-foreground">ID: T-10024</div>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <Badge class="bg-primary hover:bg-primary/90 text-primary-foreground rounded-[4px] text-[10px] px-2 py-0.5">ENTERPRISE</Badge>
            </TableCell>
            <TableCell>
              <Badge variant="outline" class="text-emerald-600 bg-emerald-50 border-emerald-100 px-2 py-0.5 font-medium" showDot>Aktif</Badge>
            </TableCell>
            <TableCell>
              <div class="text-sm text-muted-foreground text-center">12 Jan<br/>2024</div>
            </TableCell>
            <TableCell>
              <div class="text-sm text-muted-foreground text-center">12 Jan<br/>2025</div>
            </TableCell>
            <TableCell>
              <div class="font-bold text-foreground">Rp 120.000.000</div>
            </TableCell>
            <TableCell>
              <div class="flex items-center gap-3 text-muted-foreground">
                <button class="hover:text-foreground"><Pencil class="w-4 h-4" /></button>
                <button class="hover:text-foreground"><Ban class="w-4 h-4" /></button>
                <button class="hover:text-foreground"><MoreVertical class="w-4 h-4" /></button>
              </div>
            </TableCell>
          </TableRow>

          <TableRow>
            <TableCell>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded bg-muted text-muted-foreground flex items-center justify-center font-bold text-xs">MP</div>
                <div>
                  <div class="font-bold text-foreground">Madrasah Pusat Al-Ikhlas</div>
                  <div class="text-xs text-muted-foreground">ID: T-09882</div>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <Badge variant="secondary" class="rounded-[4px] text-[10px] px-2 py-0.5">PROFESSIONAL</Badge>
            </TableCell>
            <TableCell>
              <Badge variant="outline" class="text-red-500 bg-red-50 border-red-100 px-2 py-0.5 font-medium" showDot>Overdue</Badge>
            </TableCell>
            <TableCell>
              <div class="text-sm text-muted-foreground text-center">05 Okt<br/>2023</div>
            </TableCell>
            <TableCell>
              <div class="text-sm font-bold text-red-500 text-center">05 Okt<br/>2024</div>
            </TableCell>
            <TableCell>
              <div class="font-bold text-foreground">Rp 45.000.000</div>
            </TableCell>
            <TableCell>
              <div class="flex items-center gap-3 text-muted-foreground">
                <button class="hover:text-foreground"><Pencil class="w-4 h-4" /></button>
                <button class="hover:text-foreground"><Ban class="w-4 h-4" /></button>
                <button class="hover:text-foreground"><MoreVertical class="w-4 h-4" /></button>
              </div>
            </TableCell>
          </TableRow>

          <TableRow>
            <TableCell>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded bg-muted-foreground/20 text-foreground flex items-center justify-center font-bold text-xs">AZ</div>
                <div>
                  <div class="font-bold text-foreground">SMP IT Al-Azhar</div>
                  <div class="text-xs text-muted-foreground">ID: T-10255</div>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <Badge variant="outline" class="rounded-[4px] text-[10px] px-2 py-0.5 text-muted-foreground font-medium">BASIC</Badge>
            </TableCell>
            <TableCell>
              <Badge variant="outline" class="text-blue-600 bg-blue-50 border-blue-100 px-2 py-0.5 font-medium" showDot>Trialing</Badge>
            </TableCell>
            <TableCell>
              <div class="text-sm text-muted-foreground text-center">20 Jun<br/>2024</div>
            </TableCell>
            <TableCell>
              <div class="text-sm text-muted-foreground text-center">04 Jul<br/>2024</div>
            </TableCell>
            <TableCell>
              <div class="font-bold text-foreground">Rp 0 (Trial)</div>
            </TableCell>
            <TableCell>
              <div class="flex items-center gap-3 text-muted-foreground">
                <button class="hover:text-foreground"><Pencil class="w-4 h-4" /></button>
                <button class="hover:text-foreground"><Ban class="w-4 h-4" /></button>
                <button class="hover:text-foreground"><MoreVertical class="w-4 h-4" /></button>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
      
      <div class="p-4 border-t flex justify-between items-center text-sm text-muted-foreground">
        <div>Menampilkan 1-10 dari 142 Institusi</div>
        <div class="flex items-center gap-1">
          <Button variant="outline" class="w-8 h-8 p-0">&lt;</Button>
          <Button class="w-8 h-8 p-0">1</Button>
          <Button variant="outline" class="w-8 h-8 p-0">2</Button>
          <Button variant="outline" class="w-8 h-8 p-0">3</Button>
          <Button variant="outline" class="w-8 h-8 p-0">&gt;</Button>
        </div>
      </div>
    </Card>

    <Card>
      <CardHeader class="border-b pb-4 pt-5">
        <CardTitle class="text-xl font-bold">Log Aktivitas Langganan</CardTitle>
      </CardHeader>
      <CardContent class="p-0">
        <div class="divide-y text-sm border-border">
          <div class="flex items-center justify-between p-5">
            <div class="flex items-center gap-8">
              <div class="text-muted-foreground w-24">14:45 WIB</div>
              <div>
                <div class="font-bold text-foreground text-base">Registrasi Tenant Baru: SMP IT Al-Azhar</div>
                <div class="text-muted-foreground italic text-xs mt-0.5">ID: T-10255 | Paket: Basic (Trial)</div>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <Badge class="bg-emerald-100 hover:bg-emerald-100 text-emerald-700 border-none font-semibold px-2 text-[10px]">SIGNUP</Badge>
              <ShieldCheck class="w-5 h-5 text-emerald-600" />
            </div>
          </div>
          
          <div class="flex items-center justify-between p-5">
            <div class="flex items-center gap-8">
              <div class="text-muted-foreground w-24">12:30 WIB</div>
              <div>
                <div class="font-bold text-foreground text-base">Pembaruan Langganan: SMA Negeri 1 Pusat</div>
                <div class="text-muted-foreground italic text-xs mt-0.5">ID: T-08442 | Paket: Professional (Tahunan)</div>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-emerald-600 font-medium">+ Rp 18.000.000</span>
              <RefreshCw class="w-5 h-5 text-emerald-600" />
            </div>
          </div>
          
          <div class="flex items-center justify-between p-5">
            <div class="flex items-center gap-8">
              <div class="text-muted-foreground w-24">09:15 WIB</div>
              <div>
                <div class="font-bold text-foreground text-base">Upgrade Paket: SMK Teknik Global</div>
                <div class="text-muted-foreground italic text-xs mt-0.5">ID: T-09110 | Basic &rarr; Professional</div>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span class="font-medium text-foreground">+ Rp 1.500.000 (Prorated)</span>
              <Upload class="w-5 h-5 text-foreground" />
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
=======
  <PlaceholderPage :icon="BellRing" />
</template>
>>>>>>> Stashed changes
