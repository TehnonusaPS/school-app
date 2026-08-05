<script setup>
import PlayfulNavbar from './PlayfulNavbar.vue'
import PlayfulHero from './PlayfulHero.vue'
import PlayfulAbout from './PlayfulAbout.vue'
import PlayfulFooter from './PlayfulFooter.vue'
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
  <div class="playful-theme font-sans antialiased text-gray-800 bg-white overflow-x-hidden">
    <PlayfulNavbar
      :branding="branding"
      :data="data"
      :sections="sections"
    />
    <PlayfulHero
      :hero="hero"
      :branding="branding"
    />
    <PlayfulAbout
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
        theme="playful"
        :branding="branding"
      />
    </template>
    <PlayfulFooter
      :branding="branding"
      :contact="contact"
      :social="social"
      :data="data"
    />
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=Nunito:wght@400;500;600;700;800;900&display=swap');

.playful-theme {
  --primary: var(--lp-primary, #7c3aed);
  --secondary: var(--lp-secondary, #f59e0b);
  --accent: var(--lp-accent, #06b6d4);
  font-family: 'Nunito', system-ui, sans-serif;
}
.playful-theme .heading-font {
  font-family: 'Fredoka', 'Nunito', sans-serif;
}

/* Wavy section divider */
.playful-theme .wave-top {
  position: absolute;
  top: -1px;
  left: 0;
  width: 100%;
  overflow: hidden;
  line-height: 0;
}
.playful-theme .wave-top svg {
  position: relative;
  display: block;
  width: calc(100% + 1.3px);
}
.playful-theme .wave-bottom {
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
  overflow: hidden;
  line-height: 0;
  transform: rotate(180deg);
}
.playful-theme .wave-bottom svg {
  position: relative;
  display: block;
  width: calc(100% + 1.3px);
}

/* Bounce animation */
@keyframes playful-bounce {
  0%,
  100% {
    transform: translateY(0) rotate(0deg);
  }
  50% {
    transform: translateY(-12px) rotate(3deg);
  }
}
.playful-theme .fun-bounce {
  animation: playful-bounce 3s ease-in-out infinite;
}
.playful-theme .fun-bounce-2 {
  animation: playful-bounce 3.5s ease-in-out infinite;
  animation-delay: 0.5s;
}

/* Wiggle */
@keyframes wiggle {
  0%,
  100% {
    transform: rotate(0deg);
  }
  25% {
    transform: rotate(5deg);
  }
  75% {
    transform: rotate(-5deg);
  }
}
.playful-theme .fun-wiggle:hover {
  animation: wiggle 0.5s ease-in-out;
}
</style>
