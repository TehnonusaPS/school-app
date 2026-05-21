<script setup>
import LandingNavbar from '@/components/landing/LandingNavbar.vue'
import LandingFooter from '@/components/landing/LandingFooter.vue'
import LandingHeroSection from '@/components/landing/LandingHeroSection.vue'
import LandingFeaturesSection from '@/components/landing/LandingFeaturesSection.vue'
import LandingModulesSection from '@/components/landing/LandingModulesSection.vue'
import LandingTestimonialsSection from '@/components/landing/LandingTestimonialsSection.vue'
import LandingPricingSection from '@/components/landing/LandingPricingSection.vue'
import LandingCtaSection from '@/components/landing/LandingCtaSection.vue'
import LandingChatWidget from '@/components/landing/LandingChatWidget.vue'
import WaveSection from '@/components/landing/WaveSection.vue'
</script>

<template>
  <div class="relative min-h-screen overflow-hidden bg-background">
    <LandingNavbar />

    <main>
      <LandingHeroSection />
      <LandingFeaturesSection />
      <WaveSection />
      <LandingModulesSection />
      <LandingTestimonialsSection />
      <LandingPricingSection />
      <LandingCtaSection />
    </main>

    <LandingFooter />
    <LandingChatWidget />
  </div>
</template>
