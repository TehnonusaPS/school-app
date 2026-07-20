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
    class="py-24 lg:py-32 bg-primary/5 relative overflow-hidden"
  >
    <div class="absolute top-10 right-10 text-8xl opacity-5 fun-bounce">🌟</div>
    <div class="absolute bottom-10 left-10 text-8xl opacity-5 fun-bounce-2">📚</div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div
        :class="[
          'text-center max-w-3xl mx-auto mb-16 transition-all duration-700',
          isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
        ]"
      >
        <span
          class="inline-block px-5 py-2 rounded-full text-xs font-extrabold uppercase tracking-widest mb-4 bg-primary/50 text-primary"
          >🏫 Tentang Kami</span
        >
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-primary mb-6">
          {{ about.title || ('Tentang ' + (branding?.entityName || 'Instansi')) }}
        </h2>
        <p class="text-lg text-gray-500 leading-relaxed">{{ about.description }}</p>
      </div>

      <div class="grid lg:grid-cols-2 gap-16 items-center">
        <div
          :class="[
            'transition-all duration-700 delay-200',
            isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'
          ]"
        >
          <div class="relative">
            <div
              class="absolute -inset-4 rounded-[2rem] bg-gradient-to-br from-primary via-secondary to-accent/50 blur-xl"
            ></div>
            <div
              class="relative rounded-[2rem] overflow-hidden border-4 border-accent/20 shadow-2xl shadow-purple-200/30"
            >
              <img
                v-if="about.image"
                :src="about.image"
                alt="Tentang"
                class="w-full aspect-[4/3] object-cover"
              />
              <div
                v-else
                class="w-full aspect-[4/3] bg-gradient-to-br from-primary via-secondary to-accent/50 flex items-center justify-center"
              >
                <span class="text-8xl">🏫</span>
              </div>
            </div>
          </div>
        </div>

        <div
          :class="[
            'space-y-6 transition-all duration-700 delay-300',
            isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'
          ]"
        >
          <div
            v-if="about.vision"
            class="bg-white rounded-2xl p-8 shadow-lg border-2 border-primary/20 fun-wiggle"
          >
            <div class="flex items-start gap-4">
              <div
                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-secondary/50 flex items-center justify-center flex-shrink-0 text-2xl"
              >
                👁️
              </div>
              <div>
                <h3 class="text-xl font-bold text-primary mb-2">Visi</h3>
                <p class="text-gray-600 leading-relaxed">{{ about.vision }}</p>
              </div>
            </div>
          </div>

          <div
            v-if="about.mission?.length"
            class="bg-white rounded-2xl p-8 shadow-lg border-2 border-secondary/20 fun-wiggle"
          >
            <div class="flex items-start gap-4">
              <div
                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-accent to-orange-500 flex items-center justify-center flex-shrink-0 text-2xl"
              >
                🎯
              </div>
              <div class="flex-1">
                <h3 class="text-xl font-bold text-primary mb-4">Misi</h3>
                <ul class="space-y-3">
                  <li
                    v-for="(item, i) in about.mission"
                    :key="i"
                    class="flex items-start gap-3"
                  >
                    <span class="text-lg mt-0.5">✅</span>
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
