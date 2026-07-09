<script setup>
import { MapPin, Phone, Mail, Instagram, Facebook, Youtube } from 'lucide-vue-next'
const props = defineProps({ branding: Object, contact: Object, social: Object, data: Object })
</script>

<template>
  <footer
    id="contact"
    class="bg-emerald-950 text-white pt-20 pb-8 relative"
  >
    <div class="absolute inset-0 islamic-pattern opacity-10"></div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
        <div class="lg:col-span-2">
          <div class="flex items-center gap-3 mb-6">
            <div
              v-if="branding.logo"
              class="w-12 h-12 rounded-xl overflow-hidden border-2 border-amber-500/30"
            >
              <img
                :src="branding.logo"
                alt="Logo"
                class="w-full h-full object-cover"
              />
            </div>
            <div
              v-else
              class="w-12 h-12 rounded-xl bg-emerald-800 border-2 border-amber-500/30 flex items-center justify-center font-bold text-xl text-amber-400"
            >
              {{ (data?.meta_title || 'S')[0] }}
            </div>
            <div>
              <h3 class="font-bold text-lg">{{ data?.meta_title || 'Sekolah Kami' }}</h3>
              <p class="text-amber-300/60 text-xs">{{ data?.hero_subtitle || '' }}</p>
            </div>
          </div>
          <p class="text-gray-400 leading-relaxed max-w-md text-sm">
            {{ data?.meta_description || '' }}
          </p>
        </div>

        <div>
          <h4 class="font-bold text-sm uppercase tracking-widest text-amber-400/70 mb-6">Kontak</h4>
          <div class="space-y-4 text-sm">
            <a
              v-if="contact.email"
              :href="`mailto:${contact.email}`"
              class="flex items-center gap-3 text-gray-300 hover:text-amber-300 transition-colors"
              ><Mail class="w-4 h-4 text-amber-500" />{{ contact.email }}</a
            >
            <a
              v-if="contact.phone"
              :href="`tel:${contact.phone}`"
              class="flex items-center gap-3 text-gray-300 hover:text-amber-300 transition-colors"
              ><Phone class="w-4 h-4 text-amber-500" />{{ contact.phone }}</a
            >
            <div
              v-if="contact.address"
              class="flex items-start gap-3 text-gray-300"
            >
              <MapPin class="w-4 h-4 text-amber-500 mt-0.5" />{{ contact.address }}
            </div>
          </div>
        </div>

        <div>
          <h4 class="font-bold text-sm uppercase tracking-widest text-amber-400/70 mb-6">
            Ikuti Kami
          </h4>
          <div class="flex gap-3">
            <a
              v-if="social.instagram"
              :href="social.instagram"
              target="_blank"
              class="w-10 h-10 rounded-xl bg-white/10 hover:bg-amber-500/20 flex items-center justify-center transition-colors"
              ><Instagram class="w-5 h-5"
            /></a>
            <a
              v-if="social.facebook"
              :href="social.facebook"
              target="_blank"
              class="w-10 h-10 rounded-xl bg-white/10 hover:bg-amber-500/20 flex items-center justify-center transition-colors"
              ><Facebook class="w-5 h-5"
            /></a>
            <a
              v-if="social.youtube"
              :href="social.youtube"
              target="_blank"
              class="w-10 h-10 rounded-xl bg-white/10 hover:bg-amber-500/20 flex items-center justify-center transition-colors"
              ><Youtube class="w-5 h-5"
            /></a>
          </div>
        </div>
      </div>

      <div
        v-if="contact.mapsEmbed"
        class="mb-16 rounded-2xl overflow-hidden border-2 border-amber-500/20"
      >
        <iframe
          :src="contact.mapsEmbed"
          class="w-full h-64 border-0"
          allowfullscreen
          loading="lazy"
        ></iframe>
      </div>

      <div class="border-t border-emerald-800 pt-8">
        <div
          class="flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-500"
        >
          <p>&copy; {{ new Date().getFullYear() }} {{ data?.meta_title || 'Sekolah Kami' }}</p>
          <p>Powered by <span class="text-amber-400/70 font-semibold">Sekolahku ERP</span></p>
        </div>
      </div>
    </div>
  </footer>
</template>
