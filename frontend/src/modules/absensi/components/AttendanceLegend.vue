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

    <!-- Lock/Security info Card -->
    <Card class="p-6 bg-slate-900 text-white rounded-2xl shadow-md border border-slate-800 relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-slate-800/40 to-transparent pointer-events-none"></div>
      <div class="relative z-10 space-y-3">
        <div class="flex items-center gap-2 text-emerald-400">
          <Unlock class="w-5 h-5" v-if="!isReadOnly" />
          <Lock class="w-5 h-5 text-amber-500" v-else />
          <h4 class="font-bold text-sm tracking-wide">{{ isReadOnly ? 'Mode Riwayat Terkunci' : 'Mode Edit Terbuka' }}</h4>
        </div>
        <p class="text-xs text-slate-300 leading-relaxed font-medium">
          {{ isReadOnly 
            ? 'Absensi semester lampau diarsipkan secara aman untuk laporan rapor akhir dan tidak dapat diedit ulang tanpa persetujuan Admin Kurikulum.' 
            : 'Silakan isi kehadiran siswa hari ini. Perubahan data kehadiran akan langsung tersimpan secara otomatis.' 
          }}
        </p>
      </div>
    </Card>
  </div>
</template>
