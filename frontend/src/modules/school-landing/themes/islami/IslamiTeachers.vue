<script setup>
import { ref, onMounted } from 'vue'
const props = defineProps({ section: Object, branding: Object })
const el = ref(null)
const isVisible = ref(false)
onMounted(() => {
  const obs = new IntersectionObserver(
    ([e]) => {
      if (e.isIntersecting) {
        isVisible.value = true
        obs.disconnect()
      }
    },
    { threshold: 0.2 }
  )
  if (el.value) obs.observe(el.value)
})
</script>

<template>
  <section
    ref="el"
    class="py-24 lg:py-32 bg-emerald-50/50 relative"
  >
    <div class="max-w-7xl mx-auto px-6">
      <div
        :class="[
          'text-center mb-4 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-emerald-50 text-emerald-700"
          >Tenaga Pendidik</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-emerald-900">
          {{ section.title || 'Tenaga Pendidik' }}
        </h2>
      </div>
      <div class="arabesque-divider max-w-xs mx-auto mb-16"></div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'text-center transition-all duration-500',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 80}ms` }"
        >
          <div
            class="relative mb-5 mx-auto w-40 h-40 rounded-full overflow-hidden border-4 border-amber-400/30 shadow-xl"
          >
            <img
              v-if="item.image"
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover"
            />
            <div
              v-else
              class="w-full h-full bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center text-white text-4xl font-bold"
            >
              {{ (item.title || 'T')[0] }}
            </div>
          </div>
          <h4 class="font-bold text-emerald-900">{{ item.title }}</h4>
          <p class="text-sm text-gray-400 mt-1">{{ item.description || item.value }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
