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
  // Paksa halaman landing selalu dalam light mode
  document.documentElement.classList.remove('dark')

  // Bersihkan semua class tema atau background yang mungkin terbawa dari dashboard
  document.body.classList.forEach(cls => {
    if (cls.startsWith('theme-') || ['bg-animated', 'bg-static_squares', 'bg-glass', 'bg-solid'].includes(cls)) {
      document.body.classList.remove(cls)
    }
  })

  // Gunakan tema yang tersimpan di localStorage (default biru)
  const savedThemeStyle = localStorage.getItem('themeStyle') || 'blue'
  const mappedStyle = savedThemeStyle === 'tahoe' ? 'blue' : savedThemeStyle
  document.body.classList.add(`theme-${mappedStyle}`)

  // Terapkan juga background style yang tersimpan
  const savedBgStyle = localStorage.getItem('backgroundStyle') || 'animated'
  document.body.classList.add(`bg-${savedBgStyle}`)
})
</script>

<template>
  <div class="font-sans antialiased text-foreground bg-background overflow-x-hidden">
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

