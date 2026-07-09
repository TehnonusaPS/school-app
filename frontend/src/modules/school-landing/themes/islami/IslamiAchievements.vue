<script setup>
import { ref, onMounted } from 'vue'
import { Trophy } from 'lucide-vue-next'
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
    class="py-24 lg:py-32 bg-[#fefcf3] relative"
  >
    <div class="max-w-7xl mx-auto px-6">
      <div
        :class="[
          'text-center mb-4 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-amber-50 text-amber-700"
          >Prestasi</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-emerald-900">
          {{ section.title || 'Prestasi' }}
        </h2>
      </div>
      <div class="arabesque-divider max-w-xs mx-auto mb-16"></div>

      <div class="max-w-4xl mx-auto space-y-6">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'flex gap-6 items-start transition-all duration-500',
            isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'
          ]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <div
            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center flex-shrink-0 shadow-lg"
          >
            <Trophy class="w-6 h-6 text-white" />
          </div>
          <div class="flex-1 bg-white rounded-2xl p-6 border-2 border-emerald-100/50 shadow-sm">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h3 class="font-bold text-emerald-900 text-lg">{{ item.title }}</h3>
                <p class="text-gray-500 text-sm mt-1">{{ item.description }}</p>
              </div>
              <span
                v-if="item.value"
                class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700"
                >{{ item.value }}</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
