<script setup>
import { ref, onMounted } from 'vue'
import CounterAnimation from '../../components/CounterAnimation.vue'

const props = defineProps({
  section: Object,
  branding: Object
})

const el = ref(null)
const isVisible = ref(false)

onMounted(() => {
  const observer = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        isVisible.value = true
        observer.disconnect()
      }
    },
    { threshold: 0.3 }
  )
  if (el.value) observer.observe(el.value)
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
    class="py-20 relative overflow-hidden"
    :style="{
      background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
    }"
  >
    <!-- Dark Premium Background -->
    <div class="absolute inset-0 bg-slate-900 z-0"></div>
    <div
      class="absolute inset-0 z-0 opacity-30"
      :style="{
        background: `radial-gradient(circle at 20% 80%, ${branding.primaryColor}, transparent 50%),
                     radial-gradient(circle at 80% 20%, ${branding.accentColor}, transparent 50%)`
      }"
    ></div>

    <!-- Pattern overlay -->
    <div
      class="absolute inset-0 z-0 opacity-5"
      style="
        background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0);
        background-size: 40px 40px;
      "
    ></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div class="mb-16 text-center">
        <h2
          v-if="section.title"
          class="heading-font text-3xl md:text-4xl font-bold text-white mb-4"
        >
          {{ section.title }}
        </h2>
        <p
          v-if="section.subtitle"
          class="text-white/70 max-w-2xl mx-auto font-medium"
        >
          {{ section.subtitle }}
        </p>
      </div>

      <div
        class="flex flex-wrap justify-center gap-6"
        :class="isVisible ? 'opacity-100' : 'opacity-0'"
        style="transition: opacity 0.6s ease"
      >
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          class="w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1rem)] max-w-[280px] text-center group bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2rem] hover:bg-white/10 transition-all duration-300 hover:-translate-y-1 shadow-[0_8px_30px_rgb(0,0,0,0.12)]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <div class="text-4xl md:text-5xl font-extrabold text-white mb-3 drop-shadow-lg">
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
          <div class="text-white/80 text-sm font-bold tracking-wider uppercase">
            {{ item.title }}
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
