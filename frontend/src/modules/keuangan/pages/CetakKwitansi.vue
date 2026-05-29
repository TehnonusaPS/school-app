<script setup>
import { ref } from 'vue'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Filter,
  X,
  Download,
  Printer,
} from 'lucide-vue-next'

const transactions = ref([
  {
    id: '#RCP-2023-0801',
    studentName: 'Aditya Pratama',
    studentClass: 'Kelas XII - MIPA 1',
    date: '12 Aug 2023',
    amount: 'Rp 1.500.000',
    status: 'BERHASIL'
  },
  {
    id: '#RCP-2023-0802',
    studentName: 'Siti Nurhaliza',
    studentClass: 'Kelas X - IPS 3',
    date: '14 Aug 2023',
    amount: 'Rp 750.000',
    status: 'DICETAK'
  },
  {
    id: '#RCP-2023-0803',
    studentName: 'Bambang Wijaya',
    studentClass: 'Kelas XI - MIPA 4',
    date: '15 Aug 2023',
    amount: 'Rp 2.100.000',
    status: 'BERHASIL'
  }
])
</script>

<template>
  <div class="h-full flex flex-col xl:flex-row gap-6 text-foreground">
    
    <!-- Left Section: Data & Filter -->
    <div class="flex-1 space-y-6">
      
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Cetak Kuitansi Pembayaran</h1>
        <p class="text-muted-foreground mt-2 text-sm">Kelola, cari, dan cetak bukti pembayaran siswa dengan mudah.</p>
      </div>

      <!-- Filter Card -->
      <Card class="p-6 shadow-sm">
        <div class="flex flex-col md:flex-row gap-4 items-end">
          <div class="flex-1 space-y-2">
            <label class="text-sm font-semibold">Rentang Tanggal</label>
            <div class="flex items-center gap-2">
              <Input type="date" value="2026-05-28" class="w-full text-muted-foreground" />
              <span class="text-muted-foreground">-</span>
              <Input type="date" value="2026-05-28" class="w-full text-muted-foreground" />
            </div>
          </div>
          
          <div class="flex-1 space-y-2">
            <label class="text-sm font-semibold">Jenis Pembayaran</label>
            <Select default-value="semua">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Semua Jenis" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Jenis</SelectItem>
                <SelectItem value="spp">SPP Bulanan</SelectItem>
                <SelectItem value="gedung">Uang Gedung</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <Button class="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-2 px-6">
            <Filter class="w-4 h-4" />
            Terapkan Filter
          </Button>
        </div>
      </Card>

      <!-- Table Section -->
      <Card class="overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-muted/50 text-muted-foreground text-xs uppercase font-semibold border-b border-border">
              <tr>
                <th class="px-6 py-4 w-12">
                  <div class="w-4 h-4 border border-border rounded bg-background"></div>
                </th>
                <th class="px-6 py-4 tracking-wider">NO. KUITANSI</th>
                <th class="px-6 py-4 tracking-wider">SISWA</th>
                <th class="px-6 py-4 tracking-wider">TANGGAL</th>
                <th class="px-6 py-4 tracking-wider">JUMLAH</th>
                <th class="px-6 py-4 tracking-wider">STATUS</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr v-for="tx in transactions" :key="tx.id" class="hover:bg-muted/30 transition-colors cursor-pointer">
                <td class="px-6 py-4">
                  <div class="w-4 h-4 border border-border rounded bg-background"></div>
                </td>
                <td class="px-6 py-4 font-semibold text-foreground">{{ tx.id }}</td>
                <td class="px-6 py-4">
                  <p class="font-bold text-foreground">{{ tx.studentName }}</p>
                  <p class="text-xs text-muted-foreground mt-1">{{ tx.studentClass }}</p>
                </td>
                <td class="px-6 py-4 text-muted-foreground">{{ tx.date }}</td>
                <td class="px-6 py-4 font-bold text-foreground">{{ tx.amount }}</td>
                <td class="px-6 py-4">
                  <span 
                    class="px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase"
                    :class="tx.status === 'BERHASIL' ? 'bg-primary/10 text-primary' : 'bg-secondary text-secondary-foreground'"
                  >
                    {{ tx.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-border flex items-center justify-between bg-muted/10">
          <p class="text-xs text-muted-foreground">Menampilkan 1-10 dari 124 transaksi</p>
          <div class="flex gap-2">
            <Button variant="outline" size="icon" class="w-8 h-8 rounded-md bg-background">&lt;</Button>
            <Button variant="default" size="icon" class="w-8 h-8 rounded-md bg-primary text-primary-foreground">1</Button>
            <Button variant="outline" size="icon" class="w-8 h-8 rounded-md bg-background">2</Button>
            <Button variant="outline" size="icon" class="w-8 h-8 rounded-md bg-background">3</Button>
            <Button variant="outline" size="icon" class="w-8 h-8 rounded-md bg-background">&gt;</Button>
          </div>
        </div>
      </Card>
    </div>

    <!-- Right Section: Preview -->
    <div class="w-full xl:w-[400px] flex-shrink-0 space-y-5">
      
      <div class="flex items-center justify-between pt-1">
        <h2 class="text-lg font-bold tracking-tight">Pratinjau Kuitansi</h2>
        <Button variant="ghost" size="icon" class="text-muted-foreground rounded-full hover:bg-muted w-8 h-8">
          <X class="w-4 h-4" />
        </Button>
      </div>

      <!-- Receipt Paper (Always Light theme explicitly for printing preview realism) -->
      <div class="bg-white text-slate-900 p-6 shadow-sm border border-slate-200 relative rounded-sm mx-auto w-full max-w-[360px]">
        <!-- Lunas Watermark -->
        <div class="absolute bottom-28 right-4 opacity-[0.15] rotate-[-12deg] pointer-events-none flex items-center justify-center">
          <div class="border-[4px] border-slate-500 text-slate-500 text-4xl font-black px-4 py-2 rounded-lg tracking-widest">
            LUNAS
          </div>
        </div>

        <!-- Header -->
        <div class="text-center mb-5">
          <h3 class="text-lg font-black tracking-tight uppercase text-black">SMK Nusantara Jaya</h3>
          <p class="text-[9px] mt-1 text-slate-600 font-medium">Jl. Pendidikan No. 123, Jakarta Selatan</p>
          <p class="text-[9px] text-slate-600 font-medium">Telp: (021) 555-0123 | Email: finance@smknusantara.id</p>
        </div>

        <div class="border-b-[1.5px] border-black mb-5"></div>

        <!-- Receipt Meta -->
        <div class="flex justify-between items-start mb-5">
          <div>
            <p class="text-[8px] font-bold text-slate-500 tracking-wider">KUITANSI PEMBAYARAN</p>
            <p class="text-xs font-bold mt-1 text-black">#RCP-2023-0801</p>
          </div>
          <div class="text-right">
            <p class="text-[8px] font-bold text-slate-500 tracking-wider">TANGGAL CETAK</p>
            <p class="text-xs font-bold mt-1 text-black">20/08/2023</p>
          </div>
        </div>

        <!-- Payer Info -->
        <div class="mb-5">
          <p class="text-[8px] font-bold text-slate-500 tracking-wider mb-1.5">TELAH TERIMA DARI:</p>
          <p class="font-bold text-sm text-black">Aditya Pratama</p>
          <p class="text-[10px] font-medium text-slate-600 mt-0.5">XII - MIPA 1 (NIS: 192010442)</p>
        </div>

        <div class="border-b border-dashed border-slate-300 mb-5"></div>

        <!-- Details -->
        <div class="mb-5">
          <p class="text-[8px] font-bold text-slate-500 tracking-wider mb-2">UNTUK PEMBAYARAN:</p>
          <ul class="text-xs space-y-1 font-semibold text-black">
            <li>SPP Bulan Agustus 2023</li>
            <li>Iuran Perpustakaan Semester Ganjil</li>
          </ul>
        </div>

        <div class="border-b border-dashed border-slate-300 mb-5"></div>

        <!-- Total Box -->
        <div class="bg-slate-50 p-4 flex items-center justify-between mb-4 border border-slate-200">
          <p class="text-[10px] font-bold tracking-wider text-black">JUMLAH TOTAL:</p>
          <p class="text-lg font-black text-black">Rp 1.500.000</p>
        </div>

        <!-- Terbilang -->
        <div class="mb-8">
          <p class="text-[8px] font-bold text-slate-500 tracking-wider mb-1">TERBILANG:</p>
          <p class="text-[11px] italic font-bold text-black">"Satu Juta Lima Ratus Ribu Rupiah"</p>
        </div>

        <!-- Signatures -->
        <div class="flex justify-between items-end mt-12 text-xs">
          <div class="text-center">
            <p class="mb-10 font-medium text-[10px]">Penyetor,</p>
            <p class="text-[10px] font-bold">( ........................................ )</p>
          </div>
          <div class="text-center">
            <p class="mb-10 font-bold text-[10px] underline underline-offset-4">Admin Utama</p>
            <p class="text-[8px] font-medium text-slate-500">NIP. 19880122001</p>
          </div>
        </div>

        <!-- Bottom decoration -->
        <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-black rounded-b-sm"></div>
      </div>

      <!-- Actions -->
      <div class="flex gap-4 pt-2">
        <Button variant="outline" class="flex-1 flex items-center gap-2 h-12 border-border bg-card hover:bg-accent font-semibold text-sm">
          <Download class="w-4 h-4" />
          Simpan PDF
        </Button>
        <Button class="flex-1 flex items-center gap-2 h-12 bg-primary text-primary-foreground hover:bg-primary/90 font-semibold text-sm shadow-md">
          <Printer class="w-4 h-4" />
          Cetak Sekarang
        </Button>
      </div>

      <!-- Tips -->
      <div class="p-6 border border-border bg-card rounded-xl shadow-sm">
        <h4 class="text-xs font-extrabold tracking-wider uppercase mb-2">TIPS CETAK</h4>
        <p class="text-xs text-muted-foreground leading-relaxed font-medium">
          Gunakan kertas HVS A5 untuk hasil terbaik. Pastikan 'Headers and Footers' dimatikan pada pengaturan browser sebelum mencetak.
        </p>
      </div>

    </div>

  </div>
</template>
