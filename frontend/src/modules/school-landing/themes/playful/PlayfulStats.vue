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
  const num = parseInt(String(val).replace(/\D/g, ''))
  const suffix = String(val).replace(/[\d,. ]/g, '')
  return { num: isNaN(num) ? 0 : num, suffix }
}
const emojis = ['🎓', '👨‍🏫', '🏆', '📚', '⭐', '🎯', '🌟', '🎨']
</script>

<template>
  <section
    ref="el"
    class="py-20 bg-gradient-to-r from-purple-600 via-pink-500 to-yellow-400 relative overflow-hidden"
  >
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-5 left-[10%] text-4xl opacity-20 fun-bounce">⭐</div>
      <div class="absolute bottom-5 right-[15%] text-4xl opacity-20 fun-bounce-2">🎈</div>
    </div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <h2
        v-if="section.title"
        class="heading-font text-3xl md:text-4xl font-bold text-white text-center mb-12"
      >
        {{ section.title }}
      </h2>
      <div
        class="grid grid-cols-2 lg:grid-cols-4 gap-8"
        :class="isVisible ? 'opacity-100' : 'opacity-0'"
        style="transition: opacity 0.6s"
      >
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          class="text-center bg-white/15 backdrop-blur-sm rounded-3xl p-6 border border-white/20 fun-wiggle"
        >
          <div class="text-3xl mb-3">{{ emojis[i % emojis.length] }}</div>
          <div class="text-3xl md:text-4xl font-extrabold text-white mb-2">
            <CounterAnimation
              v-if="isVisible"
              :target="parseValue(item.value).num"
              :suffix="parseValue(item.value).suffix"
            />
          </div>
          <div class="text-white/80 text-sm font-bold">{{ item.title }}</div>
        </div>
      </div>
    </div>
  </section>
</template>
