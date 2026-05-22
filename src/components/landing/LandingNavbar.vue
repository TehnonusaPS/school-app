<script setup>
import { School } from 'lucide-vue-next'

defineProps({
  brandName: {
    type: String,
    default: 'Sekolahku'
  },
  loginPath: {
    type: String,
    default: '/login'
  },
  links: {
    type: Array,
    default: () => [
      { label: 'Fitur', href: '#fitur' },
      { label: 'Modul', href: '#modul' },
      { label: 'Harga', href: '#harga' },
      { label: 'Testimoni', href: '#testimoni' },
      { label: 'Hubungi Kami', href: '#kontak' }
    ]
  }
})
</script>

<template>
  <header class="fixed inset-x-0 top-4 z-50 px-4">
    <nav
      class="glass mx-auto flex max-w-6xl items-center justify-between rounded-full px-3 py-2.5 pl-5"
    >
      <router-link to="/" class="flex items-center gap-2">
        <div
          class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-primary-foreground"
        >
          <School class="h-5 w-5" />
        </div>
        <span class="font-display text-lg font-bold tracking-tight">{{ brandName }}</span>
      </router-link>
      <div class="hidden items-center gap-8 text-sm font-medium text-muted-foreground md:flex">
        <a
          v-for="link in links"
          :key="link.label"
          :href="link.href"
          class="transition hover:text-foreground"
        >
          {{ link.label }}
        </a>
      </div>
      <div class="flex items-center gap-2">
        <router-link
          :to="loginPath"
          class="hidden rounded-full bg-secondary px-4 py-2 text-sm font-medium text-secondary-foreground transition hover:bg-secondary/90 sm:block"
        >
          Masuk
        </router-link>
      </div>
    </nav>
  </header>
</template>
