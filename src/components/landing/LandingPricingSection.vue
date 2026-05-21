<script setup>
import { Check, ShieldCheck } from 'lucide-vue-next'

const plans = [
  {
    name: 'Starter',
    price: 'Rp 1,5jt',
    desc: 'Untuk 1 sekolah, hingga 300 siswa.',
    features: ['Akademik & Rapor', 'Absensi digital', 'Komunikasi orang tua', 'Support email'],
    color: 'bg-secondary',
    featured: false
  },
  {
    name: 'Yayasan',
    price: 'Rp 4,9jt',
    desc: 'Multi-sekolah, hingga 2.000 siswa.',
    features: [
      'Semua di Starter',
      'Dashboard yayasan',
      'Keuangan multi-unit',
      'PPDB online',
      'Support prioritas'
    ],
    color: 'bg-primary',
    featured: true
  },
  {
    name: 'Enterprise',
    price: 'Custom',
    desc: 'Yayasan besar, kustomisasi penuh.',
    features: ['Semua di Yayasan', 'Single Sign-On', 'Integrasi kustom', 'Dedicated manager'],
    color: 'bg-lavender',
    featured: false
  }
]
</script>

<template>
  <section id="harga" class="relative px-6 py-24">
    <div class="mx-auto max-w-6xl">
      <div class="mx-auto max-w-2xl text-center">
        <span class="text-sm font-semibold uppercase tracking-wider text-primary">Harga</span>
        <h2 class="mt-3 text-4xl font-bold tracking-tight md:text-5xl">
          Transparan, tumbuh bersama yayasan Anda
        </h2>
        <p class="mt-4 text-muted-foreground">
          Per bulan, dibayar tahunan. Tanpa biaya tersembunyi.
        </p>
      </div>

      <div class="mt-14 grid gap-5 md:grid-cols-3">
        <div
          v-for="plan in plans"
          :key="plan.name"
          class="relative rounded-3xl p-7"
          :class="
            plan.featured
              ? 'glass-dark text-background ring-2 ring-primary/40 shadow-2xl shadow-primary/20'
              : 'glass'
          "
        >
          <span
            v-if="plan.featured"
            class="absolute -top-3 left-7 rounded-full bg-secondary px-3 py-1 text-xs font-bold text-secondary-foreground"
          >
            Paling Populer
          </span>
          <div
            class="mb-5 inline-flex h-10 w-10 items-center justify-center rounded-xl text-white"
            :class="plan.color"
          >
            <ShieldCheck class="h-5 w-5" />
          </div>
          <h3 class="text-xl font-bold">{{ plan.name }}</h3>
          <p
            class="mt-1 text-sm"
            :class="plan.featured ? 'text-background/70' : 'text-muted-foreground'"
          >
            {{ plan.desc }}
          </p>
          <div class="mt-5 flex items-baseline gap-1">
            <span class="text-4xl font-bold">{{ plan.price }}</span>
            <span
              class="text-sm"
              :class="plan.featured ? 'text-background/60' : 'text-muted-foreground'"
              >/bulan</span
            >
          </div>
          <ul class="mt-6 space-y-3 text-sm">
            <li v-for="feature in plan.features" :key="feature" class="flex items-start gap-2">
              <Check
                class="mt-0.5 h-4 w-4 shrink-0"
                :class="plan.featured ? 'text-secondary' : 'text-primary'"
              />
              {{ feature }}
            </li>
          </ul>
          <button
            class="mt-7 w-full rounded-full px-5 py-3 text-sm font-semibold transition"
            :class="
              plan.featured
                ? 'bg-secondary text-secondary-foreground hover:opacity-90'
                : 'bg-foreground text-background hover:opacity-90'
            "
            type="button"
          >
            Pilih {{ plan.name }}
          </button>
        </div>
      </div>
    </div>
  </section>
</template>
