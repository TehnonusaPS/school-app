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
    class="py-24 lg:py-32 bg-primary islamic-pattern relative overflow-hidden"
  >
    <div
      class="absolute inset-0 opacity-20"
      style="
        background:
          radial-gradient(circle at 30% 50%, #d9770633 0%, transparent 50%),
          radial-gradient(circle at 70% 30%, #04785733 0%, transparent 50%);
      "
    ></div>
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
      <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
        {{ section.title || 'Bergabunglah Bersama Kami' }}
      </h2>
      <div class="arabesque-divider max-w-xs mx-auto my-8"></div>
      <p class="text-white/70 text-lg mb-10 max-w-2xl mx-auto">
        {{ section.subtitle || 'Daftarkan putra/putri Anda dan berikan pendidikan terbaik.' }}
      </p>
      <button
        @click="scrollTo('contact')"
        class="group inline-flex items-center gap-2 px-10 py-4 rounded-2xl bg-secondary text-primary font-bold text-lg hover:bg-secondary shadow-lg shadow-amber-500/20 transition-all hover:-translate-y-1"
      >
        Daftar Sekarang
        <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
      </button>
    </div>
  </section>
</template>
