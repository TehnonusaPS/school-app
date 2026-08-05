<script setup>
import { ref, onMounted } from 'vue'
import { Quote } from 'lucide-vue-next'
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
    :id="section.type"
    ref="el"
    class="py-24 lg:py-32 bg-primary/5 islamic-pattern relative"
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
          >Testimoni</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-primary">
          {{ section.title || 'Kata Mereka' }}
        </h2>
      </div>
      <div class="arabesque-divider max-w-xs mx-auto mb-16"></div>

      <div class="flex flex-wrap justify-center gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'w-full md:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.35rem)] bg-white rounded-2xl p-8 shadow-lg border-2 border-primary/20 transition-all duration-500 hover:shadow-xl hover:border-secondary/20',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <Quote class="w-8 h-8 mb-4 text-secondary" />
          <p class="text-gray-600 leading-relaxed mb-6 italic">"{{ item.description }}"</p>
          <div class="flex items-center gap-4">
            <div
              v-if="item.image"
              class="w-12 h-12 rounded-full overflow-hidden border-2 border-secondary/20"
            >
              <img
                :src="item.image"
                :alt="item.title"
                class="w-full h-full object-cover"
              />
            </div>
            <div
              v-else
              class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white font-bold border-2 border-secondary/20"
            >
              {{ (item.title || 'U')[0] }}
            </div>
            <div>
              <h4 class="font-semibold text-primary text-sm">{{ item.title }}</h4>
              <p class="text-xs text-gray-400">{{ item.value || '' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
