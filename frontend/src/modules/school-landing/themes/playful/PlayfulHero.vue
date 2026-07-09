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
    <div class="absolute inset-0 z-0">
      <TransitionGroup
        enter-active-class="transition-opacity duration-1000"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-1000 absolute inset-0"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-for="(img, i) in images"
          v-show="i === currentSlide"
          :key="i"
          class="absolute inset-0 bg-cover bg-center"
          :style="{ backgroundImage: `url(${img.url})` }"
        />
      </TransitionGroup>
      <div
        v-if="!images.length"
        class="absolute inset-0 bg-gradient-to-br from-purple-600 via-pink-500 to-yellow-400"
      ></div>
      <div
        class="absolute inset-0 bg-gradient-to-br from-purple-900/60 via-pink-800/40 to-transparent z-[1]"
      ></div>
    </div>

    <!-- Fun floating shapes -->
    <div class="absolute inset-0 z-[2] pointer-events-none overflow-hidden">
      <div
        class="absolute top-20 right-10 w-32 h-32 rounded-full bg-yellow-400/20 blur-2xl fun-bounce"
      ></div>
      <div
        class="absolute bottom-40 left-10 w-24 h-24 rounded-full bg-pink-400/20 blur-2xl fun-bounce-2"
      ></div>
      <div class="absolute top-1/3 right-1/4 text-6xl fun-bounce opacity-20">⭐</div>
      <div class="absolute bottom-1/3 left-20 text-5xl fun-bounce-2 opacity-20">🎈</div>
      <div
        class="absolute top-1/2 right-10 text-4xl fun-bounce opacity-15"
        style="animation-delay: 1s"
      >
        🌈
      </div>
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
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white/20 backdrop-blur-sm text-white text-sm font-bold mb-8 border border-white/20"
        >
          🎒 Pendaftaran Siswa Baru Dibuka!
        </div>

        <h1
          class="heading-font text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold text-white leading-[1.1] mb-6"
        >
          {{ hero.title || 'Selamat Datang di Sekolah Kami!' }} 🎉
        </h1>

        <p
          v-if="hero.subtitle"
          class="text-xl md:text-2xl text-yellow-200 font-bold mb-4"
        >
          {{ hero.subtitle }}
        </p>

        <p class="text-lg text-white/80 mb-10 leading-relaxed max-w-2xl">
          {{ hero.description || 'Tempat belajar yang seru, kreatif, dan penuh petualangan!' }}
        </p>

        <div class="flex flex-wrap gap-4">
          <button
            @click="scrollTo('registration_cta')"
            class="group flex items-center gap-2 px-8 py-4 rounded-2xl bg-yellow-400 text-purple-900 font-extrabold text-sm shadow-lg shadow-yellow-400/30 hover:bg-yellow-300 transition-all hover:-translate-y-1 hover:scale-105"
          >
            {{ hero.ctaText || '🎉 Daftar Sekarang!' }}
            <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
          </button>
          <button
            @click="scrollTo('about')"
            class="px-8 py-4 rounded-2xl text-white border-3 border-white/30 font-bold text-sm hover:bg-white/10 transition-all hover:-translate-y-1"
          >
            📖 Tentang Kami
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
            i === currentSlide ? 'bg-yellow-400 scale-125 w-8' : 'bg-white/40'
          ]"
        />
      </div>
    </template>

    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 text-white/60 animate-bounce">
      <ChevronDown class="w-8 h-8" />
    </div>
  </section>
</template>
