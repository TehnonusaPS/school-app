<script setup>
import { MapPin, Phone, Mail, Instagram, Facebook, Youtube } from 'lucide-vue-next'

const props = defineProps({
  branding: Object,
  contact: Object,
  social: Object,
  data: Object
})
</script>

<template>
  <footer
    id="contact"
    class="bg-gray-900 text-white pt-20 pb-8"
  >
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
        <!-- Brand -->
        <div class="lg:col-span-2">
          <div class="flex items-center gap-3 mb-6">
            <div
              v-if="branding.logo"
              class="w-12 h-12 rounded-xl overflow-hidden"
            >
              <img
                :src="branding.logo"
                alt="Logo"
                class="w-full h-full object-cover"
              />
            </div>
            <div
              v-else
              class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-xl"
              :style="{
                background: `linear-gradient(135deg, ${branding.primaryColor}, ${branding.accentColor})`
              }"
            >
              {{ (data?.meta_title || 'S')[0] }}
            </div>
            <div>
              <h3 class="font-bold text-lg">{{ data?.meta_title || 'Sekolah Kami' }}</h3>
              <p class="text-gray-400 text-xs">{{ data?.hero_subtitle || '' }}</p>
            </div>
          </div>
          <p class="text-gray-400 leading-relaxed max-w-md text-sm">
            {{
              data?.meta_description ||
              'Membangun generasi unggul, berkarakter, dan berprestasi untuk masa depan Indonesia yang lebih baik.'
            }}
          </p>
        </div>

        <!-- Contact Info -->
        <div>
          <h4 class="font-bold text-sm uppercase tracking-widest text-gray-400 mb-6">Kontak</h4>
          <div class="space-y-4 text-sm">
            <a
              v-if="contact.email"
              :href="`mailto:${contact.email}`"
              class="flex items-center gap-3 text-gray-300 hover:text-white transition-colors"
            >
              <Mail
                class="w-4 h-4 flex-shrink-0"
                :style="{ color: branding.accentColor }"
              />
              {{ contact.email }}
            </a>
            <a
              v-if="contact.phone"
              :href="`tel:${contact.phone}`"
              class="flex items-center gap-3 text-gray-300 hover:text-white transition-colors"
            >
              <Phone
                class="w-4 h-4 flex-shrink-0"
                :style="{ color: branding.accentColor }"
              />
              {{ contact.phone }}
            </a>
            <div
              v-if="contact.address"
              class="flex items-start gap-3 text-gray-300"
            >
              <MapPin
                class="w-4 h-4 flex-shrink-0 mt-0.5"
                :style="{ color: branding.accentColor }"
              />
              {{ contact.address }}
            </div>
          </div>
        </div>

        <!-- Social -->
        <div>
          <h4 class="font-bold text-sm uppercase tracking-widest text-gray-400 mb-6">Ikuti Kami</h4>
          <div class="flex gap-3">
            <a
              v-if="social.instagram"
              :href="social.instagram"
              target="_blank"
              class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors"
            >
              <Instagram class="w-5 h-5" />
            </a>
            <a
              v-if="social.facebook"
              :href="social.facebook"
              target="_blank"
              class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors"
            >
              <Facebook class="w-5 h-5" />
            </a>
            <a
              v-if="social.youtube"
              :href="social.youtube"
              target="_blank"
              class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors"
            >
              <Youtube class="w-5 h-5" />
            </a>
          </div>
        </div>
      </div>

      <!-- Google Maps Embed -->
      <div
        v-if="contact.mapsEmbed"
        class="mb-16 rounded-2xl overflow-hidden"
      >
        <iframe
          :src="contact.mapsEmbed"
          class="w-full h-64 border-0"
          allowfullscreen
          loading="lazy"
        ></iframe>
      </div>

      <!-- Divider & Copyright -->
      <div class="border-t border-gray-800 pt-8">
        <div
          class="flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-500"
        >
          <p>
            &copy; {{ new Date().getFullYear() }} {{ data?.meta_title || 'Sekolah Kami' }}. All
            rights reserved.
          </p>
          <p>Powered by <span class="font-semibold text-gray-400">Sekolahku ERP</span></p>
        </div>
      </div>
    </div>
  </footer>
</template>
