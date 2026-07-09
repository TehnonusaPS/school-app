<script setup>
import { ref, onMounted } from 'vue'
import ImageLightbox from '../../components/ImageLightbox.vue'
const props = defineProps({ section: Object, branding: Object })
const el = ref(null)
const isVisible = ref(false)
const lightboxOpen = ref(false)
const lightboxIndex = ref(0)
const images = ref([])
onMounted(() => {
  images.value = (props.section.items || []).map(i => ({ url: i.image, title: i.title }))
  const obs = new IntersectionObserver(
    ([e]) => {
      if (e.isIntersecting) {
        isVisible.value = true
        obs.disconnect()
      }
    },
    { threshold: 0.1 }
  )
  if (el.value) obs.observe(el.value)
})
function openLightbox(i) {
  lightboxIndex.value = i
  lightboxOpen.value = true
}
</script>

<template>
  <section
    id="gallery"
    ref="el"
    class="py-24 lg:py-32 bg-white relative"
  >
    <div class="max-w-7xl mx-auto px-6">
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-5 py-2 rounded-full text-xs font-extrabold uppercase tracking-widest mb-4 bg-cyan-100 text-cyan-600"
          >📸 Galeri</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-purple-800 mb-6">
          {{ section.title || 'Galeri Kegiatan' }}
        </h2>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'group cursor-pointer transition-all duration-500 fun-wiggle',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 60}ms` }"
          @click="openLightbox(i)"
        >
          <div
            class="rounded-3xl overflow-hidden border-3 border-gray-100 hover:border-yellow-400 shadow-lg hover:shadow-xl transition-all"
          >
            <div class="relative overflow-hidden aspect-[4/3]">
              <img
                v-if="item.image"
                :src="item.image"
                :alt="item.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
              />
              <div
                v-else
                class="w-full h-full bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center"
              >
                <span class="text-5xl">🖼️</span>
              </div>
              <div
                class="absolute inset-0 bg-purple-900/0 group-hover:bg-purple-900/20 transition-colors flex items-center justify-center"
              >
                <div
                  class="w-14 h-14 rounded-full bg-yellow-400 flex items-center justify-center opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-100 transition-all text-2xl"
                >
                  🔍
                </div>
              </div>
            </div>
            <div
              v-if="item.title"
              class="px-5 py-4 bg-white"
            >
              <h4 class="text-sm font-extrabold text-purple-800">{{ item.title }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ImageLightbox
      v-model="lightboxOpen"
      :images="images"
      :startIndex="lightboxIndex"
    />
  </section>
</template>
