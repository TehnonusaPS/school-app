<script setup>
import { ref, onMounted } from 'vue'
import ImageLightbox from '../../components/ImageLightbox.vue'
import { getDefaultImage } from '../../composables/useDefaultImages'

const props = defineProps({
  section: Object,
  branding: Object
})

const el = ref(null)
const isVisible = ref(false)
const lightboxOpen = ref(false)
const lightboxIndex = ref(0)

const images = ref([])

onMounted(() => {
  images.value = (props.section.items || []).map(item => ({
    url: item.image,
    title: item.title
  }))

  const observer = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        isVisible.value = true
        observer.disconnect()
      }
    },
    { threshold: 0.1 }
  )
  if (el.value) observer.observe(el.value)
})

function openLightbox(index) {
  lightboxIndex.value = index
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
      <!-- Header -->
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4"
          :style="{ color: branding.primaryColor, background: branding.primaryColor + '15' }"
        >
          Galeri
        </span>
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
          {{ section.title || 'Galeri Kegiatan' }}
        </h2>
        <p
          v-if="section.subtitle"
          class="text-lg text-gray-500 leading-relaxed"
        >
          {{ section.subtitle }}
        </p>
      </div>

      <!-- Masonry-like Grid -->
      <div class="flex flex-wrap justify-center gap-6">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'w-full sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)] group cursor-pointer transition-all duration-500',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 60}ms` }"
          @click="openLightbox(i)"
        >
          <div
            class="rounded-2xl overflow-hidden shadow-lg shadow-gray-100/50 hover:shadow-xl transition-shadow"
          >
            <div class="relative overflow-hidden">
              <img
                v-if="item.image"
                :src="item.image"
                :alt="item.title || 'Gallery'"
                class="w-full object-cover group-hover:scale-105 transition-transform duration-700"
                :class="[
                  i % 3 === 0 ? 'aspect-[4/3]' : i % 3 === 1 ? 'aspect-square' : 'aspect-[3/4]'
                ]"
              />
              <div
                v-else
                class="relative w-full overflow-hidden"
                :class="[
                  i % 3 === 0 ? 'aspect-[4/3]' : i % 3 === 1 ? 'aspect-square' : 'aspect-[3/4]'
                ]"
              >
                <img
                  :src="getDefaultImage('gallery', i)"
                  :alt="item.title || 'Kegiatan Sekolah'"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                />
                <div
                  class="absolute inset-0"
                  :style="{ background: `linear-gradient(135deg, ${branding.primaryColor}20, ${branding.accentColor}10)` }"
                />
              </div>
              <!-- Hover overlay -->
              <div
                class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center"
              >
                <div
                  class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all"
                >
                  <svg
                    class="w-5 h-5 text-gray-800"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"
                    />
                  </svg>
                </div>
              </div>
            </div>
            <div
              v-if="item.title"
              class="px-5 py-4 bg-white"
            >
              <h4 class="text-sm font-semibold text-gray-800">{{ item.title }}</h4>
              <p
                v-if="item.description"
                class="text-xs text-gray-400 mt-1"
              >
                {{ item.description }}
              </p>
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
