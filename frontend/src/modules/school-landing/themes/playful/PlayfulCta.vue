<script setup>
import { ArrowRight } from 'lucide-vue-next'
const props = defineProps({ section: Object, branding: Object })
function scrollTo(id) {
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
</script>

<template>
  <section
    id="registration_cta"
    class="py-24 lg:py-32 bg-gradient-to-r from-primary via-secondary to-accent/50 relative overflow-hidden"
  >
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-10 left-[10%] text-6xl opacity-20 fun-bounce">🎉</div>
      <div class="absolute bottom-10 right-[10%] text-6xl opacity-20 fun-bounce-2">🎈</div>
      <div class="absolute top-1/2 left-1/2 text-8xl opacity-10 fun-bounce">⭐</div>
    </div>

    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
      <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
        {{ section.title || '🎉 Ayo Bergabung!' }}
      </h2>
      <p class="text-white/80 text-lg mb-10 max-w-2xl mx-auto">
        {{
          section.subtitle ||
          'Daftarkan anak Anda untuk pengalaman belajar yang seru dan menyenangkan!'
        }}
      </p>
      <button
        @click="scrollTo('contact')"
        class="group inline-flex items-center gap-2 px-10 py-4 rounded-2xl bg-white text-primary font-extrabold text-lg shadow-lg hover:shadow-2xl transition-all hover:-translate-y-1 hover:scale-105"
      >
        🎒 Daftar Sekarang!
        <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
      </button>
    </div>
  </section>
</template>
