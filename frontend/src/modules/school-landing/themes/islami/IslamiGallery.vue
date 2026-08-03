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
    class="py-24 lg:py-32 bg-[#fefcf3] relative"
  >
    <div class="max-w-7xl mx-auto px-6">
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-4 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-primary/5 text-primary"
          >Galeri</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-primary mb-6">
          {{ section.title || 'Galeri' }}
        </h2>
      </div>
      <div class="arabesque-divider max-w-xs mx-auto mb-16"></div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'group cursor-pointer transition-all duration-500',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 60}ms` }"
          @click="openLightbox(i)"
        >
          <div
            class="rounded-2xl overflow-hidden border-2 border-secondary/20 shadow-lg hover:shadow-xl hover:border-secondary/20 transition-all"
          >
            <div class="relative overflow-hidden aspect-[4/3]">
              <img
                v-if="item.image"
                :src="item.image"
                :alt="item.title || 'Gallery'"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
              />
              <div
                v-else
                class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/5 flex items-center justify-center"
              >
                <span class="text-4xl">🖼️</span>
              </div>
              <div
                class="absolute inset-0 bg-primary/0 group-hover:bg-primary/40 transition-colors flex items-center justify-center"
              >
                <div
                  class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100 transition-all"
                >
                  <svg
                    class="w-5 h-5 text-white"
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
              <h4 class="text-sm font-semibold text-primary">{{ item.title }}</h4>
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
