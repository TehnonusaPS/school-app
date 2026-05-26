<script setup lang="ts">
import { TrendingUp, MessageCircle } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Progress } from '@/components/ui/progress'

const akademik = {
  nilai: 85.8,
  max: 100,
  naik: '+0.12 dari semester lalu',
  kehadiran: 85
}

const pesan = [
  {
    nama: 'Bapak Udin (Wali Kelas)',
    isi: 'Tingkatkan belajarmu ya, semangat!',
    waktu: '10 menit lalu',
    inisial: 'BU',
    warna: 'bg-blue-500'
  },
  {
    nama: 'Ibu Renata (Guru Matematika)',
    isi: 'Belajar sungguh-sungguh untuk UTS minggu depan.',
    waktu: '1 jam lalu',
    inisial: 'IR',
    warna: 'bg-violet-500'
  },
  {
    nama: 'Pak Supri (Guru B. Inggris)',
    isi: 'Jangan lupa kumpulkan tugas essay besok!',
    waktu: '2 jam lalu',
    inisial: 'PS',
    warna: 'bg-emerald-500'
  }
]
</script>

<template>
  <div class="lg:col-span-2 flex flex-col gap-4">
    <!-- Akademik -->
    <Card>
      <CardHeader class="pb-2">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <TrendingUp class="size-4 text-primary" />
            <CardTitle class="text-base font-semibold">Akademik</CardTitle>
          </div>
        </div>
      </CardHeader>
      <CardContent class="space-y-4">
        <div>
          <div class="flex items-baseline gap-1">
            <span class="text-4xl font-bold tracking-tight">{{ akademik.nilai }}</span>
            <span class="text-sm text-muted-foreground font-medium">/{{ akademik.max }}</span>
          </div>
          <p class="text-xs text-muted-foreground mt-1">Nilai Rata-rata Saat Ini</p>
          <p class="text-xs font-semibold text-emerald-500 mt-0.5">{{ akademik.naik }}</p>
        </div>
        <div class="space-y-1.5">
          <div class="flex items-center justify-between text-xs">
            <span class="text-muted-foreground">Kehadiran</span>
            <span class="font-semibold">{{ akademik.kehadiran }}%</span>
          </div>
          <Progress :model-value="akademik.kehadiran" class="h-2" />
        </div>
      </CardContent>
    </Card>

    <!-- Komunikasi Terakhir -->
    <Card class="flex-1">
      <CardHeader class="pb-3">
        <div class="flex items-center gap-2">
          <MessageCircle class="size-4 text-primary" />
          <CardTitle class="text-base font-semibold">Komunikasi Terakhir</CardTitle>
        </div>
        <CardDescription class="text-xs">Pesan dari guru & wali kelas</CardDescription>
      </CardHeader>
      <CardContent class="space-y-3">
        <div
          v-for="(p, i) in pesan"
          :key="i"
          class="flex items-start gap-3 rounded-lg p-2 hover:bg-muted/40 transition-colors cursor-default"
        >
          <div :class="['size-9 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0', p.warna]">
            {{ p.inisial }}
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold leading-snug truncate">{{ p.nama }}</p>
            <p class="text-xs text-muted-foreground mt-0.5 line-clamp-1">{{ p.isi }}</p>
            <p class="text-[10px] text-muted-foreground/70 mt-0.5">{{ p.waktu }}</p>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
