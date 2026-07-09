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
  const num = parseInt(String(val).replace(/\D/g, ''))
  const suffix = String(val).replace(/[\d,. ]/g, '')
  return { num: isNaN(num) ? 0 : num, suffix }
}
</script>

<template>
  <section
    ref="el"
    class="py-20 relative overflow-hidden"
    :style="{
      background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
    }"
  >
    <!-- Pattern overlay -->
    <div
      class="absolute inset-0 opacity-10"
      style="
        background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0);
        background-size: 40px 40px;
      "
    ></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <h2
        v-if="section.title"
        class="heading-font text-3xl md:text-4xl font-bold text-white text-center mb-4"
      >
        {{ section.title }}
      </h2>
      <p
        v-if="section.subtitle"
        class="text-white/70 text-center mb-12 max-w-2xl mx-auto"
      >
        {{ section.subtitle }}
      </p>

      <div
        class="grid grid-cols-2 lg:grid-cols-4 gap-8"
        :class="isVisible ? 'opacity-100' : 'opacity-0'"
        style="transition: opacity 0.6s ease"
      >
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          class="text-center group"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <div class="text-4xl md:text-5xl font-extrabold text-white mb-2">
            <CounterAnimation
              v-if="isVisible"
              :target="parseValue(item.value).num"
              :suffix="parseValue(item.value).suffix"
            />
          </div>
          <div class="text-white/80 text-sm font-medium">
            {{ item.title }}
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
