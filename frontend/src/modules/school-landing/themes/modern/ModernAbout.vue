<script setup>
import { ref, onMounted } from 'vue'
import { Eye, Target, CheckCircle } from 'lucide-vue-next'

const props = defineProps({
  about: Object,
  branding: Object
})

const el = ref(null)
const isVisible = ref(false)

onMounted(() => {
  const observer = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        isVisible.value = true
        observer.disconnect()
      }
    },
    { threshold: 0.2 }
  )
  if (el.value) observer.observe(el.value)
})
</script>

<template>
  <section
    id="about"
    ref="el"
    class="py-24 lg:py-32 bg-slate-900 relative overflow-hidden"
  >
    <!-- Premium Mesh Background -->
    <div class="absolute inset-0 z-0 pointer-events-none">
      <div
        class="absolute top-[-10%] left-[-10%] w-3/4 h-3/4 rounded-full opacity-30 blur-[120px]"
        :style="{ background: branding.primaryColor }"
      ></div>
      <div
        class="absolute bottom-[-10%] right-[-10%] w-3/4 h-3/4 rounded-full opacity-20 blur-[120px]"
        :style="{ background: branding.accentColor }"
      ></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <!-- Section Header -->
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
          Tentang Kami
        </span>
        <h2 class="heading-font text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 drop-shadow-md">
          {{ about.title || ('Tentang ' + (branding?.entityName || 'Instansi')) }}
        </h2>
        <p class="text-lg text-white/70 leading-relaxed">
          {{
            about.description ||
            'Kami berkomitmen memberikan pendidikan terbaik untuk generasi masa depan.'
          }}
        </p>
      </div>

      <div class="grid lg:grid-cols-2 gap-16 items-stretch">
        <!-- Image -->
        <div
          :class="[
            'transition-all duration-700 delay-200 h-full',
            isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'
          ]"
        >
          <div class="relative h-full">
            <div
              class="absolute -inset-4 rounded-3xl opacity-20 blur-2xl"
              :style="{
                background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
              }"
            ></div>
            <img
              v-if="about.image"
              :src="about.image"
              alt="Tentang Sekolah"
              class="relative rounded-3xl w-full h-full min-h-[400px] object-cover shadow-2xl"
            />
            <div
              v-else
              class="relative rounded-3xl w-full h-full min-h-[400px] flex items-center justify-center shadow-2xl"
              :style="{
                background: `linear-gradient(135deg, ${branding.primaryColor}20, ${branding.accentColor}20)`
              }"
            >
              <span class="text-6xl">🏫</span>
            </div>
          </div>
        </div>

        <!-- Vision & Mission -->
        <div
          :class="[
            'space-y-8 transition-all duration-700 delay-300',
            isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'
          ]"
        >
          <!-- Vision -->
          <div
            v-if="about.vision"
            class="bg-white/5 backdrop-blur-xl rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-white/10 hover:bg-white/10 transition-colors"
          >
            <div class="flex items-start gap-4">
              <div
                class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                :style="{
                  background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
                }"
              >
                <Eye class="w-6 h-6 text-white" />
              </div>
              <div>
                <h3 class="text-xl font-bold text-white mb-2">Visi</h3>
                <p class="text-white/70 leading-relaxed">{{ about.vision }}</p>
              </div>
            </div>
          </div>

          <!-- Mission -->
          <div
            v-if="about.mission?.length"
            class="bg-white/5 backdrop-blur-xl rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-white/10 hover:bg-white/10 transition-colors"
          >
            <div class="flex items-start gap-4">
              <div
                class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                :style="{
                  background: `linear-gradient(135deg, ${branding.secondaryColor}, ${branding.primaryColor})`
                }"
              >
                <Target class="w-6 h-6 text-white" />
              </div>
              <div class="flex-1">
                <h3 class="text-xl font-bold text-white mb-4">Misi</h3>
                <ul class="space-y-3">
                  <li
                    v-for="(item, i) in about.mission"
                    :key="i"
                    class="flex items-start gap-3"
                  >
                    <CheckCircle
                      class="w-5 h-5 flex-shrink-0 mt-0.5"
                      :style="{ color: branding.primaryColor }"
                    />
                    <span class="text-white/70">{{ item }}</span>
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
