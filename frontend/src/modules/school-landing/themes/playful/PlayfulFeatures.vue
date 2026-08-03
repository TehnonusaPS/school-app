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
const bgColors = [
  'from-primary to-primary/50',
  'from-secondary to-secondary/50',
  'from-accent to-orange-500',
  'from-cyan-400 to-blue-500',
  'from-green-400 to-emerald-500',
  'from-red-400 to-rose-500'
]
</script>

<template>
  <section
    id="features"
    ref="el"
    class="py-24 lg:py-32 bg-white relative"
  >
    <div class="max-w-7xl mx-auto px-6">
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-primary/10 text-primary"
          >Keunggulan</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-primary mb-6">
          {{ section.title || 'Keunggulan Kami' }}
        </h2>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'group bg-white rounded-[2rem] p-8 shadow-xl shadow-gray-200/50 border border-gray-100 hover:border-primary/20 hover:shadow-2xl hover:shadow-primary/10 transition-all duration-500 hover:-translate-y-3',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 80}ms` }"
        >
          <div
            :class="[
              'w-16 h-16 rounded-2xl bg-gradient-to-br flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all shadow-lg',
              bgColors[i % bgColors.length]
            ]"
          >
            <component
              v-if="getIcon(item.icon)"
              :is="getIcon(item.icon)"
              class="w-8 h-8 text-white"
            />
            <LucideIcons.Sparkles
              v-else
              class="w-8 h-8 text-white"
            />
          </div>
          <h3 class="text-lg font-extrabold text-gray-800 mb-3">{{ item.title }}</h3>
          <p class="text-gray-500 text-sm leading-relaxed">{{ item.description }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
