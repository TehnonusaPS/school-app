<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { ArrowRight, ChevronDown, ChevronLeft, ChevronRight } from 'lucide-vue-next'

const props = defineProps({ hero: Object, branding: Object })

const images = computed(() => {
  const heroImages = props.hero?.images || []
  if (heroImages.length) return heroImages
  if (props.hero?.image) return [{ url: props.hero.image, caption: '' }]
  return []
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
  if (images.value.length > 1) interval = setInterval(nextSlide, 6000)
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
    <!-- Background Carousel -->
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
          class="absolute inset-0 bg-cover bg-center bg-no-repeat"
          :style="{ backgroundImage: `url(${img.url})` }"
        />
      </TransitionGroup>
      <div
        v-if="!images.length"
        class="absolute inset-0 bg-gradient-to-br from-primary/10 via-primary/20 to-accent"
      ></div>

      <!-- Islamic arch overlay -->
      <div
        class="absolute inset-0 bg-gradient-to-t from-primary/10 via-primary/20 to-primary/5 z-[1]"
      ></div>

      <!-- Geometric pattern overlay -->
      <div class="absolute inset-0 islamic-pattern opacity-30 z-[2]"></div>

      <!-- Gold radial accents -->
      <div
        class="absolute inset-0 z-[3] opacity-20"
        style="
          background:
            radial-gradient(circle at 80% 30%, #d9770644 0%, transparent 40%),
            radial-gradient(circle at 20% 70%, #d9770633 0%, transparent 40%);
        "
      ></div>
    </div>

    <!-- Decorative arch frame -->
    <div class="absolute bottom-0 left-0 right-0 z-[4] h-24 pointer-events-none">
      <svg
        viewBox="0 0 1440 120"
        class="w-full h-full"
        preserveAspectRatio="none"
      >
        <path
          d="M0,120 L0,80 Q360,0 720,80 Q1080,0 1440,80 L1440,120 Z"
          fill="#fefcf3"
        />
      </svg>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-32 lg:py-40">
      <div class="max-w-3xl">
        <!-- Bismillah badge -->
        <div
          class="inline-flex items-center gap-2 px-5 py-2 rounded-full border border-secondary/20 bg-secondary backdrop-blur-sm text-secondary text-sm font-medium mb-8"
        >
          ﷽
        </div>

        <h1
          class="heading-font text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold text-white leading-[1.15] mb-6"
        >
          {{ hero.title || ('Selamat Datang di ' + (branding?.entityName || 'Instansi')) }}
        </h1>

        <p
          v-if="hero.subtitle"
          class="text-xl md:text-2xl text-secondary font-medium mb-4"
        >
          {{ hero.subtitle }}
        </p>

        <p class="text-lg text-white/70 mb-10 leading-relaxed max-w-2xl">
          {{
            hero.description ||
            'Membangun generasi Qurani yang berilmu, berakhlak, dan berprestasi.'
          }}
        </p>

        <div class="flex flex-wrap gap-4">
          <button
            @click="scrollTo('registration_cta')"
            class="group flex items-center gap-2 px-8 py-4 rounded-2xl bg-secondary text-primary font-bold text-sm hover:bg-secondary shadow-lg shadow-amber-500/20 transition-all hover:-translate-y-1"
          >
            {{ hero.ctaText || 'Daftar Sekarang' }}
            <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
          </button>
          <button
            @click="scrollTo('about')"
            class="px-8 py-4 rounded-2xl text-white border-2 border-secondary/20 font-semibold text-sm hover:bg-white/5 transition-all hover:-translate-y-1"
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
        class="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-secondary hover:bg-secondary backdrop-blur-sm flex items-center justify-center text-secondary transition-all"
      >
        <ChevronLeft class="w-6 h-6" />
      </button>
      <button
        @click="nextSlide"
        class="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-secondary hover:bg-secondary backdrop-blur-sm flex items-center justify-center text-secondary transition-all"
      >
        <ChevronRight class="w-6 h-6" />
      </button>
      <div class="absolute bottom-28 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <button
          v-for="(_, i) in images"
          :key="i"
          @click="currentSlide = i"
          :class="[
            'w-3 h-3 rounded-full transition-all duration-300',
            i === currentSlide ? 'bg-secondary scale-125 w-8' : 'bg-white/30 hover:bg-white/50'
          ]"
        />
      </div>
    </template>

    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 text-secondary animate-bounce">
      <ChevronDown class="w-8 h-8" />
    </div>
  </section>
</template>
