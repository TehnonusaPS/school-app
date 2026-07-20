<script setup>
import { ref, onMounted } from 'vue'
import * as LucideIcons from 'lucide-vue-next'
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
function getIcon(name) {
  if (!name || typeof name !== 'string') return null
  const pascal = name.replace(/(^|-)(\w)/g, (_, __, c) => c.toUpperCase())
  return LucideIcons[pascal] || null
}
</script>

<template>
  <section
    id="features"
    ref="el"
    class="py-24 lg:py-32 bg-[#fefcf3] relative"
  >
    <div class="max-w-7xl mx-auto px-6">
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-4 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-primary/5 text-primary"
          >Keunggulan</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-primary mb-6">
          {{ section.title || 'Keunggulan Kami' }}
        </h2>
      </div>
      <div class="arabesque-divider max-w-xs mx-auto mb-16"></div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'group bg-white rounded-2xl p-8 border-2 border-primary/20 hover:border-secondary/20 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 80}ms` }"
        >
          <div
            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-accent flex items-center justify-center mb-6 group-hover:from-secondary/10 group-hover:to-secondary/5 transition-all"
          >
            <component
              v-if="getIcon(item.icon)"
              :is="getIcon(item.icon)"
              class="w-7 h-7 text-white"
            />
            <span
              v-else
              class="text-2xl"
              >{{ item.icon || '✨' }}</span
            >
          </div>
          <h3 class="text-lg font-bold text-primary mb-3">{{ item.title }}</h3>
          <p class="text-gray-500 text-sm leading-relaxed">{{ item.description }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
