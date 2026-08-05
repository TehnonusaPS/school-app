<script setup>
import { ref, onMounted } from 'vue'
import { Trophy } from 'lucide-vue-next'

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
    class="py-24 lg:py-32 bg-white relative"
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
          :style="{ color: branding.secondaryColor, background: branding.secondaryColor + '15' }"
        >
          Prestasi
        </span>
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900">
          {{ section.title || 'Prestasi Kami' }}
        </h2>
      </div>

      <!-- Achievement Timeline -->
      <div class="max-w-4xl mx-auto space-y-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'flex gap-6 items-start transition-all duration-500',
            isVisible ? 'opacity-100 translate-x-0' : 'opacity-0',
            i % 2 === 0 ? (isVisible ? '' : '-translate-x-12') : isVisible ? '' : 'translate-x-12'
          ]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <!-- Icon -->
          <div
            class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg"
            :style="{
              background: `linear-gradient(135deg, ${branding.secondaryColor}, ${branding.primaryColor})`
            }"
          >
            <Trophy class="w-6 h-6 text-white" />
          </div>
          <!-- Content -->
          <div class="flex-1 bg-gray-50 rounded-2xl p-6 border border-gray-100">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h3 class="font-bold text-gray-900 text-lg">{{ item.title }}</h3>
                <p class="text-gray-500 text-sm mt-1 leading-relaxed">{{ item.description }}</p>
              </div>
              <span
                v-if="item.value"
                class="px-3 py-1 rounded-full text-xs font-bold flex-shrink-0"
                :style="{ color: branding.primaryColor, background: branding.primaryColor + '15' }"
              >
                {{ item.value }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
