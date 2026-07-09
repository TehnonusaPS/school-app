<script setup>
import { ref, onMounted } from 'vue'
import { Eye, Target, CheckCircle } from 'lucide-vue-next'
const props = defineProps({ about: Object, branding: Object })
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
    { threshold: 0.2 }
  )
  if (el.value) obs.observe(el.value)
})
</script>

<template>
  <section
    id="about"
    ref="el"
    class="py-24 lg:py-32 bg-[#fefcf3] islamic-pattern relative overflow-hidden"
  >
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-4 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4 bg-emerald-50 text-emerald-700"
          >Tentang Kami</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-emerald-900 mb-6">
          {{ about.title || 'Tentang Sekolah Kami' }}
        </h2>
        <p class="text-lg text-gray-600 leading-relaxed">{{ about.description }}</p>
      </div>

      <!-- Arabesque divider -->
      <div class="arabesque-divider max-w-xs mx-auto my-12"></div>

      <div class="grid lg:grid-cols-2 gap-16 items-center">
        <div
          :class="[
            'transition-all duration-700 delay-200',
            isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'
          ]"
        >
          <div class="relative">
            <div
              class="absolute -inset-3 rounded-3xl bg-gradient-to-br from-emerald-200/40 to-amber-200/40 blur-xl"
            ></div>
            <!-- Ornamental border frame -->
            <div
              class="relative rounded-3xl overflow-hidden border-4 border-amber-500/20 shadow-2xl"
            >
              <img
                v-if="about.image"
                :src="about.image"
                alt="Tentang"
                class="w-full aspect-[4/3] object-cover"
              />
              <div
                v-else
                class="w-full aspect-[4/3] bg-gradient-to-br from-emerald-100 to-amber-50 flex items-center justify-center"
              >
                <span class="text-6xl">🕌</span>
              </div>
            </div>
          </div>
        </div>

        <div
          :class="[
            'space-y-8 transition-all duration-700 delay-300',
            isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'
          ]"
        >
          <div
            v-if="about.vision"
            class="bg-white rounded-2xl p-8 shadow-lg border border-emerald-100"
          >
            <div class="flex items-start gap-4">
              <div
                class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center flex-shrink-0"
              >
                <Eye class="w-6 h-6 text-white" />
              </div>
              <div>
                <h3 class="text-xl font-bold text-emerald-900 mb-2">Visi</h3>
                <p class="text-gray-600 leading-relaxed">{{ about.vision }}</p>
              </div>
            </div>
          </div>

          <div
            v-if="about.mission?.length"
            class="bg-white rounded-2xl p-8 shadow-lg border border-emerald-100"
          >
            <div class="flex items-start gap-4">
              <div
                class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center flex-shrink-0"
              >
                <Target class="w-6 h-6 text-white" />
              </div>
              <div class="flex-1">
                <h3 class="text-xl font-bold text-emerald-900 mb-4">Misi</h3>
                <ul class="space-y-3">
                  <li
                    v-for="(item, i) in about.mission"
                    :key="i"
                    class="flex items-start gap-3"
                  >
                    <CheckCircle class="w-5 h-5 flex-shrink-0 mt-0.5 text-emerald-600" />
                    <span class="text-gray-600">{{ item }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
