<script setup>
import AppCard from '@/components/app-card/AppCard.vue'
import { Button } from '@/components/ui/button'
import { GraduationCap, Settings, ArrowRight } from 'lucide-vue-next'
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
    <!-- AppCard Default -->
    <AppCard
      title="Informasi Akademik"
      description="Kelola kurikulum, jadwal pelajaran, dan kalender akademik sekolah."
      :icon="GraduationCap"
    >
      <div class="space-y-4">
        <p class="text-sm text-muted-foreground">
          Modul ini memungkinkan tata usaha dan kepala sekolah untuk mengatur tahun ajaran baru serta pembagian kelas.
        </p>
        <div class="flex items-center justify-between p-3 rounded-lg bg-muted/40 border text-xs">
          <span>Tahun Ajaran Aktif</span>
          <span class="font-semibold text-primary">2026/2027 Ganjil</span>
        </div>
      </div>
      
      <template #action>
        <Button size="icon" variant="ghost" class="rounded-full">
          <Settings class="h-4 w-4" />
        </Button>
      </template>

      <template #footer>
        <Button class="w-full justify-between" variant="outline">
          Buka Modul Akademik
          <ArrowRight class="h-4 w-4" />
        </Button>
      </template>
    </AppCard>

    <!-- AppCard Custom Layout (Using Slots) -->
    <AppCard cardClass="border-primary/20 bg-primary/5">
      <template #header>
        <div class="flex items-start justify-between">
          <div>
            <span class="text-[10px] bg-primary/10 text-primary font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Pemberitahuan</span>
            <h3 class="text-lg font-bold text-foreground mt-1">Sistem Pemeliharaan</h3>
          </div>
          <span class="text-xs text-muted-foreground">2 jam yang lalu</span>
        </div>
      </template>

      <div class="space-y-3">
        <p class="text-sm text-foreground">
          Sistem absensi online akan mengalami pemeliharaan rutin pada hari Sabtu mulai pukul 22:00 WIB hingga Minggu pukul 04:00 WIB.
        </p>
        <p class="text-xs text-muted-foreground italic">
          * Selama pemeliharaan berlangsung, aplikasi mobile absensi wali kelas tidak dapat diakses sementara waktu.
        </p>
      </div>

      <template #footer>
        <div class="flex justify-end gap-2 w-full">
          <Button size="sm" variant="ghost">Abaikan</Button>
          <Button size="sm">Pelajari Lebih Lanjut</Button>
        </div>
      </template>
    </AppCard>
  </div>
</template>
