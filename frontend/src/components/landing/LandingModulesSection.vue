<script setup>
import { Check, GraduationCap } from 'lucide-vue-next'

const activeSchools = [
  { name: 'TK Cahaya Kecil', siswa: 142, color: 'bg-amber-100 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400' },
  { name: 'SD Cahaya Bangsa 1', siswa: 612, color: 'bg-rose-100 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400' },
  { name: 'SMP Cahaya Bangsa', siswa: 488, color: 'bg-blue-100 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400' },
  { name: 'SMA Cahaya Bangsa', siswa: 524, color: 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' },
  { name: 'Pesantren Al-Cahaya', siswa: 318, color: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400' }
]
</script>

<template>
  <section id="modul" class="relative px-6 py-24">
    <div class="blob left-0 top-1/2 h-100 w-100 bg-lavender" />
    <div class="relative mx-auto grid max-w-6xl gap-10 lg:grid-cols-2 lg:items-center">
      <div>
        <span class="text-sm font-semibold uppercase tracking-wider text-primary"
          >Untuk Yayasan</span
        >
        <h2 class="mt-3 text-4xl font-bold tracking-tight md:text-5xl">
          Satu akun, semua sekolah Anda.
        </h2>
        <p class="mt-4 text-muted-foreground">
          Yayasan dengan banyak unit sekolah (TK, SD, SMP, SMA, pesantren) bisa mengelola seluruh
          operasional dari satu tempat dengan kontrol akses berlapis.
        </p>
        <ul class="mt-6 space-y-3">
          <li
            v-for="item in [
              'Konsolidasi laporan keuangan multi-sekolah',
              'Hak akses bertingkat: yayasan, kepsek, guru, TU',
              'Standarisasi kurikulum & rapor antar unit',
              'Audit trail & keamanan data terenkripsi'
            ]"
            :key="item"
            class="flex items-start gap-3"
          >
            <span
              class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-accent text-accent-foreground"
            >
              <Check class="h-3.5 w-3.5" />
            </span>
            <span class="text-foreground">{{ item }}</span>
          </li>
        </ul>
      </div>

      <div class="relative">
        <div class="glass rounded-3xl p-6">
          <div class="flex items-center justify-between">
            <h4 class="font-semibold">Sekolah Aktif</h4>
            <span class="text-xs text-muted-foreground">12 unit</span>
          </div>
          <div class="mt-5 space-y-3">
            <div
              v-for="school in activeSchools"
              :key="school.name"
              class="flex items-center gap-3 rounded-2xl bg-white/60 p-3"
            >
              <div
                class="flex h-11 w-11 items-center justify-center rounded-xl font-medium"
                :class="school.color"
              >
                <GraduationCap class="h-5 w-5" />
              </div>
              <div class="flex-1">
                <div class="text-sm font-semibold">{{ school.name }}</div>
                <div class="text-xs text-muted-foreground">{{ school.siswa }} siswa aktif</div>
              </div>
              <div
                class="rounded-full bg-accent/20 px-2.5 py-1 text-xs font-medium text-accent-foreground"
              >
                Aktif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
