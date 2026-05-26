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
  <header class="fixed inset-x-0 top-4 z-50 px-4">
    <div class="mx-auto max-w-6xl">
      <nav
        class="glass flex items-center justify-between rounded-full px-3 py-2.5 pl-5 transition-all duration-300"
        :class="{ 'rounded-[24px]': isMobileMenuOpen }"
      >
        <router-link to="/" class="flex items-center gap-2" @click="closeMobileMenu">
          <div
            class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-primary-foreground"
          >
            <School class="h-5 w-5" />
          </div>
          <span class="font-display text-lg font-bold tracking-tight">{{ brandName }}</span>
        </router-link>

        <!-- Desktop Links -->
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

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
          <!-- Login Button - Always visible -->
          <router-link
            :to="loginPath"
            class="rounded-full bg-secondary px-4 py-2 text-sm font-semibold text-secondary-foreground transition hover:bg-secondary/90 shadow-sm"
            @click="closeMobileMenu"
          >
            Masuk
          </router-link>

          <!-- Mobile Menu Button -->
          <button
            class="flex h-9 w-9 items-center justify-center rounded-full border border-border/40 hover:bg-muted/50 transition md:hidden focus:outline-none"
            aria-label="Toggle Menu"
            @click="toggleMobileMenu"
          >
            <component :is="isMobileMenuOpen ? X : Menu" class="h-5 w-5 text-foreground" />
          </button>
        </div>
      </nav>

      <!-- Mobile Dropdown Menu -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="transform scale-95 opacity-0 -translate-y-4"
        enter-to-class="transform scale-100 opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="transform scale-100 opacity-100 translate-y-0"
        leave-to-class="transform scale-95 opacity-0 -translate-y-4"
      >
        <div
          v-if="isMobileMenuOpen"
          class="glass mt-2 flex flex-col gap-2 rounded-[24px] p-4 shadow-xl md:hidden"
        >
          <a
            v-for="link in links"
            :key="link.label"
            :href="link.href"
            class="flex items-center rounded-xl px-4 py-3 text-sm font-medium text-muted-foreground hover:bg-primary/10 hover:text-primary transition"
            @click="closeMobileMenu"
          >
            {{ link.label }}
          </a>
        </div>
      </transition>
    </div>
  </header>
</template>
