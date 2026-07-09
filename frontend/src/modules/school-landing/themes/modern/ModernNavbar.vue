<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Menu, X } from 'lucide-vue-next'

const props = defineProps({
  branding: Object,
  data: Object
})

const isScrolled = ref(false)
const isMobileOpen = ref(false)

const navLinks = [
  { id: 'about', label: 'Tentang' },
  { id: 'features', label: 'Keunggulan' },
  { id: 'programs', label: 'Program' },
  { id: 'gallery', label: 'Galeri' },
  { id: 'contact', label: 'Kontak' }
]

function scrollTo(id) {
  isMobileOpen.value = false
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function handleScroll() {
  isScrolled.value = window.scrollY > 60
}

onMounted(() => window.addEventListener('scroll', handleScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<template>
  <nav
    :class="[
      'fixed top-0 w-full z-50 transition-all duration-500',
      isScrolled
        ? 'bg-white/80 backdrop-blur-xl shadow-lg shadow-black/5 py-3'
        : 'bg-transparent py-5'
    ]"
  >
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
      <!-- Logo -->
      <a
        href="#"
        @click.prevent="scrollTo('hero')"
        class="flex items-center gap-3 group"
      >
        <div
          v-if="branding.logo"
          class="w-11 h-11 rounded-xl overflow-hidden shadow-md group-hover:shadow-lg transition-shadow"
        >
          <img
            :src="branding.logo"
            alt="Logo"
            class="w-full h-full object-cover"
          />
        </div>
        <div
          v-else
          class="w-11 h-11 rounded-xl flex items-center justify-center shadow-md font-bold text-white text-lg"
          :style="{
            background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
          }"
        >
          {{ (data?.meta_title || 'S')[0] }}
        </div>
        <div class="leading-tight">
          <div
            :class="[
              'font-bold text-base tracking-tight transition-colors',
              isScrolled ? 'text-gray-800' : 'text-white'
            ]"
          >
            {{ data?.meta_title || 'Sekolah Kami' }}
          </div>
          <div :class="['text-xs', isScrolled ? 'text-gray-400' : 'text-white/70']">
            {{ data?.hero_subtitle || '' }}
          </div>
        </div>
      </a>

      <!-- Desktop Links -->
      <div class="hidden lg:flex items-center gap-1">
        <button
          v-for="link in navLinks"
          :key="link.id"
          @click="scrollTo(link.id)"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-medium transition-all hover:bg-white/10',
            isScrolled
              ? 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
              : 'text-white/90 hover:text-white'
          ]"
        >
          {{ link.label }}
        </button>
      </div>

      <!-- CTA -->
      <div class="hidden lg:block">
        <button
          @click="scrollTo('registration_cta')"
          class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5"
          :style="{
            background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
          }"
        >
          Daftar Sekarang
        </button>
      </div>

      <!-- Mobile Toggle -->
      <button
        @click="isMobileOpen = !isMobileOpen"
        class="lg:hidden p-2 rounded-lg"
        :class="isScrolled ? 'text-gray-700' : 'text-white'"
      >
        <Menu
          v-if="!isMobileOpen"
          class="w-6 h-6"
        />
        <X
          v-else
          class="w-6 h-6"
        />
      </button>
    </div>

    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div
        v-if="isMobileOpen"
        class="lg:hidden absolute top-full left-0 w-full bg-white/95 backdrop-blur-xl border-t border-gray-100 shadow-xl px-6 py-6"
      >
        <div class="flex flex-col gap-1">
          <button
            v-for="link in navLinks"
            :key="link.id"
            @click="scrollTo(link.id)"
            class="text-left text-gray-700 font-medium py-3 px-4 rounded-xl hover:bg-gray-50 transition-colors"
          >
            {{ link.label }}
          </button>
          <button
            @click="scrollTo('registration_cta')"
            class="mt-3 py-3 rounded-xl text-white font-semibold text-center"
            :style="{
              background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
            }"
          >
            Daftar Sekarang
          </button>
        </div>
      </div>
    </Transition>
  </nav>
</template>
