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

const themeOptions = [
  {
    id: 'modern',
    name: '🔵 Modern Akademik',
    desc: 'Clean, minimalis, profesional dengan glassmorphism & parallax.'
  },
  {
    id: 'islami',
    name: '🟢 Islami Elegant',
    desc: 'Warna emerald-emas yang hangat dengan geometri ornamentik islami.'
  },
  {
    id: 'playful',
    name: '🟡 Colorful Playful',
    desc: 'Sangat cocok untuk TK/SD dengan bentuk wavy, blob, dan emoji ceria.'
  }
]

const form = ref({
  theme: 'modern',
  slug: '',
  meta_title: '',
  meta_description: '',
  primary_color: '#1e40af',
  secondary_color: '#f59e0b',
  accent_color: '#0ea5e9'
})

onMounted(async () => {
  await store.fetchLandingPage()
  if (store.landingPage) {
    form.value.theme = store.landingPage.theme || 'modern'
    form.value.slug = store.landingPage.slug || ''
    form.value.meta_title = store.landingPage.meta_title || ''
    form.value.meta_description = store.landingPage.meta_description || ''
    form.value.primary_color = store.landingPage.primary_color || '#1e40af'
    form.value.secondary_color = store.landingPage.secondary_color || '#f59e0b'
    form.value.accent_color = store.landingPage.accent_color || '#0ea5e9'
  }
})

async function handleSave() {
  try {
    await store.saveSettings(form.value)
    toast.success('Pengaturan umum berhasil disimpan!')
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
      <!-- Theme Selection -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Pilih Tema Visual</h3>
        <p class="text-xs text-gray-400">Ganti tampilan landing page sekolah secara instan.</p>
        <div class="grid md:grid-cols-3 gap-4">
          <label
            v-for="opt in themeOptions"
            :key="opt.id"
            class="flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-white/10"
            :class="form.theme === opt.id ? 'border-primary bg-primary/10' : 'border-gray-200 dark:border-white/20'"
          >
            <input
              type="radio"
              v-model="form.theme"
              :value="opt.id"
              class="sr-only"
            />
            <span class="font-extrabold text-sm text-foreground">{{ opt.name }}</span>
            <span class="text-xs text-gray-400 mt-2 leading-relaxed">{{ opt.desc }}</span>
          </label>
        </div>
      </div>

      <!-- Domain Slug -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Alamat Halaman (Slug URL)</h3>
        <div>
          <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Slug URL</Label>
          <div
            class="flex items-center rounded-xl border border-gray-200 dark:border-white/20 bg-white/50 dark:bg-background/30 overflow-hidden h-11 transition-all focus-within:border-primary/50 focus-within:ring-1 focus-within:ring-primary/50"
          >
            <span
              class="flex items-center justify-center bg-gray-50 dark:bg-white/5 text-gray-500 px-4 h-full text-sm font-medium border-r border-gray-200 dark:border-white/20"
              >/s/</span
            >
            <Input
              type="text"
              v-model="form.slug"
              class="flex-1 h-full rounded-none border-0 bg-transparent px-4 text-foreground focus-visible:ring-0 focus-visible:ring-offset-0"
              placeholder="contoh: sdit-nur-iman"
              required
            />
          </div>
        </div>
      </div>

      <!-- Branding Colors -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Warna Identitas (Branding Colors)</h3>
        <div class="grid grid-cols-3 gap-6">
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Warna Utama</Label>
            <div class="flex items-center gap-3">
              <input
                type="color"
                v-model="form.primary_color"
                class="w-11 h-11 rounded-xl border border-white/10 cursor-pointer bg-transparent"
              />
              <Input
                type="text"
                v-model="form.primary_color"
                class="flex-1 h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl uppercase text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              />
            </div>
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Warna Sekunder</Label
            >
            <div class="flex items-center gap-3">
              <input
                type="color"
                v-model="form.secondary_color"
                class="w-11 h-11 rounded-xl border border-white/10 cursor-pointer bg-transparent"
              />
              <Input
                type="text"
                v-model="form.secondary_color"
                class="flex-1 h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl uppercase text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              />
            </div>
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Warna Aksen</Label>
            <div class="flex items-center gap-3">
              <input
                type="color"
                v-model="form.accent_color"
                class="w-11 h-11 rounded-xl border border-white/10 cursor-pointer bg-transparent"
              />
              <Input
                type="text"
                v-model="form.accent_color"
                class="flex-1 h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl uppercase text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- SEO / Meta Tags -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Meta SEO</h3>
        <div class="space-y-4">
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Judul Halaman (Meta Title)</Label
            >
            <Input
              type="text"
              v-model="form.meta_title"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="contoh: SDIT Nur Iman — Unggul & Islami"
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Deskripsi Singkat (Meta Description)</Label
            >
            <Textarea
              v-model="form.meta_description"
              rows="3"
              class="w-full px-4 py-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="Deskripsi singkat pencarian Google..."
            />
          </div>
        </div>
      </div>

      <!-- Submit Button -->
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
