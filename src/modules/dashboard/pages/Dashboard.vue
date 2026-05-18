<template>
  <div class="space-y-6">
    <!-- Welcome Header -->
    <div class="border-b pb-4">
      <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
      <p class="text-muted-foreground mt-1">
        Selamat datang!! Berikut adalah ringkasan aktivitas aplikasi sekolah.
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl border bg-card p-6 shadow-sm">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-muted-foreground">Total Siswa</span>
          <Users class="size-4 text-muted-foreground" />
        </div>
        <div class="text-2xl font-bold">342</div>
        <p class="text-xs text-muted-foreground mt-1">Aktif tahun ajaran ini</p>
      </div>

      <div class="rounded-xl border bg-card p-6 shadow-sm">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-muted-foreground">Total Guru</span>
          <GraduationCap class="size-4 text-muted-foreground" />
        </div>
        <div class="text-2xl font-bold">28</div>
        <p class="text-xs text-muted-foreground mt-1">Guru & tenaga pendidik</p>
      </div>

      <div class="rounded-xl border bg-card p-6 shadow-sm">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-muted-foreground">Pembayaran SPP</span>
          <Wallet class="size-4 text-muted-foreground" />
        </div>
        <div class="text-2xl font-bold">87%</div>
        <p class="text-xs text-muted-foreground mt-1">Lunas bulan ini</p>
      </div>

      <div class="rounded-xl border bg-card p-6 shadow-sm">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-muted-foreground">Kehadiran Hari Ini</span>
          <ClipboardList class="size-4 text-muted-foreground" />
        </div>
        <div class="text-2xl font-bold">95%</div>
        <p class="text-xs text-muted-foreground mt-1">Dari total siswa aktif</p>
      </div>
    </div>

    <!-- Info placeholder -->
    <div
      class="rounded-xl border border-dashed border-muted-foreground/30 bg-muted/30 p-8 text-center"
    >
      <p class="text-sm text-muted-foreground">
        Grafik dan data detail akan ditampilkan di sini setelah koneksi ke API tersedia.
      </p>
    </div>
  </div>
</template>

<script setup>
import { Users, GraduationCap, Wallet, ClipboardList } from 'lucide-vue-next'
</script>
