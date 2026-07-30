<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Menu, X } from 'lucide-vue-next'
const props = defineProps({
  branding: Object,
  data: Object,
  sections: { type: Array, default: () => [] }
})
const isScrolled = ref(false)
const isMobileOpen = ref(false)
const navLinks = computed(() => {
  const links = [{ id: 'about', label: 'Tentang' }]
  const sectionMap = {
    features: 'Keunggulan',
    stats: 'Statistik',
    programs: 'Program',
    gallery: 'Galeri',
    testimonials: 'Testimoni',
    faq: 'FAQ'
  }
  for (const s of props.sections) {
    if (s.is_visible !== false && s.items?.length > 0 && sectionMap[s.type]) {
      links.push({ id: s.type, label: sectionMap[s.type] })
    }
  }
  links.push({ id: 'contact', label: 'Kontak' })
  return links
})
function scrollTo(id) {
  isMobileOpen.value = false
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
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
        ? 'bg-white/95 backdrop-blur-xl shadow-lg shadow-purple-100/50 py-2'
        : 'bg-transparent py-5'
    ]"
  >
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
      <a
        href="#"
        @click.prevent="scrollTo('hero')"
        class="flex items-center gap-3 group"
      >
        <div
          v-if="branding.logo"
          class="w-12 h-12 rounded-2xl overflow-hidden shadow-md border-3 border-accent/20 group-hover:scale-110 transition-transform"
        >
          <img
            :src="branding.logo"
            alt="Logo"
            class="w-full h-full object-cover"
          />
        </div>
        <div
          v-else
          class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-md font-bold text-white text-xl bg-gradient-to-br from-primary via-secondary to-accent/50 group-hover:scale-110 transition-transform"
        >
          {{ (data?.meta_title || 'S')[0] }}
        </div>
        <div class="leading-tight">
          <div :class="['font-extrabold text-base', isScrolled ? 'text-primary' : 'text-white']">
            {{ data?.meta_title || (branding?.entityName || 'Instansi') }}
          </div>
          <div :class="['text-xs font-medium', isScrolled ? 'text-secondary' : 'text-accent']">
            {{ data?.hero_subtitle || 'Pendidikan Berkualitas' }}
          </div>
        </div>
      </a>

      <div class="hidden lg:flex items-center gap-1">
        <button
          v-for="link in navLinks"
          :key="link.id"
          @click="scrollTo(link.id)"
          :class="[
            'px-4 py-2 rounded-xl text-sm font-bold transition-all fun-wiggle',
            isScrolled
              ? 'text-gray-600 hover:text-primary hover:bg-primary/5'
              : 'text-white/90 hover:text-white hover:bg-white/10'
          ]"
        >
          {{ link.label }}
        </button>
      </div>

      <div class="hidden lg:block">
        <a
          :href="data?.hero_cta_link || '#contact'"
          class="inline-block px-6 py-2.5 rounded-2xl text-sm font-bold text-white shadow-lg bg-primary hover:bg-primary/90 hover:shadow-xl transition-all hover:-translate-y-0.5 hover:scale-105"
        >
          {{ data?.hero_cta_text || 'Daftar Yuk!' }}
        </a>
      </div>

      <button
        @click="isMobileOpen = !isMobileOpen"
        class="lg:hidden p-2"
        :class="isScrolled ? 'text-primary' : 'text-white'"
      >
        <Menu
          v-if="!isMobileOpen"
          class="w-6 h-6"
        /><X
          v-else
          class="w-6 h-6"
        />
      </button>
    </div>

    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div
        v-if="isMobileOpen"
        class="lg:hidden absolute top-full left-0 w-full bg-white/98 backdrop-blur-xl border-t-4 border-primary/20 shadow-xl px-6 py-6"
      >
        <div class="flex flex-col gap-1">
          <button
            v-for="link in navLinks"
            :key="link.id"
            @click="scrollTo(link.id)"
            class="text-left text-gray-700 font-bold py-3 px-4 rounded-xl hover:bg-primary/5 transition-colors"
          >
            {{ link.label }}
          </button>
          <a
            :href="data?.hero_cta_link || '#contact'"
            @click="isMobileOpen = false"
            class="block mt-3 py-3 rounded-2xl text-white font-bold text-center bg-primary hover:bg-primary/90 transition-colors"
          >
            {{ data?.hero_cta_text || 'Daftar Yuk!' }}
          </a>
        </div>
      </div>
    </Transition>
  </nav>
</template>
