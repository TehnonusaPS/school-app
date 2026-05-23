<script setup>
import { onMounted } from 'vue'

import LandingNavbar from '@/components/landing/LandingNavbar.vue'
import LandingHeroSection from '@/components/landing/LandingHeroSection.vue'
import WaveSection from '@/components/landing/WaveSection.vue'
import LandingFeaturesSection from '@/components/landing/LandingFeaturesSection.vue'
import LandingModulesSection from '@/components/landing/LandingModulesSection.vue'
import LandingPricingSection from '@/components/landing/LandingPricingSection.vue'
import LandingTestimonialsSection from '@/components/landing/LandingTestimonialsSection.vue'
import LandingCtaSection from '@/components/landing/LandingCtaSection.vue'
import LandingFooter from '@/components/landing/LandingFooter.vue'
import LandingChatWidget from '@/components/landing/LandingChatWidget.vue'

onMounted(() => {
  // Pastikan class theme-emerald ada di body agar ambient gradients dan styles menyala indah
  if (!document.body.classList.contains('theme-emerald')) {
    document.body.classList.add('theme-emerald')
  }
})
</script>

<template>
  <div class="font-sans antialiased text-foreground bg-background">
    <!-- Navbar (floating glass) -->
    <LandingNavbar brandName="Sekolahku ERP" loginPath="/login" />

    <!-- Hero Section -->
    <LandingHeroSection />

    <!-- Wave Carousel (visual asset showcaser & value list) -->
    <WaveSection />

    <!-- Fitur Section -->
    <LandingFeaturesSection />

    <!-- Modul Section -->
    <LandingModulesSection />

    <!-- Harga Section -->
    <LandingPricingSection />

    <!-- Testimoni Section -->
    <LandingTestimonialsSection />

    <!-- CTA Section -->
    <LandingCtaSection />

    <!-- Footer Section -->
    <LandingFooter brandName="Sekolahku ERP" />

    <!-- Live Floating WhatsApp Chat Helper -->
    <LandingChatWidget />
  </div>
</template>

