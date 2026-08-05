<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { ArrowRight, ChevronDown, ChevronLeft, ChevronRight } from 'lucide-vue-next'
const props = defineProps({ hero: Object, branding: Object })
const images = computed(() => {
  const h = props.hero?.images || []
  return h.length ? h : props.hero?.image ? [{ url: props.hero.image }] : []
})
const currentSlide = ref(0)
let interval = null
function nextSlide() {
  if (images.value.length > 1) currentSlide.value = (currentSlide.value + 1) % images.value.length
}
function prevSlide() {
  if (images.value.length > 1)
    currentSlide.value = (currentSlide.value - 1 + images.value.length) % images.value.length
}
onMounted(() => {
  if (images.value.length > 1) interval = setInterval(nextSlide, 5000)
})
onUnmounted(() => {
  if (interval) clearInterval(interval)
})
function scrollTo(id) {
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
</script>

<template>
  <section
    id="hero"
    class="relative min-h-screen flex items-center overflow-hidden"
  >
    <!-- Background -->
    <div class="absolute inset-0 z-0 overflow-hidden">
      <div 
        class="flex w-full h-full transition-transform duration-700 ease-in-out"
        :style="{ transform: `translateX(-${currentSlide * 100}%)` }"
      >
        <div
          v-for="(img, i) in images"
          :key="i"
          class="w-full h-full flex-shrink-0 bg-cover bg-center"
          :style="{ backgroundImage: `url(${img.url})` }"
        ></div>
      </div>
      <div
        v-if="!images.length"
        class="absolute inset-0 bg-gradient-to-br from-primary via-secondary to-accent/50 z-[1]"
      ></div>
      <div
        v-else
        class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-black/10 z-[1]"
      ></div>
    </div>

    <!-- Soft glowing blobs instead of emojis -->
    <div class="absolute inset-0 z-[2] pointer-events-none overflow-hidden">
      <div
        class="absolute top-20 right-[10%] w-[30rem] h-[30rem] rounded-full bg-accent/10 mix-blend-screen blur-[100px] animate-pulse"
      ></div>
      <div
        class="absolute bottom-10 left-[5%] w-[25rem] h-[25rem] rounded-full bg-secondary/10 mix-blend-screen blur-[100px] animate-pulse"
        style="animation-delay: 2s"
      ></div>
      <div
        class="absolute top-1/2 left-1/3 w-[20rem] h-[20rem] rounded-full bg-primary/10 mix-blend-screen blur-[100px] animate-pulse"
        style="animation-delay: 4s"
      ></div>
    </div>

    <!-- Wavy bottom -->
    <div class="absolute bottom-0 left-0 w-full z-[4]">
      <svg
        viewBox="0 0 1440 120"
        preserveAspectRatio="none"
        class="w-full h-20"
      >
        <path
          d="M0,40 C280,120 720,-20 1440,60 L1440,120 L0,120 Z"
          fill="white"
        />
      </svg>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-32 lg:py-40">
      <div class="max-w-3xl">
        <div
          v-if="hero.badgeText"
          class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-white/10 backdrop-blur-md text-white text-sm font-bold mb-8 border border-white/20 shadow-xl"
        >
          <span class="w-2.5 h-2.5 rounded-full bg-secondary animate-pulse"></span>
          {{ hero.badgeText }}
        </div>

        <h1
          class="heading-font text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-extrabold text-white leading-[1.1] mb-6 tracking-tight line-clamp-3 break-words drop-shadow-sm"
        >
          {{ hero.title || ('Selamat Datang di ' + (branding?.entityName || 'Instansi')) }}
        </h1>

        <p
          v-if="hero.subtitle"
          class="text-xl md:text-2xl text-secondary font-bold mb-6 line-clamp-2 break-words drop-shadow-sm"
        >
          {{ hero.subtitle }}
        </p>

        <p class="text-lg md:text-xl text-white/90 mb-10 leading-relaxed max-w-2xl font-medium line-clamp-4 break-words">
          {{ hero.description || 'Pendidikan berkualitas dengan lingkungan belajar yang kreatif, menyenangkan, dan inovatif.' }}
        </p>

        <div class="flex flex-wrap gap-4">
          <a
            :href="hero.ctaLink || '#contact'"
            class="group flex items-center gap-3 px-8 py-4 rounded-[1.25rem] bg-white text-primary font-extrabold text-sm shadow-xl shadow-black/10 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:scale-105"
          >
            <span class="truncate">{{ hero.ctaText || 'Daftar Sekarang' }}</span>
            <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
          </a>
          <button
            @click="scrollTo('about')"
            class="px-8 py-4 rounded-[1.25rem] text-white bg-white/10 backdrop-blur-md border border-white/20 font-bold text-sm hover:bg-white/20 transition-all duration-300 hover:-translate-y-1 shadow-lg"
          >
            Tentang Kami
          </button>
        </div>
      </div>
    </div>

    <!-- Carousel controls -->
    <template v-if="images.length > 1">
      <button
        @click="prevSlide"
        class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-all"
      >
        <ChevronLeft class="w-6 h-6" />
      </button>
      <button
        @click="nextSlide"
        class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-all"
      >
        <ChevronRight class="w-6 h-6" />
      </button>
      <div class="absolute bottom-24 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <button
          v-for="(_, i) in images"
          :key="i"
          @click="currentSlide = i"
          :class="[
            'w-3 h-3 rounded-full transition-all',
            i === currentSlide ? 'bg-accent scale-125 w-8' : 'bg-white/40'
          ]"
        />
      </div>
    </template>

    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 text-white/60 animate-bounce">
      <ChevronDown class="w-8 h-8" />
    </div>
  </section>
</template>
