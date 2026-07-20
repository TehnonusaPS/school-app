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
const medals = ['🥇', '🥈', '🥉', '🏅', '🎖️', '🏆']
</script>

<template>
  <section
    ref="el"
    class="py-24 lg:py-32 bg-white relative"
  >
    <div class="max-w-7xl mx-auto px-6">
      <div
        :class="[
          'text-center mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-5 py-2 rounded-full text-xs font-extrabold uppercase tracking-widest mb-4 bg-accent/10 text-accent"
          >🏆 Prestasi</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-primary">
          {{ section.title || 'Prestasi' }}
        </h2>
      </div>

      <div class="max-w-4xl mx-auto space-y-6">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'flex gap-5 items-start transition-all duration-500 fun-wiggle',
            isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'
          ]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <div
            class="w-16 h-16 rounded-2xl bg-gradient-to-br from-accent to-orange-400 flex items-center justify-center flex-shrink-0 shadow-lg text-3xl"
          >
            {{ medals[i % medals.length] }}
          </div>
          <div class="flex-1 bg-primary/5 rounded-2xl p-6 border-2 border-primary/20">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h3 class="font-extrabold text-primary text-lg">{{ item.title }}</h3>
                <p class="text-gray-500 text-sm mt-1">{{ item.description }}</p>
              </div>
              <span
                v-if="item.value"
                class="px-3 py-1 rounded-full text-xs font-extrabold bg-accent/10 text-accent"
                >{{ item.value }}</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
