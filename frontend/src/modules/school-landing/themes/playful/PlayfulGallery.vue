<script setup>
import { ref, onMounted } from 'vue'
import { Image as ImageIcon, Search } from 'lucide-vue-next'
import ImageLightbox from '../../components/ImageLightbox.vue'
import { getDefaultImage } from '../../composables/useDefaultImages'
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
          class="inline-block px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-primary/10 text-primary"
          >Galeri</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-primary mb-6">
          {{ section.title || 'Galeri Kegiatan' }}
        </h2>
      </div>

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
            class="rounded-[2rem] overflow-hidden bg-white shadow-xl shadow-gray-200/50 border border-gray-100 hover:border-primary/20 hover:shadow-2xl hover:shadow-primary/10 transition-all hover:-translate-y-2"
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
                class="relative w-full h-full overflow-hidden"
              >
                <img
                  :src="getDefaultImage('gallery', i)"
                  :alt="item.title || 'Kegiatan Sekolah'"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                />
                <div class="absolute inset-0 bg-gradient-to-br from-primary/15 to-secondary/10" />
              </div>
              <div
                class="absolute inset-0 bg-primary/50 opacity-0 group-hover:opacity-100 transition-colors flex items-center justify-center backdrop-blur-sm"
              >
                <div
                  class="w-14 h-14 rounded-full bg-white flex items-center justify-center scale-50 group-hover:scale-100 transition-transform duration-300 shadow-xl"
                >
                  <Search class="w-6 h-6 text-primary" />
                </div>
              </div>
            </div>
            <div
              v-if="item.title"
              class="px-5 py-4 bg-white"
            >
              <h4 class="text-sm font-extrabold text-primary">{{ item.title }}</h4>
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
