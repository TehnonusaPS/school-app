<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Progress } from '@/components/ui/progress'

const siswaHadir = { hadir: 400, total: 402, hadir_pct: 99, alpa_pct: 0, izin_pct: 1 }
const guruHadir  = { hadir: 30,  total: 102, hadir_pct: 30, alpa_pct: 20, izin_pct: 50 }
</script>

<template>
  <Card class="lg:col-span-3">
    <CardHeader class="pb-2">
      <CardTitle class="text-base font-semibold">Kehadiran Hari Ini</CardTitle>
      <CardDescription class="text-xs">
        Rekap kehadiran siswa, guru, dan staff
      </CardDescription>
    </CardHeader>

    <CardContent class="space-y-5">
      <!-- Siswa -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <span class="text-sm font-semibold">Siswa (Hadir/Total)</span>
          <span class="text-sm font-bold tabular-nums">{{ siswaHadir.hadir }}/{{ siswaHadir.total }}</span>
        </div>
        <Progress :model-value="siswaHadir.hadir_pct" class="h-3" />
        <div class="flex items-center gap-4 text-xs text-muted-foreground">
          <span class="flex items-center gap-1">
            <span class="inline-block size-2 rounded-full bg-emerald-500"></span>
            Hadir ({{ siswaHadir.hadir_pct }}%)
          </span>
          <span class="flex items-center gap-1">
            <span class="inline-block size-2 rounded-full bg-rose-500"></span>
            Alpa ({{ siswaHadir.alpa_pct }}%)
          </span>
          <span class="flex items-center gap-1">
            <span class="inline-block size-2 rounded-full bg-amber-400"></span>
            Izin ({{ siswaHadir.izin_pct }}%)
          </span>
        </div>
      </div>

      <div class="border-t" />

      <!-- Guru & Staff -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <span class="text-sm font-semibold">Staff & Guru</span>
          <span class="text-sm font-bold tabular-nums">{{ guruHadir.hadir }}/{{ guruHadir.total }}</span>
        </div>
        <Progress :model-value="guruHadir.hadir_pct" class="h-3" />
        <div class="flex items-center gap-4 text-xs text-muted-foreground">
          <span class="flex items-center gap-1">
            <span class="inline-block size-2 rounded-full bg-emerald-500"></span>
            Hadir ({{ guruHadir.hadir_pct }}%)
          </span>
          <span class="flex items-center gap-1">
            <span class="inline-block size-2 rounded-full bg-rose-500"></span>
            Alpa ({{ guruHadir.alpa_pct }}%)
          </span>
          <span class="flex items-center gap-1">
            <span class="inline-block size-2 rounded-full bg-amber-400"></span>
            Izin ({{ guruHadir.izin_pct }}%)
          </span>
        </div>
      </div>

      <div class="border-t" />

      <!-- Ringkasan -->
      <div>
        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">Ringkasan Hari Ini</p>
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-lg bg-muted/50 p-3 space-y-0.5">
            <p class="text-[10px] text-muted-foreground">Hadir Siswa</p>
            <p class="text-sm font-bold text-emerald-500">400</p>
          </div>
          <div class="rounded-lg bg-muted/50 p-3 space-y-0.5">
            <p class="text-[10px] text-muted-foreground">Tidak Hadir Siswa</p>
            <p class="text-sm font-bold text-rose-500">2</p>
          </div>
          <div class="rounded-lg bg-muted/50 p-3 space-y-0.5">
            <p class="text-[10px] text-muted-foreground">Hadir Guru & Staff</p>
            <p class="text-sm font-bold text-emerald-500">30</p>
          </div>
          <div class="rounded-lg bg-muted/50 p-3 space-y-0.5">
            <p class="text-[10px] text-muted-foreground">Tidak Hadir Guru & Staff</p>
            <p class="text-sm font-bold text-rose-500">72</p>
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
