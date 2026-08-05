<script setup>
/**
 * IslamiTheme — Root wrapper for the "Islami Elegant" theme.
 * Warm, Islamic-inspired design with geometric patterns and arabesque ornaments.
 */
import IslamiNavbar from './IslamiNavbar.vue'
import IslamiHero from './IslamiHero.vue'
import IslamiAbout from './IslamiAbout.vue'
import IslamiFooter from './IslamiFooter.vue'
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
  <div class="islami-theme font-sans antialiased text-gray-800 bg-cream-50 overflow-x-hidden">
    <IslamiNavbar
      :branding="branding"
      :data="data"
      :sections="sections"
    />
    <IslamiHero
      :hero="hero"
      :branding="branding"
    />
    <IslamiAbout
      v-if="about?.title || about?.description || about?.vision || (about?.mission && about.mission.length) || about?.image"
      :about="about"
      :branding="branding"
    />

    <template
      v-for="section in sections"
      :key="section.id"
    >
      <SectionRenderer
        :section="section"
        theme="islami"
        :branding="branding"
      />
    </template>

    <IslamiFooter
      :branding="branding"
      :contact="contact"
      :social="social"
      :data="data"
    />
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Poppins:wght@300;400;500;600;700;800&display=swap');

.islami-theme {
  --primary: var(--lp-primary, #047857);
  --secondary: var(--lp-secondary, #d97706);
  --accent: var(--lp-accent, #065f46);
  font-family: 'Poppins', system-ui, sans-serif;
}

.islami-theme .heading-font {
  font-family: 'Amiri', serif;
}

.bg-cream-50, .islami-theme.bg-cream-50 {
  background-color: #fefcf3;
}

/* Islamic geometric pattern (SVG data URI) */
.islami-theme .islamic-pattern {
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23047857' fill-opacity='0.06'%3E%3Cpath d='M30 0L60 30L30 60L0 30z M30 8L52 30L30 52L8 30z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

/* Arabesque divider */
.islami-theme .arabesque-divider {
  height: 3px;
  background: linear-gradient(
    90deg,
    transparent 0%,
    var(--lp-secondary, #d97706) 20%,
    var(--lp-primary, #047857) 50%,
    var(--lp-secondary, #d97706) 80%,
    transparent 100%
  );
  border-radius: 2px;
}
</style>
