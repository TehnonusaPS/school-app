<script setup>
import { ArrowRight, Building2, MessageCircle, School, Sparkles } from 'lucide-vue-next'

const stats = [
  { label: 'Total Siswa', value: '4.218', color: 'bg-primary' },
  { label: 'Sekolah Aktif', value: '12', color: 'bg-accent' },
  { label: 'Pemasukan Bulan Ini', value: 'Rp 1.8M', color: 'bg-secondary' },
  { label: 'Kehadiran Hari Ini', value: '96.4%', color: 'bg-lavender' }
]

const yayasan = [
  'Yayasan Cahaya Bangsa',
  'Al-Hikmah Foundation',
  'Tunas Mulia',
  'Pelita Harapan Group',
  'Yayasan Nurul Iman',
  'Bina Insan Mandiri',
  'Yayasan Bakti Pertiwi',
  'Cendekia Muda'
]

const marqueeItems = [...yayasan, ...yayasan]
</script>

<template>
  <section class="relative overflow-hidden pb-24 pt-36">
    <div class="blob -left-20 -top-20 h-105 w-105 bg-primary" />
    <div class="blob right-0 top-40 h-95 w-95 bg-accent" />
    <div class="blob bottom-0 left-1/3 h-80 w-80 bg-secondary" />

    <div class="relative mx-auto max-w-6xl px-6 text-center">
      <div
        class="mx-auto inline-flex items-center gap-2 rounded-full bg-secondary/15 px-4 py-1.5 text-xs font-medium text-secondary-foreground ring-1 ring-secondary/20"
      >
        <Sparkles class="h-3.5 w-3.5 text-primary" />
        Platform ERP #1 untuk Yayasan Pendidikan
      </div>
      <h1
        class="mx-auto mt-6 max-w-4xl text-5xl font-bold leading-[1.05] tracking-tight text-foreground md:text-7xl"
      >
        Kelola seluruh sekolah yayasan Anda dari
        <span class="relative inline-block">
          <span class="relative z-10">satu dashboard</span>
          <span class="absolute inset-x-0 bottom-2 z-0 h-3 rounded-full bg-secondary/70" />
        </span>
        .
      </h1>
      <p class="mx-auto mt-6 max-w-2xl text-lg text-muted-foreground md:text-xl">
        Sekolahku menyatukan akademik, keuangan, kesiswaan, dan komunikasi orang tua untuk semua
        unit sekolah di bawah yayasan Anda - modern, aman, dan mudah dipakai guru.
      </p>

      <div class="mt-28 space-y-4">
        <p class="text-center text-xs uppercase tracking-[0.25em] text-muted-foreground">
          Dipercaya oleh yayasan di Indonesia
        </p>
        <div class="marquee-mask relative mt-6 overflow-hidden">
          <div class="animate-marquee flex w-max gap-10 whitespace-nowrap">
            <div
              v-for="(name, index) in marqueeItems"
              :key="`r-${index}`"
              class="flex shrink-0 items-center gap-2.5 text-foreground/70"
            >
              <span
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <Building2 class="h-4 w-4" />
              </span>
              <span class="text-sm font-semibold tracking-tight">{{ name }}</span>
            </div>
          </div>
        </div>
        <div class="marquee-mask relative mt-3 overflow-hidden">
          <div class="animate-marquee-reverse flex w-max gap-10 whitespace-nowrap">
            <div
              v-for="(name, index) in marqueeItems"
              :key="`l-${index}`"
              class="flex shrink-0 items-center gap-2.5 text-foreground/70"
            >
              <span
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <Building2 class="h-4 w-4" />
              </span>
              <span class="text-sm font-semibold tracking-tight">{{ name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
