<script setup>
import { ArrowRight } from 'lucide-vue-next'

const props = defineProps({
  section: Object,
  branding: Object
})

function scrollTo(id) {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
</script>

<template>
  <section
    id="registration_cta"
    class="py-24 lg:py-32 relative overflow-hidden"
    :style="{
      background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
    }"
  >
    <!-- Decorations -->
    <div
      class="absolute inset-0 opacity-10"
      style="
        background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0);
        background-size: 40px 40px;
      "
    ></div>
    <div class="absolute top-10 right-10 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute bottom-10 left-10 w-48 h-48 rounded-full bg-white/10 blur-3xl"></div>

    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
      <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
        {{ section.title || 'Siap Bergabung Bersama Kami?' }}
      </h2>
      <p class="text-white/80 text-lg mb-10 max-w-2xl mx-auto leading-relaxed">
        {{
          section.subtitle ||
          'Daftarkan putra/putri Anda sekarang dan berikan mereka pendidikan terbaik untuk masa depan cerah.'
        }}
      </p>

      <div class="flex flex-wrap justify-center gap-4">
        <a
          v-if="section.items?.[0]?.link"
          :href="section.items[0].link"
          class="group inline-flex items-center gap-2 px-10 py-4 rounded-2xl bg-white font-bold text-lg hover:shadow-2xl transition-all hover:-translate-y-1"
          :style="{ color: branding.primaryColor }"
        >
          {{ section.items?.[0]?.title || 'Daftar Sekarang' }}
          <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
        </a>
        <button
          v-else
          @click="scrollTo('contact')"
          class="group inline-flex items-center gap-2 px-10 py-4 rounded-2xl bg-white font-bold text-lg hover:shadow-2xl transition-all hover:-translate-y-1"
          :style="{ color: branding.primaryColor }"
        >
          Daftar Sekarang
          <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
        </button>
      </div>
    </div>
  </section>
</template>
