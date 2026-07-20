<script setup>
import { ref, onMounted } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
const props = defineProps({ section: Object, branding: Object })
const el = ref(null)
const isVisible = ref(false)
const openIndex = ref(null)
function toggle(i) {
  openIndex.value = openIndex.value === i ? null : i
}
onMounted(() => {
  const obs = new IntersectionObserver(
    ([e]) => {
      if (e.isIntersecting) {
        isVisible.value = true
        obs.disconnect()
      }
    },
    { threshold: 0.2 }
  )
  if (el.value) obs.observe(el.value)
})
</script>

<template>
  <section
    ref="el"
    class="py-24 lg:py-32 bg-white relative"
  >
    <div class="max-w-3xl mx-auto px-6">
      <div
        :class="[
          'text-center mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-5 py-2 rounded-full text-xs font-extrabold uppercase tracking-widest mb-4 bg-orange-100 text-orange-600"
          >❓ FAQ</span
        >
        <h2 class="heading-font text-3xl md:text-4xl font-bold text-primary">
          {{ section.title || 'Pertanyaan Umum' }}
        </h2>
      </div>

      <div class="space-y-4">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          :class="[
            'rounded-2xl border-3 transition-all duration-500',
            openIndex === i
              ? 'border-accent/20 shadow-lg bg-white'
              : 'border-gray-100 bg-primary/5 hover:bg-white hover:border-primary/20',
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
          ]"
          :style="{ transitionDelay: `${i * 60}ms` }"
        >
          <button
            @click="toggle(i)"
            class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left"
          >
            <span class="font-extrabold text-primary">{{ item.title }}</span>
            <ChevronDown
              :class="[
                'w-5 h-5 flex-shrink-0 text-accent transition-transform duration-300',
                openIndex === i ? 'rotate-180' : ''
              ]"
            />
          </button>
          <Transition
            enter-active-class="transition-all duration-300"
            enter-from-class="max-h-0 opacity-0"
            enter-to-class="max-h-96 opacity-100"
            leave-active-class="transition-all duration-200"
            leave-from-class="max-h-96 opacity-100"
            leave-to-class="max-h-0 opacity-0"
          >
            <div
              v-if="openIndex === i"
              class="overflow-hidden"
            >
              <div class="px-6 pb-5 text-gray-500 leading-relaxed">{{ item.description }}</div>
            </div>
          </Transition>
        </div>
      </div>
    </div>
  </section>
</template>
