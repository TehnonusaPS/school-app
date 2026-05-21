<script setup>
import { Star } from 'lucide-vue-next'

const testimonials = [
  {
    quote: 'Laporan keuangan 8 sekolah kami sekarang siap dalam hitungan menit, bukan minggu.',
    name: 'Bapak Hasan',
    role: 'Ketua Yayasan Cahaya Bangsa',
    color: 'bg-primary'
  },
  {
    quote:
      'Orang tua jadi lebih aktif memantau anak. Tagihan SPP juga lancar tanpa diingatkan manual.',
    name: 'Ibu Rina',
    role: 'Kepala Sekolah SD Tunas Mulia',
    color: 'bg-coral'
  },
  {
    quote:
      'Migrasi data lama kami dibantu sampai selesai. Guru-guru langsung bisa pakai di hari pertama.',
    name: 'Pak Adi',
    role: 'IT Yayasan Pelita Harapan',
    color: 'bg-accent'
  }
]
</script>

<template>
  <section id="testimoni" class="relative px-6 py-24">
    <div class="mx-auto max-w-6xl">
      <div class="mx-auto max-w-2xl text-center">
        <span class="text-sm font-semibold uppercase tracking-wider text-primary">Testimoni</span>
        <h2 class="mt-3 text-4xl font-bold tracking-tight md:text-5xl">
          Dipercaya yayasan di seluruh Indonesia
        </h2>
      </div>
      <div class="mt-14 grid gap-5 md:grid-cols-3">
        <div v-for="item in testimonials" :key="item.name" class="glass rounded-3xl p-6">
          <div class="flex gap-1 text-secondary">
            <Star v-for="index in 5" :key="index" class="h-4 w-4 fill-secondary" />
          </div>
          <p class="mt-4 text-foreground">"{{ item.quote }}"</p>
          <div class="mt-6 flex items-center gap-3">
            <div
              class="flex h-11 w-11 items-center justify-center rounded-full text-white font-semibold"
              :class="item.color"
            >
              {{ item.name.split(' ').slice(-1)[0][0] }}
            </div>
            <div>
              <div class="text-sm font-semibold">{{ item.name }}</div>
              <div class="text-xs text-muted-foreground">{{ item.role }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
