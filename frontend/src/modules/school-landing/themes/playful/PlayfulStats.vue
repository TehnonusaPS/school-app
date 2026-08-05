<script setup>
import { ref, onMounted } from 'vue'
import { TrendingUp } from 'lucide-vue-next'
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
    class="py-20 bg-gradient-to-r from-primary via-secondary to-accent/50 relative overflow-hidden"
  >
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-5 left-[10%] w-32 h-32 rounded-full bg-white/20 blur-[50px]"></div>
      <div class="absolute bottom-5 right-[15%] w-48 h-48 rounded-full bg-white/20 blur-[60px]"></div>
    </div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <h2
        v-if="section.title"
        class="heading-font text-3xl md:text-4xl font-bold text-white text-center mb-12"
      >
        {{ section.title }}
      </h2>
      <div
        class="flex flex-wrap justify-center gap-8"
        :class="isVisible ? 'opacity-100' : 'opacity-0'"
        style="transition: opacity 0.6s"
      >
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          class="w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1rem)] max-w-[280px] text-center bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:-translate-y-2"
        >
          <div class="flex justify-center mb-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
              <TrendingUp class="w-6 h-6 text-white" />
            </div>
          </div>
          <div class="text-3xl md:text-4xl font-extrabold text-white mb-2">
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
          <div class="text-white/80 text-sm font-bold">{{ item.title }}</div>
        </div>
      </div>
    </div>
  </section>
</template>
