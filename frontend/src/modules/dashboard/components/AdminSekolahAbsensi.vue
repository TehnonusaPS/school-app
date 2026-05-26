<script setup lang="ts">
import { ClipboardList } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'

const absensi = [
  { nama: 'Ahmad Fauzi',       kelas: 'X MIPA 1',   jam: '07:15', status: 'Hadir' },
  { nama: 'Siti Rahayu',       kelas: 'XI IPS 2',   jam: '07:42', status: 'Terlambat' },
  { nama: 'Budi Santoso',      kelas: 'X MIPA 1',   jam: '07:10', status: 'Hadir' },
  { nama: 'Dewi Lestari',      kelas: 'XII IPA 3',  jam: '07:08', status: 'Hadir' },
  { nama: 'Mukhlis Abidin',    kelas: 'XI MIPA 2',  jam: '—',     status: 'Alpa' },
  { nama: 'Nur Fadilah',       kelas: 'X IPS 1',    jam: '07:20', status: 'Hadir' },
  { nama: 'Agus Prasetyo',     kelas: 'XII IPS 1',  jam: '07:05', status: 'Izin' },
  { nama: 'Ratna Sari',        kelas: 'XI IPA 1',   jam: '07:12', status: 'Hadir' },
  { nama: 'Hendra Wijaya',     kelas: 'X MIPA 3',   jam: '07:51', status: 'Terlambat' },
  { nama: 'Aisyah Putri',      kelas: 'XII IPA 1',  jam: '07:09', status: 'Hadir' },
  { nama: 'Doni Prasetya',     kelas: 'X IPS 2',    jam: '07:18', status: 'Hadir' },
  { nama: 'Lina Marlina',      kelas: 'XI MIPA 1',  jam: '—',     status: 'Alpa' },
  { nama: 'Fajar Nugraha',     kelas: 'XII IPS 2',  jam: '07:25', status: 'Hadir' },
  { nama: 'Yuni Astuti',       kelas: 'X MIPA 2',   jam: '07:07', status: 'Hadir' },
  { nama: 'Rizky Ramadhan',    kelas: 'XI IPS 1',   jam: '07:38', status: 'Terlambat' },
]

const statusVariant = (s: string) =>
  s === 'Hadir'     ? 'default'   :
  s === 'Terlambat' ? 'secondary' :
  s === 'Izin'      ? 'outline'   :
  'destructive'
</script>

<template>
  <Card class="lg:col-span-3">
    <CardHeader class="pb-3">
      <div class="flex items-center gap-2">
        <ClipboardList class="size-4 text-primary" />
        <CardTitle class="text-base font-semibold">Update Absensi Terbaru</CardTitle>
      </div>
      <CardDescription class="text-xs">Rekap absensi siswa hari ini secara real-time</CardDescription>
    </CardHeader>

    <CardContent class="p-0">
      <!-- Fixed header -->
      <table class="w-full text-sm border-b">
        <colgroup>
          <col style="width: 38%" />
          <col style="width: 22%" />
          <col style="width: 16%" />
          <col style="width: 24%" />
        </colgroup>
        <thead>
          <tr class="border-b bg-muted/50">
            <th class="pl-6 py-3 text-left text-xs font-medium text-muted-foreground">Siswa</th>
            <th class="py-3 text-left text-xs font-medium text-muted-foreground">Kelas</th>
            <th class="py-3 text-center text-xs font-medium text-muted-foreground">Jam</th>
            <th class="pr-6 py-3 text-right text-xs font-medium text-muted-foreground">Status</th>
          </tr>
        </thead>
      </table>

      <!-- Scrollable body — diperpanjang agar tidak ada space kosong -->
      <div class="h-[390px] overflow-y-auto">
        <table class="w-full text-sm">
          <colgroup>
            <col style="width: 38%" />
            <col style="width: 22%" />
            <col style="width: 16%" />
            <col style="width: 24%" />
          </colgroup>
          <tbody>
            <tr
              v-for="(a, i) in absensi"
              :key="i"
              class="border-b transition-colors hover:bg-muted/50 cursor-default"
            >
              <td class="pl-6 py-3 font-medium text-sm">{{ a.nama }}</td>
              <td class="py-3 text-muted-foreground text-sm">{{ a.kelas }}</td>
              <td class="py-3 text-center font-mono text-xs text-muted-foreground">{{ a.jam }}</td>
              <td class="pr-6 py-3 text-right">
                <Badge :variant="statusVariant(a.status)" class="text-xs">
                  {{ a.status }}
                </Badge>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardContent>
  </Card>
</template>
