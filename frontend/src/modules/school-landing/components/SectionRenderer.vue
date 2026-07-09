<script setup>
/**
 * SectionRenderer — Dynamically renders the correct theme section component
 * based on section.type and current theme.
 */
import { defineAsyncComponent, computed } from 'vue'

const props = defineProps({
  section: { type: Object, required: true },
  theme: { type: String, default: 'modern' },
  branding: { type: Object, default: () => ({}) }
})

// Map section types to theme-specific components
const themeComponentMap = {
  modern: {
    stats: defineAsyncComponent(() => import('../themes/modern/ModernStats.vue')),
    features: defineAsyncComponent(() => import('../themes/modern/ModernFeatures.vue')),
    programs: defineAsyncComponent(() => import('../themes/modern/ModernPrograms.vue')),
    facilities: defineAsyncComponent(() => import('../themes/modern/ModernGallery.vue')),
    gallery: defineAsyncComponent(() => import('../themes/modern/ModernGallery.vue')),
    testimonials: defineAsyncComponent(() => import('../themes/modern/ModernTestimonials.vue')),
    achievements: defineAsyncComponent(() => import('../themes/modern/ModernAchievements.vue')),
    teachers: defineAsyncComponent(() => import('../themes/modern/ModernTeachers.vue')),
    faq: defineAsyncComponent(() => import('../themes/modern/ModernFaq.vue')),
    registration_cta: defineAsyncComponent(() => import('../themes/modern/ModernCta.vue'))
  },
  islami: {
    stats: defineAsyncComponent(() => import('../themes/islami/IslamiStats.vue')),
    features: defineAsyncComponent(() => import('../themes/islami/IslamiFeatures.vue')),
    programs: defineAsyncComponent(() => import('../themes/islami/IslamiPrograms.vue')),
    facilities: defineAsyncComponent(() => import('../themes/islami/IslamiGallery.vue')),
    gallery: defineAsyncComponent(() => import('../themes/islami/IslamiGallery.vue')),
    testimonials: defineAsyncComponent(() => import('../themes/islami/IslamiTestimonials.vue')),
    achievements: defineAsyncComponent(() => import('../themes/islami/IslamiAchievements.vue')),
    teachers: defineAsyncComponent(() => import('../themes/islami/IslamiTeachers.vue')),
    faq: defineAsyncComponent(() => import('../themes/islami/IslamiFaq.vue')),
    registration_cta: defineAsyncComponent(() => import('../themes/islami/IslamiCta.vue'))
  },
  playful: {
    stats: defineAsyncComponent(() => import('../themes/playful/PlayfulStats.vue')),
    features: defineAsyncComponent(() => import('../themes/playful/PlayfulFeatures.vue')),
    programs: defineAsyncComponent(() => import('../themes/playful/PlayfulPrograms.vue')),
    facilities: defineAsyncComponent(() => import('../themes/playful/PlayfulGallery.vue')),
    gallery: defineAsyncComponent(() => import('../themes/playful/PlayfulGallery.vue')),
    testimonials: defineAsyncComponent(() => import('../themes/playful/PlayfulTestimonials.vue')),
    achievements: defineAsyncComponent(() => import('../themes/playful/PlayfulAchievements.vue')),
    teachers: defineAsyncComponent(() => import('../themes/playful/PlayfulTeachers.vue')),
    faq: defineAsyncComponent(() => import('../themes/playful/PlayfulFaq.vue')),
    registration_cta: defineAsyncComponent(() => import('../themes/playful/PlayfulCta.vue'))
  }
}

const SectionComponent = computed(() => {
  const map = themeComponentMap[props.theme] || themeComponentMap.modern
  return map[props.section.type] || null
})
</script>

<template>
  <component
    v-if="SectionComponent"
    :is="SectionComponent"
    :section="section"
    :branding="branding"
  />
</template>
