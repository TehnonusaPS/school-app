<script setup>
/**
 * ModernTheme — Root wrapper for the "Modern Akademik" theme.
 * Clean, minimalist, professional design with glassmorphism and parallax.
 */
import ModernNavbar from './ModernNavbar.vue'
import ModernHero from './ModernHero.vue'
import ModernAbout from './ModernAbout.vue'
import ModernFooter from './ModernFooter.vue'
import SectionRenderer from '../../components/SectionRenderer.vue'

const props = defineProps({
  data: Object,
  hero: Object,
  about: Object,
  branding: Object,
  contact: Object,
  social: Object,
  sections: Array,
  getSectionByType: Function
})
</script>

<template>
  <div class="modern-theme font-sans antialiased text-gray-800 bg-white overflow-x-hidden">
    <ModernNavbar
      :branding="branding"
      :data="data"
      :sections="sections"
    />
    <ModernHero
      :hero="hero"
      :branding="branding"
    />
    <ModernAbout
      v-if="about?.title || about?.description || about?.vision || (about?.mission && about.mission.length) || about?.image"
      :about="about"
      :branding="branding"
    />

    <!-- Dynamic Sections -->
    <template
      v-for="section in sections"
      :key="section.id"
    >
      <SectionRenderer
        :section="section"
        theme="modern"
        :branding="branding"
      />
    </template>

    <ModernFooter
      :branding="branding"
      :contact="contact"
      :social="social"
      :data="data"
    />
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@600;700;800&display=swap');

.modern-theme {
  --primary: var(--lp-primary, #1e40af);
  --secondary: var(--lp-secondary, #f59e0b);
  --accent: var(--lp-accent, #0ea5e9);
  font-family: 'Inter', system-ui, sans-serif;
}

.modern-theme .heading-font {
  font-family: 'Playfair Display', serif;
}

/* Scroll reveal animation */
.modern-theme .reveal {
  opacity: 0;
  transform: translateY(40px);
  transition:
    opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1),
    transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}
.modern-theme .reveal.visible {
  opacity: 1;
  transform: translateY(0);
}
</style>
