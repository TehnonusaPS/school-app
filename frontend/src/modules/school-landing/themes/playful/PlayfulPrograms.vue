<script setup>
import { ref, onMounted } from 'vue'
import { BookOpen } from 'lucide-vue-next'
const props = defineProps({ section: Object, branding: Object })
const el = ref(null)
const isVisible = ref(false)
onMounted(() => {
  const obs = new IntersectionObserver(
    ([e]) => {
      if (e.isIntersecting) {
        isVisible.value = true
        obs.disconnect()
      }
    },
    { threshold: 0.15 }
  )
  if (el.value) obs.observe(el.value)
})
</script>

<template>
  <section
    id="programs"
    ref="el"
    class="py-24 lg:py-32 bg-primary/5 relative overflow-hidden"
  >
    <div class="absolute top-0 left-0 w-[40rem] h-[40rem] rounded-full bg-primary/10 blur-[120px] pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-primary/10 text-primary"
          >Program</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-primary mb-6">
          {{ section.title || 'Program Unggulan' }}
        </h2>
      </div>

      <div class="grid md:grid-cols-2 gap-8">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'group bg-white rounded-[2rem] overflow-hidden shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-primary/10 border border-gray-100 hover:border-primary/20 transition-all duration-500 hover:-translate-y-2',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
          ]"
          :style="{ transitionDelay: `${i * 100}ms` }"
        >
          <div class="aspect-[16/10] overflow-hidden">
            <img
              v-if="item.image"
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
            />
              <div
                v-else
                class="w-full h-full bg-primary/5 flex items-center justify-center text-primary/30"
              >
                <BookOpen class="w-16 h-16" />
              </div>
          </div>
          <div class="bg-white p-6">
            <h3 class="text-lg font-extrabold text-primary mb-2">{{ item.title }}</h3>
            <p class="text-gray-500 text-sm line-clamp-2">{{ item.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
