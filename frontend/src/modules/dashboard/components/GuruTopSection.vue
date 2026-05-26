<script setup lang="ts">
import { BookOpen, ClipboardCheck, TrendingUp } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
const nama = auth.user?.name ?? 'Guru'
</script>

<template>
  <div class="grid gap-4 lg:grid-cols-3">
    <!-- Welcome Card -->
    <Card class="transition-all duration-200 hover:shadow-md">
      <CardHeader class="pb-2">
        <CardDescription class="text-sm font-medium">Selamat Pagi 👋</CardDescription>
        <p class="text-xl font-bold tracking-tight">{{ nama }}</p>
      </CardHeader>
      <CardContent class="space-y-3">
        <p class="text-xs text-muted-foreground leading-relaxed">
          Hari ini Anda memiliki <strong class="text-foreground">4 sesi</strong> mengajar
          dan <strong class="text-foreground">12 tugas</strong> siswa yang perlu dinilai.
        </p>
        <Button variant="outline" size="sm" class="h-8 text-xs gap-1.5">
          <BookOpen class="size-3.5" />
          Lihat Jadwal Penuh
        </Button>
      </CardContent>
    </Card>

    <!-- Rata-rata Presensi -->
    <Card class="transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
      <CardHeader class="pb-2">
        <div class="flex items-center justify-between">
          <CardDescription class="text-sm font-medium">Rata-rata Presensi</CardDescription>
          <div class="rounded-lg p-2 bg-emerald-500/10">
            <TrendingUp class="size-4 text-emerald-500" />
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <div class="text-3xl font-bold tracking-tight">94.8%</div>
        <div class="mt-1 flex items-center gap-1.5 text-xs">
          <span class="text-emerald-500 font-semibold">+2.4%</span>
          <span class="text-muted-foreground">dari bulan lalu</span>
        </div>
      </CardContent>
    </Card>

    <!-- Tugas Menunggu -->
    <Card class="transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
      <CardHeader class="pb-2">
        <div class="flex items-center justify-between">
          <CardDescription class="text-sm font-medium">Tugas Menunggu</CardDescription>
          <div class="rounded-lg p-2 bg-amber-500/10">
            <ClipboardCheck class="size-4 text-amber-500" />
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <div class="text-3xl font-bold tracking-tight">12</div>
        <div class="mt-1 text-xs text-muted-foreground">
          Deadline penilaian: <span class="font-semibold text-rose-500">2 hari</span>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
