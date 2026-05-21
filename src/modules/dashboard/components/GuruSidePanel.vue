<script setup lang="ts">
import { Users, MessageCircle } from 'lucide-vue-next'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle
} from '@/components/ui/card'

const kelas = [
  { nama: '10-A', siswa: 32, aktif: 30 },
  { nama: '11-B', siswa: 28, aktif: 28 },
  { nama: '10-B', siswa: 30, aktif: 27 },
  { nama: '12-A', siswa: 26, aktif: 25 }
]

const pesan = [
  {
    nama: 'Bapak Udin (Wali Kelas 10-A)',
    isi: 'Pak, izin konfirmasi terkait tugas Matematika...',
    waktu: '10 menit lalu',
    inisial: 'BU',
    warna: 'bg-blue-500'
  },
  {
    nama: 'Ibu Renata (Wali Kelas 11-B)',
    isi: 'Pak, izin konfirmasi terkait tugas Aljabar...',
    waktu: '25 menit lalu',
    inisial: 'IR',
    warna: 'bg-violet-500'
  },
  {
    nama: 'Ahmad Fauzi (Siswa 10-A)',
    isi: 'Pak, boleh tanya soal nomor 5 ulangan kemarin?',
    waktu: '1 jam lalu',
    inisial: 'AF',
    warna: 'bg-emerald-500'
  }
]
</script>

<template>
  <div class="lg:col-span-2 flex flex-col gap-4">
    <!-- Kelas Saya -->
    <Card>
      <CardHeader class="pb-3">
        <div class="flex items-center gap-2">
          <Users class="size-4 text-primary" />
          <CardTitle class="text-base font-semibold">Kelas Saya</CardTitle>
        </div>
        <CardDescription class="text-xs">{{ kelas.length }} kelas yang Anda ampu</CardDescription>
      </CardHeader>
      <CardContent>
        <div class="grid grid-cols-2 gap-3">
          <div
            v-for="k in kelas"
            :key="k.nama"
            class="rounded-xl border p-3 space-y-2 hover:bg-muted/40 transition-colors cursor-default"
          >
            <p class="text-lg font-bold">{{ k.nama }}</p>
            <div class="flex items-center justify-between text-xs text-muted-foreground">
              <span>{{ k.siswa }} Siswa</span>
              <span class="text-emerald-500 font-semibold">{{ k.aktif }} hadir</span>
            </div>
          </div>
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
        <CardDescription class="text-xs">Pesan terbaru yang masuk</CardDescription>
      </CardHeader>
      <CardContent class="space-y-3">
        <div
          v-for="(p, i) in pesan"
          :key="i"
          class="flex items-start gap-3 rounded-lg p-2 hover:bg-muted/40 transition-colors cursor-default"
        >
          <!-- Avatar inisial -->
          <div :class="['size-9 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0', p.warna]">
            {{ p.inisial }}
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold leading-snug truncate">{{ p.nama }}</p>
            <p class="text-xs text-muted-foreground truncate mt-0.5">{{ p.isi }}</p>
            <p class="text-[10px] text-muted-foreground/70 mt-0.5">{{ p.waktu }}</p>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
