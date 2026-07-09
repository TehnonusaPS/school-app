<script setup>
import { ref, onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Save } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'

import { toast } from 'vue-sonner'

const store = useLandingEditorStore()

const form = ref({
  contact_email: '',
  contact_phone: '',
  contact_address: '',
  contact_maps_embed: '',
  social_instagram: '',
  social_facebook: '',
  social_youtube: '',
  social_tiktok: ''
})

onMounted(async () => {
  await store.fetchLandingPage()
  if (store.landingPage) {
    form.value.contact_email = store.landingPage.contact_email || ''
    form.value.contact_phone = store.landingPage.contact_phone || ''
    form.value.contact_address = store.landingPage.contact_address || ''
    form.value.contact_maps_embed = store.landingPage.contact_maps_embed || ''
    form.value.social_instagram = store.landingPage.social_instagram || ''
    form.value.social_facebook = store.landingPage.social_facebook || ''
    form.value.social_youtube = store.landingPage.social_youtube || ''
    form.value.social_tiktok = store.landingPage.social_tiktok || ''
  }
})

async function handleSave() {
  try {
    await store.saveSettings(form.value)
    toast.success('Kontak dan media sosial berhasil disimpan!')
  } catch (err) {
    toast.error(err.message)
  }
}
</script>

<template>
  <BuilderLayout>
    <div
      v-if="store.loading"
      class="flex justify-center items-center py-12"
    >
      <div
        class="w-8 h-8 border-4 border-purple-500 border-t-transparent rounded-full animate-spin"
      ></div>
    </div>

    <form
      v-else
      @submit.prevent="handleSave"
      class="space-y-8"
    >
      <!-- Kontak Info Dasar -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Informasi Kontak & Lokasi</h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Email Sekolah</Label
            >
            <Input
              type="email"
              v-model="form.contact_email"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="contoh: info@sekolah.sch.id"
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Telepon / WhatsApp</Label
            >
            <Input
              type="text"
              v-model="form.contact_phone"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="contoh: +62 812-3456-7890"
            />
          </div>
          <div class="md:col-span-2">
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Alamat Lengkap</Label
            >
            <Textarea
              v-model="form.contact_address"
              rows="3"
              class="w-full px-4 py-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="Alamat jalan, kelurahan, kecamatan, kota..."
            />
          </div>
          <div class="md:col-span-2">
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Google Maps Embed URL (Iframe Src)</Label
            >
            <Input
              type="text"
              v-model="form.contact_maps_embed"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="Salin atribut src dari tag iframe Google Maps Embed..."
            />
          </div>
        </div>
      </div>

      <!-- Media Sosial -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Tautan Media Sosial</h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">TikTok URL</Label>
            <Input
              type="text"
              v-model="form.social_tiktok"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="https://tiktok.com/@sekolah"
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Instagram URL</Label>
            <Input
              type="text"
              v-model="form.social_instagram"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="https://instagram.com/sekolah"
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Facebook URL</Label>
            <Input
              type="text"
              v-model="form.social_facebook"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="https://facebook.com/sekolah"
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">YouTube URL</Label>
            <Input
              type="text"
              v-model="form.social_youtube"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="https://youtube.com/c/sekolah"
            />
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div class="flex justify-end">
        <Button
          type="submit"
          class="flex items-center gap-2 px-6 rounded-xl font-bold transition-all h-11"
          :disabled="store.saving"
        >
          <Save class="w-4 h-4" />
          {{ store.saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </Button>
      </div>
    </form>
  </BuilderLayout>
</template>
