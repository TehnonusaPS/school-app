<script setup>
import { useRoute } from 'vue-router'
import { useLandingPage } from '../composables/useLandingPage'
import { defineAsyncComponent, computed, watch } from 'vue'

const route = useRoute()
const slug = route.params.slug

const {
  data,
  loading,
  error,
  theme,
  heroData,
  aboutData,
  brandingData,
  contactData,
  socialData,
  sections,
  getSectionByType
} = useLandingPage(slug)

// Dynamic theme component mapping
const themeComponents = {
  modern: defineAsyncComponent(() => import('../themes/modern/ModernTheme.vue')),
  islami: defineAsyncComponent(() => import('../themes/islami/IslamiTheme.vue')),
  playful: defineAsyncComponent(() => import('../themes/playful/PlayfulTheme.vue'))
}

const ThemeComponent = computed(() => themeComponents[theme.value] || themeComponents.modern)

// Apply custom CSS variables from branding
watch(
  () => brandingData.value,
  branding => {
    if (branding) {
      document.documentElement.style.setProperty('--lp-primary', branding.primaryColor)
      document.documentElement.style.setProperty('--lp-secondary', branding.secondaryColor)
      document.documentElement.style.setProperty('--lp-accent', branding.accentColor)
    }
  },
  { immediate: true }
)

// Set page title & meta tags
watch(
  () => data.value,
  pageData => {
    if (pageData?.meta_title) {
      document.title = pageData.meta_title
    }
    // Inject meta description for SEO
    if (pageData?.meta_description) {
      let metaDesc = document.querySelector('meta[name="description"]')
      if (!metaDesc) {
        metaDesc = document.createElement('meta')
        metaDesc.setAttribute('name', 'description')
        document.head.appendChild(metaDesc)
      }
      metaDesc.setAttribute('content', pageData.meta_description)
    }
    // Inject Open Graph tags for social media sharing
    const ogTags = {
      'og:title': pageData?.meta_title || '',
      'og:description': pageData?.meta_description || '',
      'og:type': 'website',
      'og:image': pageData?.hero_images?.[0]?.url || pageData?.about_image || ''
    }
    for (const [property, content] of Object.entries(ogTags)) {
      if (!content) continue
      let tag = document.querySelector(`meta[property="${property}"]`)
      if (!tag) {
        tag = document.createElement('meta')
        tag.setAttribute('property', property)
        document.head.appendChild(tag)
      }
      tag.setAttribute('content', content)
    }
  },
  { immediate: true }
)
</script>

<template>
  <!-- Loading State -->
  <div
    v-if="loading"
    class="min-h-screen flex items-center justify-center bg-gray-50"
  >
    <div class="text-center">
      <div class="inline-flex items-center gap-3">
        <div
          class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
        ></div>
        <span class="text-lg text-gray-600 font-medium">Memuat halaman...</span>
      </div>
    </div>
  </div>

  <!-- Error State -->
  <div
    v-else-if="error"
    class="min-h-screen flex items-center justify-center bg-gray-50"
  >
    <div class="text-center max-w-md mx-auto px-6">
      <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-red-50 flex items-center justify-center">
        <svg
          class="w-10 h-10 text-red-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
          />
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-800 mb-2">Halaman Tidak Ditemukan</h2>
      <p class="text-gray-500">{{ error }}</p>
    </div>
  </div>

  <!-- Theme Renderer -->
  <component
    v-else-if="data"
    :is="ThemeComponent"
    :data="data"
    :hero="heroData"
    :about="aboutData"
    :branding="brandingData"
    :contact="contactData"
    :social="socialData"
    :sections="sections"
    :getSectionByType="getSectionByType"
  />
</template>
