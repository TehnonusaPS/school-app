<script setup>
import { Card } from '@/components/ui/card'
import {
  HelpCircle,
  Unlock,
  Lock
} from 'lucide-vue-next'

defineProps({
  isReadOnly: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div class="space-y-6 text-left">
    <!-- Legend Card -->
    <Card class="p-5 rounded-2xl border border-border bg-card shadow-xs">
      <h3 class="font-bold text-sm tracking-wider uppercase mb-4 text-primary">KETERANGAN ABSENSI</h3>
      
      <div class="space-y-4">
        <div class="flex items-center gap-3">
          <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white font-extrabold flex items-center justify-center text-xs shadow-sm">H</span>
          <div class="text-xs">
            <p class="font-bold text-foreground">Hadir</p>
            <p class="text-muted-foreground">Siswa menghadiri proses belajar-mengajar.</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="w-8 h-8 rounded-lg bg-amber-500 text-white font-extrabold flex items-center justify-center text-xs shadow-sm">T</span>
          <div class="text-xs">
            <p class="font-bold text-foreground">Terlambat</p>
            <p class="text-muted-foreground">Siswa hadir melewati batas toleransi keterlambatan sekolah.</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="w-8 h-8 rounded-lg bg-blue-500 text-white font-extrabold flex items-center justify-center text-xs shadow-sm">S</span>
          <div class="text-xs">
            <p class="font-bold text-foreground">Sakit</p>
            <p class="text-muted-foreground">Siswa sakit dengan surat keterangan wali/dokter.</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="w-8 h-8 rounded-lg bg-amber-500 text-white font-extrabold flex items-center justify-center text-xs shadow-sm">I</span>
          <div class="text-xs">
            <p class="font-bold text-foreground">Izin</p>
            <p class="text-muted-foreground">Siswa meminta izin untuk kepentingan mendesak.</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="w-8 h-8 rounded-lg bg-red-500 text-white font-extrabold flex items-center justify-center text-xs shadow-sm">A</span>
          <div class="text-xs">
            <p class="font-bold text-foreground">Alpha</p>
            <p class="text-muted-foreground">Tanpa keterangan atau tidak memberi kabar.</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="w-8 h-8 rounded-lg bg-muted border border-border text-muted-foreground/60 font-extrabold flex items-center justify-center text-xs">?</span>
          <div class="text-xs">
            <p class="font-bold text-foreground">Belum Diisi</p>
            <p class="text-muted-foreground">Data presensi tanggal tersebut belum dimasukkan.</p>
          </div>
        </div>
      </div>

      <div class="border-t border-border mt-5 pt-4 text-[11px] text-muted-foreground leading-relaxed flex items-start gap-1.5 font-medium">
        <HelpCircle class="w-4 h-4 text-primary shrink-0 mt-0.5" />
        <span>Klik satu kali pada kotak tanggal untuk mengubah status kehadiran secara berurutan. Kolom berwarna merah muda adalah hari Sabtu & Minggu (Libur).</span>
      </div>
    </Card>
  </div>
</template>
