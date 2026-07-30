<script setup>
import { ref, onMounted } from 'vue'
import * as LucideIcons from 'lucide-vue-next'
import { computed } from 'vue'

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

function getIcon(name) {
  if (!name) return null
  // Convert kebab-case to PascalCase
  const pascal = name.replace(/(^|-)(\w)/g, (_, __, c) => c.toUpperCase())
  return LucideIcons[pascal] || null
}
</script>

<template>
  <section
    id="features"
    ref="el"
    class="py-24 lg:py-32 bg-slate-950 relative"
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
          Keunggulan
        </span>
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 drop-shadow-sm">
          {{ section.title || 'Keunggulan Kami' }}
        </h2>
        <p
          v-if="section.subtitle"
          class="text-lg text-white/70 leading-relaxed"
        >
          {{ section.subtitle }}
        </p>
      </div>

      <!-- Feature Cards Grid -->
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'group bg-white/5 rounded-[2rem] p-8 border border-white/10 hover:bg-white/10 hover:border-white/20 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] transition-all duration-500 hover:-translate-y-2 cursor-default relative overflow-hidden',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 80}ms` }"
        >
          <!-- Icon -->
          <div
            class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 transition-transform group-hover:scale-110"
            :style="{
              background: `linear-gradient(135deg, ${branding.primaryColor}15, ${branding.accentColor}15)`
            }"
          >
            <component
              v-if="getIcon(item.icon)"
              :is="getIcon(item.icon)"
              class="w-7 h-7"
              :style="{ color: branding.primaryColor }"
            />
            <LucideIcons.CheckCircle
              v-else
              class="w-7 h-7 text-white/40"
            />
          </div>

          <!-- Subtle Glow Overlay -->
          <div 
            class="absolute inset-0 opacity-0 group-hover:opacity-[0.03] transition-opacity duration-700 pointer-events-none"
            :style="{ background: `radial-gradient(circle at 50% 0%, ${branding.primaryColor}, transparent 70%)` }"
          ></div>

          <!-- Content -->
          <h3 class="text-lg font-bold text-white mb-3 group-hover:text-white/90">
            {{ item.title }}
          </h3>
          <p class="text-white/70 text-sm leading-relaxed">
            {{ item.description }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>
