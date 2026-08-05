<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  target: { type: Number, required: true },
  duration: { type: Number, default: 2000 },
  suffix: { type: String, default: '' },
  prefix: { type: String, default: '' }
})

const current = ref(0)
const isVisible = ref(false)
const el = ref(null)

function animate() {
  const start = performance.now()
  const step = timestamp => {
    const progress = Math.min((timestamp - start) / props.duration, 1)
    // easeOutQuart for smooth deceleration
    const eased = 1 - Math.pow(1 - progress, 4)
    current.value = Math.round(eased * props.target)
    if (progress < 1) requestAnimationFrame(step)
  }
  requestAnimationFrame(step)
}

onMounted(() => {
  const observer = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting && !isVisible.value) {
        isVisible.value = true
        animate()
        observer.disconnect()
      }
    },
    { threshold: 0.3 }
  )
  if (el.value) observer.observe(el.value)
})
</script>

<template>
  <span
    ref="el"
    class="tabular-nums"
  >
    {{ prefix }}{{ current.toLocaleString('id-ID') }}{{ suffix }}
  </span>
</template>
