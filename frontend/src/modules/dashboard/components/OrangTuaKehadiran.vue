<script setup lang="ts">
import { Download, ClipboardList } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'

const log = [
  { tanggal: 'Hari Ini',     jam: '07:12 WIB', keterangan: 'Masuk Sekolah', status: 'Tepat Waktu' },
  { tanggal: 'Kemarin',      jam: '07:08 WIB', keterangan: 'Masuk Sekolah', status: 'Tepat Waktu' },
  { tanggal: '09 Okt 2026',  jam: '07:45 WIB', keterangan: 'Masuk Sekolah', status: 'Terlambat'   },
  { tanggal: '08 Okt 2026',  jam: '07:10 WIB', keterangan: 'Masuk Sekolah', status: 'Tepat Waktu' },
  { tanggal: '07 Okt 2026',  jam: '—',          keterangan: 'Tidak Hadir',   status: 'Izin'        },
  { tanggal: '06 Okt 2026',  jam: '07:05 WIB', keterangan: 'Masuk Sekolah', status: 'Tepat Waktu' },
  { tanggal: '05 Okt 2026',  jam: '07:30 WIB', keterangan: 'Masuk Sekolah', status: 'Terlambat'   },
  { tanggal: '04 Okt 2026',  jam: '07:09 WIB', keterangan: 'Masuk Sekolah', status: 'Tepat Waktu' },
  { tanggal: '03 Okt 2026',  jam: '07:12 WIB', keterangan: 'Masuk Sekolah', status: 'Tepat Waktu' },
  { tanggal: '02 Okt 2026',  jam: '—',          keterangan: 'Tidak Hadir',   status: 'Alpa'        },
]

const badgeVariant = (s: string) =>
  s === 'Tepat Waktu' ? 'default'   :
  s === 'Terlambat'   ? 'secondary' :
  s === 'Izin'        ? 'outline'   : 'destructive'
</script>

<template>
  <Card class="flex flex-col">
    <CardHeader class="pb-3">
      <div class="flex items-center gap-2">
        <ClipboardList class="size-4 text-primary" />
        <CardTitle class="text-base font-semibold">Log Kehadiran</CardTitle>
      </div>
      <CardDescription class="text-xs">Rekap kehadiran siswa beberapa hari terakhir</CardDescription>
    </CardHeader>

    <!-- Fixed header row -->
    <div class="border-b border-t bg-muted/50 px-6 py-2 grid grid-cols-3 text-xs font-medium text-muted-foreground">
      <span>Tanggal</span>
      <span>Jam & Keterangan</span>
      <span class="text-right">Status</span>
    </div>

    <!-- Scrollable content -->
    <div class="overflow-y-auto max-h-[300px] divide-y">
      <div
        v-for="(item, i) in log"
        :key="i"
        class="grid grid-cols-3 items-center px-6 py-3 hover:bg-muted/30 transition-colors"
      >
        <p class="text-sm font-semibold">{{ item.tanggal }}</p>
        <p class="text-xs font-medium text-primary">{{ item.jam }}<br/><span class="text-muted-foreground font-normal">{{ item.keterangan }}</span></p>
        <div class="flex justify-end">
          <Badge :variant="badgeVariant(item.status)" class="text-xs">
            {{ item.status }}
          </Badge>
        </div>
      </div>
    </div>

    <CardFooter class="border-t pt-4 mt-auto">
      <Button variant="outline" class="w-full gap-2 text-sm">
        <Download class="size-4" />
        Download Rekap
      </Button>
    </CardFooter>
  </Card>
</template>
