<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
</script>

<template>
  <div class="rounded-xl border bg-card text-card-foreground shadow overflow-hidden flex min-h-[210px]">
    <!-- Info sisi kiri — primary gradient -->
    <div class="flex flex-col justify-between p-6 bg-gradient-to-br from-primary via-primary/90 to-primary/80 text-primary-foreground w-[60%] shrink-0">
      <div class="space-y-1">
        <Badge class="text-[10px] mb-2 w-fit bg-primary-foreground/20 text-primary-foreground border-primary-foreground/30 hover:bg-primary-foreground/30">
          Siswa
        </Badge>
        <h2 class="text-xl font-bold leading-tight">Aditya Pratama</h2>
        <p class="text-sm text-primary-foreground/80 mt-0.5">Kelas 10 - IPA 2</p>
        <p class="text-xs text-primary-foreground/60">NISN: 0092837411</p>
      </div>

      <!-- Mini stats -->
      <div class="flex gap-3 mt-5">
        <div class="rounded-lg border border-primary-foreground/25 bg-primary-foreground/10 px-4 py-3 text-left flex-1">
          <p class="text-2xl font-bold">8.8</p>
          <p class="text-xs text-primary-foreground/70 mt-1">Rata-rata</p>
        </div>
        <div class="rounded-lg border border-primary-foreground/25 bg-primary-foreground/10 px-4 py-3 text-left flex-1">
          <p class="text-2xl font-bold">95%</p>
          <p class="text-xs text-primary-foreground/70 mt-1">Kehadiran</p>
        </div>
      </div>
    </div>

    <!-- Foto siswa sisi kanan -->
    <div class="relative flex-1 overflow-hidden">
      <img
        src="/images/student-dummy.png"
        alt="Foto Siswa"
        class="absolute inset-0 w-full h-full object-cover object-top"
      />
      <div class="absolute inset-0 bg-gradient-to-r from-primary/40 via-transparent to-transparent pointer-events-none" />
    </div>
  </div>
</template>
