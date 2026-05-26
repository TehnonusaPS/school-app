<script setup>
import { ref } from 'vue'
import { School, Menu, X } from 'lucide-vue-next'

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

const isMobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}
</script>

<template>
  <header 
    class="fixed top-0 left-0 right-0 z-50 w-full border-b border-border/40 bg-background/80 backdrop-blur-md transition-all duration-300 overflow-hidden"
  >
    <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">
      <div class="flex h-14 sm:h-16 items-center justify-between gap-2">
        <!-- Logo -->
        <router-link to="/" class="flex items-center gap-2 min-w-0 shrink" @click="closeMobileMenu">
          <div
            class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm shrink-0"
          >
            <School class="h-4 w-4 sm:h-5 sm:w-5" />
          </div>
          <span class="font-display text-base sm:text-lg font-bold tracking-tight text-foreground truncate">{{ brandName }}</span>
        </router-link>

        <!-- Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-muted-foreground">
          <a
            v-for="link in links"
            :key="link.label"
            :href="link.href"
            class="transition hover:text-foreground relative py-2"
          >
            {{ link.label }}
          </a>
        </nav>

        <!-- Actions -->
        <div class="flex items-center gap-2 shrink-0">
          <!-- "Masuk" Button - Always visible -->
          <router-link
            :to="loginPath"
            class="rounded-full bg-secondary px-3.5 sm:px-5 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-secondary-foreground shadow-sm hover:bg-secondary/90 transition duration-200 whitespace-nowrap"
            @click="closeMobileMenu"
          >
            Masuk
          </router-link>

          <!-- Mobile Toggle Button -->
          <button
            class="inline-flex md:hidden h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-lg border border-border/40 hover:bg-muted/50 transition focus:outline-none shrink-0"
            aria-label="Toggle Menu"
            @click="toggleMobileMenu"
          >
            <component :is="isMobileMenuOpen ? X : Menu" class="h-5 w-5 text-foreground" />
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="-translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="-translate-y-2 opacity-0"
    >
      <div
        v-if="isMobileMenuOpen"
        class="md:hidden border-t border-border/20 bg-background/95 backdrop-blur-lg px-4 py-3 shadow-lg"
      >
        <div class="space-y-1">
          <a
            v-for="link in links"
            :key="link.label"
            :href="link.href"
            class="block rounded-lg px-3 py-2.5 text-base font-medium text-muted-foreground hover:bg-primary/5 hover:text-primary transition"
            @click="closeMobileMenu"
          >
            {{ link.label }}
          </a>
        </div>
      </div>
    </transition>
  </header>
</template>
