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
    { threshold: 0.15 }
  )
  if (el.value) obs.observe(el.value)
})
</script>

<template>
  <section
    id="programs"
    ref="el"
    class="py-24 lg:py-32 bg-purple-50/50 relative overflow-hidden"
  >
    <div class="absolute top-20 right-10 text-8xl opacity-5 fun-bounce">📚</div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-5 py-2 rounded-full text-xs font-extrabold uppercase tracking-widest mb-4 bg-pink-100 text-pink-600"
          >📚 Program</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-purple-800 mb-6">
          {{ section.title || 'Program Unggulan' }}
        </h2>
      </div>

      <div class="grid md:grid-cols-2 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'group rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl border-3 border-white hover:border-yellow-400 transition-all duration-500 hover:-translate-y-2 fun-wiggle',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <div class="aspect-[16/10] overflow-hidden">
            <img
              v-if="item.image"
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
            />
            <div
              v-else
              class="w-full h-full bg-gradient-to-br from-purple-200 via-pink-100 to-yellow-100 flex items-center justify-center"
            >
              <span class="text-6xl">{{ item.icon || '📖' }}</span>
            </div>
          </div>
          <div class="bg-white p-6">
            <h3 class="text-lg font-extrabold text-purple-800 mb-2">{{ item.title }}</h3>
            <p class="text-gray-500 text-sm line-clamp-2">{{ item.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
