<script setup>
import { ref, onMounted } from 'vue'
import { getDefaultImage } from '../../composables/useDefaultImages'
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
    class="py-24 lg:py-32 bg-primary/5 islamic-pattern relative overflow-hidden"
  >
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-4 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-primary/5 text-primary"
          >Program</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-primary mb-6">
          {{ section.title || 'Program Unggulan' }}
        </h2>
      </div>
      <div class="arabesque-divider max-w-xs mx-auto mb-16"></div>

      <div class="flex flex-wrap justify-center gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'w-full md:w-[calc(50%-1rem)] group relative rounded-3xl overflow-hidden border-2 border-secondary/20 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2',
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
              class="relative w-full h-full overflow-hidden"
            >
              <img
                :src="getDefaultImage('program', i)"
                :alt="item.title || 'Program Sekolah'"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
              />
              <div class="absolute inset-0 bg-gradient-to-br from-primary/20 to-secondary/10" />
            </div>
          </div>
          <div
            class="absolute inset-0 bg-gradient-to-t from-primary/10 via-primary/20 to-transparent flex items-end p-8"
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
