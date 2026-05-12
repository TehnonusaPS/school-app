<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

import LandingNavbar   from '../components/LandingNavbar.vue'
import LandingHero     from '../components/LandingHero.vue'
import LandingStats    from '../components/LandingStats.vue'
import LandingFeatures from '../components/LandingFeatures.vue'
import LandingSolusi   from '../components/LandingSolusi.vue'
import LandingTentang  from '../components/LandingTentang.vue'
import LandingKontak   from '../components/LandingKontak.vue'
import LandingFooter   from '../components/LandingFooter.vue'

const isScrolled = ref(false)
const handleScroll = () => { isScrolled.value = window.scrollY > 50 }
onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))

const scrollTo = (id) => {
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' })
}
</script>

<template>
  <div class="font-sans text-gray-800 antialiased">
    <LandingNavbar :isScrolled="isScrolled" @scrollTo="scrollTo" />
    <LandingHero   @scrollTo="scrollTo" />
    <LandingStats  />
    <LandingFeatures />
    <LandingSolusi />
    <LandingTentang />
    <LandingKontak />
    <LandingFooter @scrollTo="scrollTo" />
  </div>
</template>
