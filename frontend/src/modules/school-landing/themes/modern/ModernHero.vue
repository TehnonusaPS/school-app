<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { ArrowRight, ChevronDown, ChevronLeft, ChevronRight } from 'lucide-vue-next'

const props = defineProps({
  hero: Object,
  branding: Object
})

// Carousel
const images = computed(() => {
  const heroImages = props.hero?.images || []
  if (heroImages.length) return heroImages
  // Fallback to single hero image
  if (props.hero?.image) return [{ url: props.hero.image, caption: '' }]
  return []
})
const currentSlide = ref(0)
let interval = null

function nextSlide() {
  if (images.value.length <= 1) return
  currentSlide.value = (currentSlide.value + 1) % images.value.length
}
function prevSlide() {
  if (images.value.length <= 1) return
  currentSlide.value = (currentSlide.value - 1 + images.value.length) % images.value.length
}

onMounted(() => {
  if (images.value.length > 1) {
    interval = setInterval(nextSlide, 6000)
  }
})
onUnmounted(() => {
  if (interval) clearInterval(interval)
})

function scrollTo(id) {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
</script>

<template>
  <section
    id="hero"
    class="relative min-h-screen flex items-center overflow-hidden"
  >
    <!-- Background Carousel -->
    <div class="absolute inset-0 z-0 overflow-hidden">
      <div 
        class="flex w-full h-full transition-transform duration-700 ease-in-out"
        :style="{ transform: `translateX(-${currentSlide * 100}%)` }"
      >
        <div
          v-for="(img, i) in images"
          :key="i"
          class="w-full h-full flex-shrink-0 bg-cover bg-center bg-no-repeat"
          :style="{ backgroundImage: `url(${img.url})` }"
        ></div>
      </div>

      <!-- Fallback gradient if no images -->
      <div
        v-if="!images.length"
        class="absolute inset-0"
        :style="{
          background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
        }"
      ></div>

      <!-- Gradient overlay -->
      <div
        class="absolute inset-0 bg-gradient-to-br from-black/70 via-black/50 to-transparent z-[1]"
      ></div>
      <!-- Decorative gradient mesh -->
      <div
        class="absolute inset-0 opacity-40 z-[2]"
        :style="{
          background: `radial-gradient(circle at 20% 50%, ${branding.primaryColor}66 0%, transparent 50%),
                       radial-gradient(circle at 80% 20%, ${branding.accentColor}44 0%, transparent 50%)`
        }"
      ></div>
    </div>

    <!-- Floating geometric shapes -->
    <div class="absolute inset-0 z-[3] overflow-hidden pointer-events-none">
      <div
        class="absolute top-20 right-20 w-72 h-72 rounded-full opacity-10 blur-3xl animate-pulse"
        :style="{ background: branding.secondaryColor }"
      ></div>
      <div
        class="absolute bottom-32 left-10 w-56 h-56 rounded-full opacity-10 blur-3xl animate-pulse"
        style="animation-delay: 1s"
        :style="{ background: branding.accentColor }"
      ></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 w-full max-w-7xl mx-auto px-6 pt-24 pb-12">
      <div class="max-w-3xl">
        <!-- Badge -->
        <div
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-white/20 bg-white/10 backdrop-blur-md text-white text-sm font-bold mb-8 animate-fade-in"
        >
          <span
            class="w-2.5 h-2.5 rounded-full animate-pulse shadow-[0_0_10px_rgba(255,255,255,0.8)]"
            :style="{ background: branding.secondaryColor }"
          ></span>
          Pendaftaran Siswa Baru Dibuka
        </div>

        <!-- Title -->
        <h1
          class="heading-font text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-white leading-[1.1] mb-6 drop-shadow-lg"
        >
          {{ hero.title || ('Selamat Datang di ' + (branding?.entityName || 'Instansi')) }}
        </h1>

        <!-- Subtitle -->
        <p
          v-if="hero.subtitle"
          class="text-xl md:text-2xl text-white font-bold mb-4 drop-shadow-md"
        >
          {{ hero.subtitle }}
        </p>

        <p class="text-lg md:text-xl text-white/90 mb-10 leading-relaxed max-w-2xl font-medium drop-shadow-sm">
          {{ hero.description || 'Membangun generasi unggul, berkarakter, dan berprestasi.' }}
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-wrap gap-4">
          <button
            @click="scrollTo('registration_cta')"
            class="group flex items-center gap-2 px-8 py-4 rounded-[1.25rem] text-white font-bold text-sm hover:shadow-[0_0_30px_rgba(255,255,255,0.3)] transition-all hover:-translate-y-1 shadow-xl"
            :style="{
              background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
            }"
          >
            {{ hero.ctaText || 'Daftar Sekarang' }}
            <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
          </button>
          <button
            @click="scrollTo('about')"
            class="px-8 py-4 rounded-[1.25rem] text-white bg-white/10 border-2 border-white/30 font-bold text-sm hover:bg-white/20 backdrop-blur-md transition-all hover:-translate-y-1 shadow-lg"
          >
            Tentang Kami
          </button>
        </div>
      </div>
    </div>

    <!-- Carousel Controls -->
    <template v-if="images.length > 1">
      <button
        @click="prevSlide"
        class="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm flex items-center justify-center text-white transition-all"
      >
        <ChevronLeft class="w-6 h-6" />
      </button>
      <button
        @click="nextSlide"
        class="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm flex items-center justify-center text-white transition-all"
      >
        <ChevronRight class="w-6 h-6" />
      </button>

      <!-- Dots -->
      <div class="absolute bottom-20 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <button
          v-for="(_, i) in images"
          :key="i"
          @click="currentSlide = i"
          :class="[
            'w-3 h-3 rounded-full transition-all duration-300',
            i === currentSlide ? 'bg-white scale-125 w-8' : 'bg-white/40 hover:bg-white/60'
          ]"
        />
      </div>
    </template>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 text-white/60 animate-bounce">
      <ChevronDown class="w-8 h-8" />
    </div>
  </section>
</template>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in {
  animation: fade-in 0.8s ease-out both;
}
</style>
