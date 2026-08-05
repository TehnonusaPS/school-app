import { ref, computed } from 'vue'
import { getPublicLandingPage } from '@/services/landingPageService'

export function useLandingPage(slug) {
  const data = ref(null) // Tanpa mock data
  const loading = ref(false)
  const error = ref(null)

  const theme = computed(() => data.value?.theme || 'modern')
  const entityName = computed(() => data.value?.type === 'Yayasan' ? 'Yayasan Kami' : 'Sekolah Kami')
  
  const heroData = computed(() => ({
    badgeText: data.value?.hero_badge_text || '',
    title: data.value?.hero_title || '',
    subtitle: data.value?.hero_subtitle || '',
    description: data.value?.hero_description || '',
    image: data.value?.hero_image || '',
    images: data.value?.hero_images?.length ? data.value.hero_images : [],
    ctaText: data.value?.hero_cta_text || '',
    ctaLink: data.value?.hero_cta_link || ''
  }))

  const aboutData = computed(() => ({
    title: data.value?.about_title || '',
    description: data.value?.about_description || '',
    image: data.value?.about_image || '',
    vision: data.value?.about_vision || '',
    mission: data.value?.about_mission?.length ? data.value.about_mission : []
  }))

  const brandingData = computed(() => ({
    primaryColor: data.value?.primary_color || '#1e40af',
    secondaryColor: data.value?.secondary_color || '#f59e0b',
    accentColor: data.value?.accent_color || '#06b6d4',
    logo: data.value?.logo || '',
    entityName: entityName.value
  }))

  const extractIframeSrc = (input) => {
    if (!input) return input
    const match = input.match(/src\s*=\s*"([^"]+)"/)
    return match ? match[1] : input
  }

  const contactData = computed(() => ({
    email: data.value?.contact_email || '',
    phone: data.value?.contact_phone || '',
    address: data.value?.contact_address || '',
    mapsEmbed: extractIframeSrc(data.value?.contact_maps_embed || '')
  }))

  const socialData = computed(() => ({
    instagram: data.value?.social_instagram || '',
    facebook: data.value?.social_facebook || '',
    youtube: data.value?.social_youtube || '',
    tiktok: data.value?.social_tiktok || ''
  }))

  const sections = computed(() => (data.value?.sections?.length ? data.value.sections : []))

  function getSectionByType(type) {
    return sections.value.find(s => s.type === type) || null
  }

  async function fetchData() {
    loading.value = true
    error.value = null
    try {
      const res = await getPublicLandingPage(slug)
      if (res) {
        data.value = res
      } else {
        error.value = 'Data halaman publik tidak valid.'
      }
    } catch (e) {
      console.warn('Gagal memuat API landing', e)
      error.value = e.response?.data?.error || 'Halaman Tidak Ditemukan atau Sedang Tidak Aktif.'
    } finally {
      loading.value = false
    }
  }

  fetchData()

  return {
    data,
    loading,
    error,
    theme,
    entityName,
    heroData,
    aboutData,
    brandingData,
    contactData,
    socialData,
    sections,
    getSectionByType,
    fetchData
  }
}
