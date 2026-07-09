<script setup>
import { ref, onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Save, Plus, Trash2 } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'

import { toast } from 'vue-sonner'

const store = useLandingEditorStore()

const form = ref({
  hero_title: '',
  hero_subtitle: '',
  hero_description: '',
  hero_cta_text: '',
  hero_cta_link: '',
  hero_images: []
})

onMounted(async () => {
  await store.fetchLandingPage()
  if (store.landingPage) {
    form.value.hero_title = store.landingPage.hero_title || ''
    form.value.hero_subtitle = store.landingPage.hero_subtitle || ''
    form.value.hero_description = store.landingPage.hero_description || ''
    form.value.hero_cta_text = store.landingPage.hero_cta_text || ''
    form.value.hero_cta_link = store.landingPage.hero_cta_link || ''
    form.value.hero_images = store.landingPage.hero_images || []
  }
})

async function handleSave() {
  try {
    await store.saveSettings(form.value)
    toast.success('Hero section berhasil disimpan!')
  } catch (err) {
    toast.error(err.message)
  }
}

async function onImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  try {
    const res = await store.uploadImage(file, 'hero')
    // Tambahkan ke list hero_images carousel
    form.value.hero_images.push({ url: res.url, caption: '' })
    toast.success('Gambar berhasil ditambahkan!')
  } catch (err) {
    toast.error(err.message)
  }
}

function removeImage(index) {
  form.value.hero_images.splice(index, 1)
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
      <!-- Carousel Gambar Hero -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Slide Gambar Carousel</h3>
        <p class="text-xs text-gray-400">
          Upload beberapa gambar sekolah terbaik Anda untuk dijadikan carousel.
        </p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div
            v-for="(img, idx) in form.hero_images"
            :key="idx"
            class="relative group rounded-xl overflow-hidden border border-gray-200 dark:border-white/10 aspect-video"
          >
            <img
              :src="img.url"
              class="w-full h-full object-cover"
            />
            <Button
              type="button"
              variant="destructive"
              size="icon"
              @click="removeImage(idx)"
              class="absolute inset-0 m-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-10 h-10"
            >
              <Trash2 class="w-5 h-5" />
            </Button>
          </div>

          <label
            class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 dark:border-white/10 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 aspect-video transition-colors"
          >
            <Plus class="w-8 h-8 text-gray-400" />
            <span class="text-xs text-gray-400 mt-2 font-bold">Tambah Gambar</span>
            <input
              type="file"
              @change="onImageUpload"
              class="sr-only"
              accept="image/*"
            />
          </label>
        </div>
      </div>

      <!-- Teks & CTA Hero -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Konten Teks</h3>
        <div class="space-y-4">
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Judul Utama (Headline)</Label
            >
            <Input
              type="text"
              v-model="form.hero_title"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              required
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Sub-judul (Sub-headline)</Label
            >
            <Input
              type="text"
              v-model="form.hero_subtitle"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Deskripsi Hero</Label
            >
            <Textarea
              v-model="form.hero_description"
              rows="3"
              class="w-full px-4 py-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
            />
          </div>
        </div>
      </div>

      <!-- Call to Action (CTA) -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Call to Action (CTA Button)</h3>
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Label Tombol</Label>
            <Input
              type="text"
              v-model="form.hero_cta_text"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="contoh: Daftar Sekarang"
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Link Tombol (URL)</Label
            >
            <Input
              type="text"
              v-model="form.hero_cta_link"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="contoh: #registration_cta atau URL external"
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
