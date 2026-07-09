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
    class="py-24 lg:py-32 bg-emerald-50/50 islamic-pattern relative overflow-hidden"
  >
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-4 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-emerald-50 text-emerald-700"
          >Program</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-emerald-900 mb-6">
          {{ section.title || 'Program Unggulan' }}
        </h2>
      </div>
      <div class="arabesque-divider max-w-xs mx-auto mb-16"></div>

      <div class="grid md:grid-cols-2 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'group relative rounded-3xl overflow-hidden border-2 border-amber-200/30 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <div class="aspect-[16/10] overflow-hidden">
            <img
              v-if="item.image"
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
            />
            <div
              v-else
              class="w-full h-full bg-gradient-to-br from-emerald-100 to-amber-50 flex items-center justify-center"
            >
              <span class="text-5xl">{{ item.icon || '📖' }}</span>
            </div>
          </div>
          <div
            class="absolute inset-0 bg-gradient-to-t from-emerald-950/80 via-emerald-900/20 to-transparent flex items-end p-8"
          >
            <div>
              <h3 class="text-xl font-bold text-white mb-2">{{ item.title }}</h3>
              <p class="text-white/70 text-sm line-clamp-2">{{ item.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
