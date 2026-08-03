<script setup>
import { ref, onMounted } from 'vue'
import { ArrowRight } from 'lucide-vue-next'

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
    { threshold: 0.15 }
  )
  if (el.value) observer.observe(el.value)
})
</script>

<template>
  <section
    id="programs"
    ref="el"
    class="py-24 lg:py-32 bg-slate-900 relative overflow-hidden"
  >
    <div
      class="absolute bottom-0 left-0 w-80 h-80 rounded-full opacity-5 blur-3xl"
      :style="{ background: branding.accentColor }"
    ></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
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
          Program
        </span>
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 drop-shadow-md">
          {{ section.title || 'Program Unggulan' }}
        </h2>
        <p
          v-if="section.subtitle"
          class="text-lg text-white/70 leading-relaxed"
        >
          {{ section.subtitle }}
        </p>
      </div>

      <!-- Programs -->
      <div class="grid md:grid-cols-2 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'group relative rounded-3xl overflow-hidden transition-all duration-500 hover:-translate-y-2',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <!-- Image -->
          <div class="aspect-[16/10] overflow-hidden">
            <img
              v-if="item.image"
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
            />
            <div
              v-else
              class="w-full h-full flex items-center justify-center"
              :style="{
                background: `linear-gradient(135deg, ${branding.primaryColor}20, ${branding.accentColor}20)`
              }"
            >
              <span class="text-5xl">{{ item.icon || '📚' }}</span>
            </div>
          </div>
          <!-- Overlay content -->
          <div class="absolute inset-x-4 bottom-4 p-6 bg-black/40 backdrop-blur-xl border border-white/20 rounded-2xl group-hover:bg-black/50 transition-colors">
            <div>
              <h3 class="text-xl font-bold text-white mb-2">{{ item.title }}</h3>
              <p class="text-white/80 text-sm leading-relaxed line-clamp-2">
                {{ item.description }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
