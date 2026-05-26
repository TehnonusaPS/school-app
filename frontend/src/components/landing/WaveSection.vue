<script setup>
import s1 from '@/assets/images/students-1.jpg'
import s2 from '@/assets/images/students-2.jpg'
import s3 from '@/assets/images/students-3.jpg'
import s4 from '@/assets/images/students-4.jpg'
import s5 from '@/assets/images/students-5.jpg'
import { Sparkles } from 'lucide-vue-next'

const slides = [
  { src: s1, label: 'Belajar Bersama' },
  { src: s2, label: 'Literasi Aktif' },
  { src: s3, label: 'Kolaborasi Digital' },
  { src: s4, label: 'Tumbuh Ceria' },
  { src: s5, label: 'Antusias Berkarya' }
]

const items = [...slides, ...slides]

const yellowList = [
  'PPDB online tanpa antre',
  'Rapor digital instan',
  'SPP otomatis & transparan',
  'Notifikasi orang tua real-time',
  'Dashboard yayasan multi-sekolah'
]
</script>

<template>
  <section class="relative w-full">
    <!-- Wave top SVG -->
    <svg
      viewBox="0 0 1440 80"
      preserveAspectRatio="none"
      class="block h-12 w-full"
      aria-hidden="true"
    >
      <path
        d="M0,40 C120,80 240,0 360,40 C480,80 600,0 720,40 C840,80 960,0 1080,40 C1200,80 1320,0 1440,40 L1440,80 L0,80 Z"
        fill="oklch(0.92 0.13 95)"
      />
    </svg>

    <div class="bg-[oklch(0.92_0.13_95)] pb-20 pt-10">
      <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid items-center gap-10 lg:grid-cols-2">

          <!-- Left: Text content -->
          <div class="w-full min-w-0">
            <span
              class="inline-flex items-center gap-2 rounded-full bg-foreground/10 px-3 py-1 text-xs font-semibold text-foreground"
            >
              <Sparkles class="h-3.5 w-3.5 shrink-0" /> Untuk siswa &amp; guru
            </span>

            <h2 class="mt-4 text-2xl font-bold tracking-tight text-foreground break-words sm:text-4xl md:text-5xl">
              Pengalaman sekolah yang menyenangkan, dari hari pertama.
            </h2>

            <p class="mt-4 text-sm leading-relaxed text-foreground/70 sm:text-base">
              Sekolahku dirancang agar guru fokus mengajar dan orang tua tenang memantau,
              sementara yayasan punya kontrol penuh.
            </p>

            <!-- Wave list items — wave shape via CSS mask on desktop -->
            <ul class="mt-7 space-y-3">
              <li
                v-for="(item, index) in yellowList"
                :key="item"
                class="wave-item flex items-center gap-3 bg-background/70 px-4 py-3 text-foreground shadow-sm sm:px-5"
              >
                <span
                  class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground sm:h-8 sm:w-8 sm:text-sm"
                >
                  {{ index + 1 }}
                </span>
                <span class="min-w-0 break-words text-sm font-medium sm:text-base">{{ item }}</span>
              </li>
            </ul>
          </div>

          <!-- Right: Carousel -->
          <div class="relative w-full">
            <!-- SVG clip def for wave-shaped cards -->
            <svg width="0" height="0" class="absolute" aria-hidden="true">
              <defs>
                <clipPath id="waveCard" clipPathUnits="objectBoundingBox">
                  <path
                    d="M0,0.08 C0.1,0.0 0.25,0.16 0.4,0.06 C0.55,-0.04 0.75,0.14 1,0.04 L1,0.92 C0.88,1.02 0.7,0.86 0.55,0.96 C0.35,1.06 0.18,0.88 0,0.96 Z"
                  />
                </clipPath>
              </defs>
            </svg>

            <div class="relative overflow-hidden rounded-3xl bg-foreground/10 p-3">
              <div class="marquee-mask overflow-hidden">
                <div class="animate-carousel flex w-max gap-5">
                  <figure
                    v-for="(slide, i) in items"
                    :key="i"
                    class="relative h-[280px] w-[220px] shrink-0 sm:h-[320px] sm:w-[260px]"
                  >
                    <div
                      class="absolute inset-0 bg-primary"
                      style="clip-path: url(#waveCard)"
                    />
                    <div
                      class="absolute inset-[6px] overflow-hidden"
                      style="clip-path: url(#waveCard)"
                    >
                      <img
                        :src="slide.src"
                        :alt="slide.label"
                        loading="lazy"
                        width="800"
                        height="800"
                        class="h-full w-full object-cover"
                      />
                    </div>
                    <figcaption
                      class="absolute inset-x-4 bottom-6 z-10 rounded-full bg-background/85 px-3 py-1.5 text-center text-xs font-semibold text-foreground"
                    >
                      {{ slide.label }}
                    </figcaption>
                  </figure>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Wave bottom SVG -->
    <div class="-mt-px">
      <svg
        viewBox="0 0 1440 80"
        preserveAspectRatio="none"
        class="block h-12 w-full"
        aria-hidden="true"
      >
        <path
          d="M0,40 C120,80 240,0 360,40 C480,80 600,0 720,40 C840,80 960,0 1080,40 C1200,80 1320,0 1440,40 L1440,0 L0,0 Z"
          fill="oklch(0.92 0.13 95)"
        />
      </svg>
    </div>
  </section>
</template>

<style scoped>
/*
  Wave list items:
  - Mobile (default): simple rounded rectangle
  - sm+ (640px): scalloped / wave shape using clip-path polygon
    The polygon has 5% inset on top and bottom so text never clips
*/
.wave-item {
  border-radius: 0.5rem;
  position: relative;
}

@media (min-width: 640px) {
  .wave-item {
    border-radius: 0;
    clip-path: polygon(
      0% 20%,   3% 5%,   6% 20%,  9% 5%,  12% 20%, 15% 5%,  18% 20%, 21% 5%,
      24% 20%, 27% 5%,  30% 20%, 33% 5%,  36% 20%, 39% 5%,  42% 20%, 45% 5%,
      48% 20%, 51% 5%,  54% 20%, 57% 5%,  60% 20%, 63% 5%,  66% 20%, 69% 5%,
      72% 20%, 75% 5%,  78% 20%, 81% 5%,  84% 20%, 87% 5%,  90% 20%, 93% 5%,
      96% 20%, 99% 5%,  100% 10%,
      100% 80%, 99% 95%, 96% 80%, 93% 95%, 90% 80%, 87% 95%, 84% 80%, 81% 95%,
      78% 80%, 75% 95%, 72% 80%, 69% 95%, 66% 80%, 63% 95%, 60% 80%, 57% 95%,
      54% 80%, 51% 95%, 48% 80%, 45% 95%, 42% 80%, 39% 95%, 36% 80%, 33% 95%,
      30% 80%, 27% 95%, 24% 80%, 21% 95%, 18% 80%, 15% 95%, 12% 80%,  9% 95%,
       6% 80%,  3% 95%,  0% 80%
    );
    /* Add vertical padding so text stays within the safe (non-clipped) zone */
    padding-top: 1rem;
    padding-bottom: 1rem;
  }
}
</style>
