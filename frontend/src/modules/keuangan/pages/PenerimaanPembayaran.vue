<script setup>
import { ref } from 'vue'
import { Card } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Textarea } from '@/components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Search,
  CheckCircle2,
  History,
  ShieldCheck
} from 'lucide-vue-next'

const recentActivities = ref([
  {
    id: 1,
    name: 'Riana Amalia',
    type: 'SPP Juni 2024',
    time: '10:45 • ID #TX9823',
    method: 'Tunai',
    amount: 'Rp 500k',
    methodClass: 'bg-primary/10 text-primary'
  },
  {
    id: 2,
    name: 'Dimas Setiawan',
    type: 'Daftar Ulang',
    time: '09:12 • ID #TX9822',
    method: 'Transfer',
    amount: 'Rp 2.500k',
    methodClass: 'bg-secondary text-secondary-foreground'
  },
  {
    id: 3,
    name: 'Eka Putri',
    type: 'SPP Mei 2024',
    time: 'Yesterday • ID #TX9821',
    method: 'Tunai',
    amount: 'Rp 500k',
    methodClass: 'bg-primary/10 text-primary'
  }
])
</script>

<template>
  <div class="h-full space-y-6 text-foreground pb-10">
    
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold tracking-tight">Penerimaan Pembayaran</h1>
      <p class="text-muted-foreground mt-2 text-sm">Rekam pembayaran SPP siswa secara instan dan akurat.</p>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-6">
      
      <!-- Left Column -->
      <div class="space-y-6">
        
        <!-- Cari Siswa -->
        <Card class="p-6 shadow-sm">
          <label class="block text-sm font-bold mb-3">Cari Siswa (Nama atau NIS)</label>
          <div class="relative mb-4">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <Input type="text" placeholder="Masukkan nama siswa atau nomor induk siswa..." class="pl-9 h-11" />
          </div>
          
          <!-- Selected Student (Mock) -->
          <div class="bg-muted/30 border border-border rounded-lg p-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold">
                BP
              </div>
              <div>
                <h4 class="font-bold text-sm">Budi Pratama</h4>
                <p class="text-xs text-muted-foreground mt-0.5">NIS: 20230045 • Kelas 10-A IPA</p>
              </div>
            </div>
            <CheckCircle2 class="w-5 h-5 text-primary" />
          </div>
        </Card>

        <!-- Tagihan Aktif -->
        <Card class="p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4 border-b border-border pb-4">
            <h2 class="text-lg font-bold">Tagihan Aktif</h2>
            <span class="bg-destructive/10 text-destructive text-xs font-bold px-3 py-1 rounded-md">
              3 Tunggakan
            </span>
          </div>
          
          <div class="space-y-3">
            <!-- Item 1 -->
            <div class="flex items-center justify-between border border-border p-4 rounded-lg bg-muted/20">
              <div class="flex items-start gap-4">
                <Checkbox id="tagihan-1" :checked="true" class="mt-1" />
                <div class="grid gap-1.5">
                  <label for="tagihan-1" class="text-sm font-bold leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer">
                    SPP Juli 2024
                  </label>
                  <p class="text-xs text-muted-foreground">Jatuh tempo: 10 Jul 2024</p>
                </div>
              </div>
              <div class="font-bold text-sm">Rp 500.000</div>
            </div>

            <!-- Item 2 -->
            <div class="flex items-center justify-between border border-border p-4 rounded-lg hover:bg-muted/10 transition-colors">
              <div class="flex items-start gap-4">
                <Checkbox id="tagihan-2" class="mt-1" />
                <div class="grid gap-1.5">
                  <label for="tagihan-2" class="text-sm font-bold leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer">
                    SPP Agustus 2024
                  </label>
                  <p class="text-xs text-muted-foreground">Jatuh tempo: 10 Agt 2024</p>
                </div>
              </div>
              <div class="font-bold text-sm">Rp 500.000</div>
            </div>

            <!-- Item 3 -->
            <div class="flex items-center justify-between border border-border p-4 rounded-lg hover:bg-muted/10 transition-colors">
              <div class="flex items-start gap-4">
                <Checkbox id="tagihan-3" class="mt-1" />
                <div class="grid gap-1.5">
                  <label for="tagihan-3" class="text-sm font-bold leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer">
                    Iuran Tahunan
                  </label>
                  <p class="text-xs text-muted-foreground">Wajib dibayar</p>
                </div>
              </div>
              <div class="font-bold text-sm">Rp 1.250.000</div>
            </div>
          </div>
        </Card>

        <!-- Rincian Pembayaran -->
        <Card class="p-6 shadow-sm">
          <h2 class="text-lg font-bold mb-5">Rincian Pembayaran</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div class="space-y-2">
              <label class="text-sm font-bold text-muted-foreground">Jumlah yang Dibayar (Rp)</label>
              <Input type="text" value="500.000" class="h-11 font-bold" />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-muted-foreground">Metode Pembayaran</label>
              <Select default-value="tunai">
                <SelectTrigger class="h-11 font-medium">
                  <SelectValue placeholder="Pilih Metode" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="tunai">Tunai (Cash)</SelectItem>
                  <SelectItem value="transfer">Transfer Bank</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div class="space-y-2 mb-6">
            <label class="text-sm font-bold text-muted-foreground">Catatan Tambahan (Opsional)</label>
            <Textarea placeholder="Contoh: Dititipkan melalui wali kelas..." class="resize-none min-h-[80px]" />
          </div>

          <div class="border-t border-border pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <p class="text-xs font-bold text-muted-foreground tracking-widest uppercase mb-1">TOTAL PEMBAYARAN</p>
              <h2 class="text-3xl font-black">Rp 500.000</h2>
            </div>
            <Button class="bg-primary text-primary-foreground hover:bg-primary/90 h-12 px-6 font-bold flex items-center gap-2 shadow-sm">
              <CheckCircle2 class="w-5 h-5" />
              Catat Pembayaran
            </Button>
          </div>
        </Card>

      </div>

      <!-- Right Column -->
      <div class="space-y-6">
        
        <!-- Aktivitas Terkini -->
        <Card class="shadow-sm">
          <div class="p-5 flex items-center gap-2 border-b border-border">
            <History class="w-5 h-5 text-muted-foreground" />
            <h3 class="font-bold text-lg">Aktivitas Terkini</h3>
          </div>
          <div class="divide-y divide-border">
            <div v-for="act in recentActivities" :key="act.id" class="p-5 flex items-start justify-between">
              <div>
                <h4 class="font-bold text-sm">{{ act.name }}</h4>
                <p class="text-[11px] font-medium text-muted-foreground mt-1">{{ act.type }}</p>
                <p class="text-[10px] text-muted-foreground mt-0.5">{{ act.time }}</p>
              </div>
              <div class="text-right flex flex-col items-end">
                <span :class="['text-[9px] px-2 py-0.5 rounded font-bold tracking-wider mb-1', act.methodClass]">
                  {{ act.method }}
                </span>
                <span class="font-bold text-sm">{{ act.amount }}</span>
              </div>
            </div>
          </div>
          <div class="p-4 border-t border-border text-center">
            <a href="#" class="text-sm font-bold text-muted-foreground hover:text-foreground">
              Lihat Semua Riwayat
            </a>
          </div>
        </Card>

        <!-- Sistem Aman Box -->
        <div class="bg-primary/5 p-6 rounded-xl shadow-md border border-border">
          <div class="flex items-center gap-2 mb-4">
            <ShieldCheck class="w-5 h-5 text-primary" />
            <h3 class="font-bold text-sm text-foreground">Sistem Aman</h3>
          </div>
          <p class="text-xs leading-relaxed font-medium text-muted-foreground">
            Sesi Anda diaudit oleh sistem keamanan sekolah. Setiap transaksi akan langsung dicatat ke buku besar keuangan sekolah.
          </p>
        </div>

      </div>

    </div>
  </div>
</template>
