<script setup>
import { ref, onMounted } from 'vue'
import { getDefaultImage } from '../../composables/useDefaultImages'

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
    { threshold: 0.2 }
  )
  if (el.value) observer.observe(el.value)
})
</script>

<template>
  <section
    ref="el"
    class="py-24 lg:py-32 bg-gray-50 relative"
  >
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
          Tenaga Pendidik
        </span>
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900">
          {{ section.title || 'Tenaga Pendidik' }}
        </h2>
      </div>

      <!-- Teacher Cards -->
      <div class="flex flex-wrap justify-center gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'w-full sm:w-[calc(50%-1rem)] lg:w-[calc(25%-1.5rem)] group text-center transition-all duration-500',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 80}ms` }"
        >
          <div
            class="relative mb-5 mx-auto w-40 h-40 rounded-2xl overflow-hidden shadow-lg group-hover:shadow-xl transition-shadow"
          >
            <img
              v-if="item.image"
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div
              v-else
              class="relative w-full h-full overflow-hidden"
            >
              <img
                :src="getDefaultImage('teacher', i)"
                :alt="item.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div
                class="absolute bottom-0 inset-x-0 h-1/3"
                :style="{ background: `linear-gradient(to top, ${branding.primaryColor}60, transparent)` }"
              />
            </div>
          </div>
          <h4 class="font-bold text-gray-900">{{ item.title }}</h4>
          <p class="text-sm text-gray-400 mt-1">{{ item.description || item.value }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
