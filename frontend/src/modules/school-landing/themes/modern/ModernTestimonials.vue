<script setup>
import { ref, onMounted } from 'vue'
import { Quote } from 'lucide-vue-next'

const props = defineProps({
  section: Object,
  branding: Object
})

const el = ref(null)
const isVisible = ref(false)
const activeIndex = ref(0)

onMounted(() => {
  const observer = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        isVisible.value = true
        observer.disconnect()
      }
    },
    { threshold: 0.2 }
  )
  if (el.value) observer.observe(el.value)

  // Auto-slide
  if (props.section.items?.length > 1) {
    setInterval(() => {
      activeIndex.value = (activeIndex.value + 1) % props.section.items.length
    }, 5000)
  }
})
</script>

<template>
  <section
    ref="el"
    class="py-24 lg:py-32 bg-gray-50 relative overflow-hidden"
  >
    <div
      class="absolute top-0 left-0 w-96 h-96 rounded-full opacity-5 blur-3xl"
      :style="{ background: branding.secondaryColor }"
    ></div>

    <div class="max-w-7xl mx-auto px-6">
      <!-- Header -->
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4"
          :style="{ color: branding.primaryColor, background: branding.primaryColor + '15' }"
        >
          Testimoni
        </span>
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900">
          {{ section.title || 'Kata Mereka' }}
        </h2>
      </div>

      <!-- Testimonial Cards -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'bg-white rounded-2xl p-8 shadow-lg shadow-gray-100/50 border border-gray-100 transition-all duration-500 hover:shadow-xl',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <Quote
            class="w-8 h-8 mb-4 opacity-20"
            :style="{ color: branding.primaryColor }"
          />
          <p class="text-gray-600 leading-relaxed mb-6 italic">"{{ item.description }}"</p>
          <div class="flex items-center gap-4">
            <div
              v-if="item.image"
              class="w-12 h-12 rounded-full overflow-hidden"
            >
              <img
                :src="item.image"
                :alt="item.title"
                class="w-full h-full object-cover"
              />
            </div>
            <div
              v-else
              class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold"
              :style="{
                background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
              }"
            >
              {{ (item.title || 'U')[0] }}
            </div>
            <div>
              <h4 class="font-semibold text-gray-900 text-sm">{{ item.title }}</h4>
              <p class="text-xs text-gray-400">{{ item.value || item.extra_data?.role || '' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
