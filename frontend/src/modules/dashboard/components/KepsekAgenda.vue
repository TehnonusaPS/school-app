<script setup lang="ts">
import { CalendarDays, ChevronRight } from 'lucide-vue-next'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle
} from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'

const agendas = [
  {
    tanggal: '24',
    bulan: 'Okt',
    judul: 'Rapat Kurikulum 2026',
    waktu: '08:00 – 10:00',
    tempat: 'Ruang Rapat A',
    type: 'rapat'
  },
  {
    tanggal: '27',
    bulan: 'Okt',
    judul: 'Penerimaan Tamu Dinas',
    waktu: '09:00 – 11:00',
    tempat: 'Aula Utama',
    type: 'tamu'
  },
  {
    tanggal: '30',
    bulan: 'Okt',
    judul: 'Evaluasi Semester Ganjil',
    waktu: '13:00 – 15:00',
    tempat: 'Ruang Guru',
    type: 'evaluasi'
  },
  {
    tanggal: '02',
    bulan: 'Nov',
    judul: 'Upacara Hari Sumpah Pemuda',
    waktu: '07:00 – 08:30',
    tempat: 'Lapangan Sekolah',
    type: 'upacara'
  }
]

const typeColor: Record<string, string> = {
  rapat:    'bg-blue-500',
  tamu:     'bg-violet-500',
  evaluasi: 'bg-amber-500',
  upacara:  'bg-emerald-500'
}
</script>

<template>
  <Card class="lg:col-span-2 flex flex-col">
    <CardHeader class="pb-2">
      <div class="flex items-center gap-2">
        <CalendarDays class="size-4 text-primary" />
        <CardTitle class="text-base font-semibold">Agenda Sekolah</CardTitle>
      </div>
      <CardDescription class="text-xs">Jadwal kegiatan yang akan datang</CardDescription>
    </CardHeader>

    <CardContent class="flex-1 space-y-3">
      <div
        v-for="(agenda, i) in agendas"
        :key="i"
        class="flex items-start gap-3 rounded-lg p-2 transition-colors hover:bg-muted/50 cursor-default"
      >
        <!-- Tanggal box -->
        <div :class="['flex flex-col items-center justify-center rounded-lg text-white min-w-[44px] py-1.5', typeColor[agenda.type] ?? 'bg-primary']">
          <span class="text-base font-bold leading-none">{{ agenda.tanggal }}</span>
          <span class="text-[10px] font-medium leading-none mt-0.5">{{ agenda.bulan }}</span>
        </div>
        <!-- Detail -->
        <div class="min-w-0 flex-1">
          <p class="text-sm font-semibold leading-snug truncate">{{ agenda.judul }}</p>
          <p class="text-[11px] text-muted-foreground mt-0.5">
            {{ agenda.waktu }} &bull; {{ agenda.tempat }}
          </p>
        </div>
      </div>
    </CardContent>

    <CardFooter class="border-t pt-4">
      <Button variant="ghost" class="w-full gap-1.5 text-xs h-8 text-muted-foreground hover:text-foreground">
        Lihat Kalender Lengkap
        <ChevronRight class="size-3.5" />
      </Button>
    </CardFooter>
  </Card>
</template>
