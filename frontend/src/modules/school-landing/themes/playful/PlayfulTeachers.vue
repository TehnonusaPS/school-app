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
    class="py-24 lg:py-32 bg-purple-50/50 relative"
  >
    <div class="max-w-7xl mx-auto px-6">
      <div
        :class="[
          'text-center mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-5 py-2 rounded-full text-xs font-extrabold uppercase tracking-widest mb-4 bg-purple-100 text-purple-600"
          >👨‍🏫 Guru</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-purple-800">
          {{ section.title || 'Tenaga Pendidik' }}
        </h2>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'text-center transition-all duration-500 fun-wiggle',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 80}ms` }"
        >
          <div
            class="relative mb-5 mx-auto w-36 h-36 rounded-full overflow-hidden border-4 border-yellow-400 shadow-xl hover:scale-105 transition-transform"
          >
            <img
              v-if="item.image"
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover"
            />
            <div
              v-else
              class="w-full h-full bg-gradient-to-br from-purple-500 via-pink-500 to-yellow-400 flex items-center justify-center text-white text-4xl font-extrabold"
            >
              {{ (item.title || 'T')[0] }}
            </div>
          </div>
          <h4 class="font-extrabold text-purple-800">{{ item.title }}</h4>
          <p class="text-sm text-gray-400 mt-1">{{ item.description || item.value }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
