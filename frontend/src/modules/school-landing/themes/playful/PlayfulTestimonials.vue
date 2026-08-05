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
const cardColors = [
  'border-primary/20 hover:border-primary/20',
  'border-secondary/20 hover:border-secondary/20',
  'border-accent/20 hover:border-accent/20',
  'border-cyan-200 hover:border-cyan-400'
]
</script>

<template>
  <section
    :id="section.type"
    ref="el"
    class="py-24 lg:py-32 bg-primary/5 relative"
  >
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div
        :class="[
          'text-center mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-primary/10 text-primary"
          >Testimoni</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-primary">
          {{ section.title || 'Kata Mereka' }}
        </h2>
      </div>

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'bg-white rounded-[2rem] p-8 shadow-xl shadow-gray-200/50 border border-gray-100 transition-all duration-500 hover:shadow-2xl hover:shadow-primary/10 hover:-translate-y-2',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <div class="mb-6 text-primary/30">
            <Quote class="w-10 h-10" />
          </div>
          <p class="text-gray-600 leading-relaxed mb-6 italic">"{{ item.description }}"</p>
          <div class="flex items-center gap-4">
            <div
              v-if="item.image"
              class="w-12 h-12 rounded-full overflow-hidden border-2 border-primary/20"
            >
              <img
                :src="item.image"
                :alt="item.title"
                class="w-full h-full object-cover"
              />
            </div>
            <div
              v-else
              class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold border-2 border-primary/20"
            >
              {{ (item.title || 'U')[0] }}
            </div>
            <div>
              <h4 class="font-extrabold text-primary text-sm">{{ item.title }}</h4>
              <p class="text-xs text-gray-400">{{ item.value || '' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
