<script setup>
import { ref, onMounted } from 'vue'
import CounterAnimation from '../../components/CounterAnimation.vue'
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
    { threshold: 0.3 }
  )
  if (el.value) obs.observe(el.value)
})
function parseValue(val) {
  const strVal = String(val)
  const hasNumber = /\d/.test(strVal)
  const num = parseInt(strVal.replace(/\D/g, ''))
  const suffix = strVal.replace(/[\d,. ]/g, '')
  return { hasNumber, num: isNaN(num) ? 0 : num, suffix }
}
</script>

<template>
  <section
    :id="section.type"
    ref="el"
    class="py-20 bg-primary relative overflow-hidden"
  >
    <div class="absolute inset-0 islamic-pattern opacity-20"></div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <h2
        v-if="section.title"
        class="heading-font text-3xl md:text-4xl font-bold text-white text-center mb-4"
      >
        {{ section.title }}
      </h2>
      <div class="arabesque-divider max-w-xs mx-auto my-8"></div>
      <div
        class="flex flex-wrap justify-center gap-8"
        :class="isVisible ? 'opacity-100' : 'opacity-0'"
        style="transition: opacity 0.6s ease"
      >
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          class="w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1rem)] max-w-[280px] text-center"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <div class="text-4xl md:text-5xl font-extrabold text-secondary mb-2">
            <template v-if="parseValue(item.value).hasNumber">
              <CounterAnimation
                v-if="isVisible"
                :target="parseValue(item.value).num"
                :suffix="parseValue(item.value).suffix"
              />
            </template>
            <template v-else>
              {{ item.value }}
            </template>
          </div>
          <div class="text-white/80 text-sm font-medium">{{ item.title }}</div>
        </div>
      </div>
    </div>
  </section>
</template>
