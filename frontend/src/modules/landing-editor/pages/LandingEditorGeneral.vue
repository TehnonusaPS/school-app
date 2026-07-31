<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Save, Upload } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { toast } from 'vue-sonner'

const store = useLandingEditorStore()

// ── Tema ─────────────────────────────────────────────────────────────────────
const themeOptions = [
  { id: 'modern',  name: '🔵 Modern Akademik',  desc: 'Clean, minimalis, profesional dengan glassmorphism & parallax.' },
  { id: 'islami',  name: '🟢 Islami Elegant',   desc: 'Warna emerald-emas yang hangat dengan geometri ornamentik islami.' },
  { id: 'playful', name: '🟡 Colorful Playful', desc: 'Sangat cocok untuk TK/SD dengan bentuk wavy, blob, dan emoji ceria.' }
]

// ── Color Palettes (sama persis dengan IndexPage.vue Konfigurasi Global) ──────
const colorPalettes = computed(() => {
  const t = form.value.theme
  if (t === 'islami') return [
    { name: 'Default',        primary: '#047857', secondary: '#fbbf24', accent: '#14b8a6' },
    { name: 'Desert Gold',    primary: '#b45309', secondary: '#fcd34d', accent: '#064e3b' },
    { name: 'Royal Sapphire', primary: '#1e3a8a', secondary: '#38bdf8', accent: '#0ea5e9' },
    { name: 'Serene Sage',    primary: '#4d7c0f', secondary: '#a3e635', accent: '#15803d' }
  ]
  if (t === 'playful') return [
    { name: 'Default',        primary: '#ec4899', secondary: '#fcd34d', accent: '#06b6d4' },
    { name: 'Candy Pop',      primary: '#8b5cf6', secondary: '#f472b6', accent: '#34d399' },
    { name: 'Sunshine Kids',  primary: '#eab308', secondary: '#fb923c', accent: '#38bdf8' },
    { name: 'Minty Fresh',    primary: '#10b981', secondary: '#6366f1', accent: '#fbbf24' }
  ]
  // modern (default)
  return [
    { name: 'Default',         primary: '#1e40af', secondary: '#f59e0b', accent: '#0ea5e9' },
    { name: 'Elegant Ruby',    primary: '#be123c', secondary: '#fbbf24', accent: '#172554' },
    { name: 'Forest Scholar',  primary: '#047857', secondary: '#eab308', accent: '#14b8a6' },
    { name: 'Executive Slate', primary: '#334155', secondary: '#94a3b8', accent: '#2563eb' }
  ]
})

const isCustomColor = ref(false)

const applyPalette = (palette) => {
  isCustomColor.value = false
  form.value.primary_color   = palette.primary
  form.value.secondary_color = palette.secondary
  form.value.accent_color    = palette.accent
}

const isPaletteMatch = (palette) => {
  if (!form.value.primary_color || !palette.primary) return false
  return form.value.primary_color.toLowerCase()   === palette.primary.toLowerCase() &&
         form.value.secondary_color.toLowerCase() === palette.secondary.toLowerCase()
}

const applyDefaultThemeColor = (newTheme) => {
  let defPrimary = '#1e40af', defSec = '#f59e0b', defAcc = '#0ea5e9'
  if (newTheme === 'islami')  { defPrimary = '#047857'; defSec = '#fbbf24'; defAcc = '#14b8a6' }
  if (newTheme === 'playful') { defPrimary = '#ec4899'; defSec = '#fcd34d'; defAcc = '#06b6d4' }
  form.value.primary_color   = defPrimary
  form.value.secondary_color = defSec
  form.value.accent_color    = defAcc
  isCustomColor.value = false
}

// ── Form State ────────────────────────────────────────────────────────────────
const form = ref({
  theme:             'modern',
  slug:              '',
  logo:              '',
  legal_number:      '',
  slogan:            '',
  meta_title:        '',
  meta_description:  '',
  hero_badge_text:   '',
  primary_color:     '#1e40af',
  secondary_color:   '#f59e0b',
  accent_color:      '#0ea5e9'
})

// Saat tema berubah, otomatis reset warna ke default tema itu
watch(() => form.value.theme, (newTheme) => {
  applyDefaultThemeColor(newTheme)
})

// ── onMounted: load dari store ────────────────────────────────────────────────
onMounted(async () => {
  await store.fetchLandingPage()
  if (store.landingPage) {
    const lp = store.landingPage
    form.value.theme             = lp.theme             || 'modern'
    form.value.slug              = lp.slug              || ''
    form.value.logo              = lp.logo              || ''
    form.value.legal_number      = lp.legal_number      || ''
    form.value.slogan            = lp.slogan            || ''
    form.value.meta_title        = lp.meta_title        || ''
    form.value.meta_description  = lp.meta_description  || ''
    form.value.hero_badge_text   = lp.hero_badge_text   || ''
    form.value.primary_color     = lp.primary_color     || '#1e40af'
    form.value.secondary_color   = lp.secondary_color   || '#f59e0b'
    form.value.accent_color      = lp.accent_color      || '#0ea5e9'
  }
})

// ── Logo upload ───────────────────────────────────────────────────────────────
const isUploading = ref(false)
async function onLogoUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { toast.error('Ukuran file maksimal 2MB'); return }
  try {
    isUploading.value = true
    const res = await store.uploadImage(file)
    form.value.logo = res.url
    toast.success('Logo berhasil diupload!')
  } catch (err) {
    toast.error(err.message)
  } finally {
    isUploading.value = false
  }
}

// ── Save ──────────────────────────────────────────────────────────────────────
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
    <!-- Loading -->
    <div v-if="store.loading" class="flex justify-center items-center py-12">
      <div class="w-8 h-8 border-4 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Error -->
    <div v-else-if="store.error" class="flex flex-col items-center justify-center py-16 text-center gap-4">
      <p class="text-destructive font-semibold">{{ store.error }}</p>
      <button @click="store.fetchLandingPage()" class="text-sm text-primary underline">Coba lagi</button>
    </div>

    <form v-else @submit.prevent="handleSave" class="space-y-8">

      <!-- ① Tema Visual -->
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
            <input type="radio" v-model="form.theme" :value="opt.id" class="sr-only" />
            <span class="font-extrabold text-sm text-foreground">{{ opt.name }}</span>
            <span class="text-xs text-gray-400 mt-2 leading-relaxed">{{ opt.desc }}</span>
          </label>
        </div>
      </div>

      <!-- ② Warna Identitas dengan Palette Presets -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-white/10 space-y-5">
        <h3 class="text-lg font-bold text-foreground">Warna Identitas (Branding Colors)</h3>

        <!-- Palette Presets -->
        <div class="space-y-2">
          <Label class="block text-xs font-bold text-gray-400 uppercase">Pilih Palette Warna</Label>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button
              v-for="palette in colorPalettes"
              :key="palette.name"
              type="button"
              @click="applyPalette(palette)"
              class="flex flex-col items-start p-3 rounded-xl border-2 transition-all text-left"
              :class="isPaletteMatch(palette) && !isCustomColor
                ? 'border-primary bg-primary/10'
                : 'border-gray-200 dark:border-white/20 hover:bg-white/5'"
            >
              <div class="flex gap-1.5 mb-2">
                <div class="w-5 h-5 rounded-full shadow ring-1 ring-white/20" :style="{ background: palette.primary }"></div>
                <div class="w-5 h-5 rounded-full shadow ring-1 ring-white/20" :style="{ background: palette.secondary }"></div>
                <div class="w-5 h-5 rounded-full shadow ring-1 ring-white/20" :style="{ background: palette.accent }"></div>
              </div>
              <span class="text-xs font-bold text-foreground">{{ palette.name }}</span>
            </button>
          </div>
        </div>

        <!-- Custom Color Pickers -->
        <div class="grid grid-cols-3 gap-6">
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Warna Utama</Label>
            <div class="flex items-center gap-3">
              <input type="color" v-model="form.primary_color" @input="isCustomColor = true"
                class="w-11 h-11 rounded-xl border border-white/10 cursor-pointer bg-transparent" />
              <Input type="text" v-model="form.primary_color" @input="isCustomColor = true"
                class="flex-1 h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl uppercase text-foreground" />
            </div>
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Warna Sekunder</Label>
            <div class="flex items-center gap-3">
              <input type="color" v-model="form.secondary_color" @input="isCustomColor = true"
                class="w-11 h-11 rounded-xl border border-white/10 cursor-pointer bg-transparent" />
              <Input type="text" v-model="form.secondary_color" @input="isCustomColor = true"
                class="flex-1 h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl uppercase text-foreground" />
            </div>
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Warna Aksen</Label>
            <div class="flex items-center gap-3">
              <input type="color" v-model="form.accent_color" @input="isCustomColor = true"
                class="w-11 h-11 rounded-xl border border-white/10 cursor-pointer bg-transparent" />
              <Input type="text" v-model="form.accent_color" @input="isCustomColor = true"
                class="flex-1 h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl uppercase text-foreground" />
            </div>
          </div>
        </div>
      </div>

      <!-- ③ Logo Instansi -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Logo Instansi</h3>
        <p class="text-xs text-gray-400">Upload logo sekolah/yayasan. Akan ditampilkan di navbar landing page dan sidebar admin.</p>
        <div class="flex items-center gap-6">
          <div class="w-24 h-24 rounded-2xl border-2 border-dashed border-gray-200 dark:border-white/20 bg-white/50 dark:bg-background/30 flex items-center justify-center overflow-hidden shrink-0">
            <img v-if="form.logo" :src="form.logo" class="w-full h-full object-contain p-1" />
            <span v-else class="text-3xl">🏫</span>
          </div>
          <div class="space-y-2">
            <label class="flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/50 hover:bg-gray-50 dark:hover:bg-white/5 rounded-xl font-bold text-sm cursor-pointer text-foreground transition-colors">
              <Upload class="w-4 h-4" />
              {{ isUploading ? 'Mengupload...' : 'Upload Logo' }}
              <input type="file" @change="onLogoUpload" class="sr-only" accept="image/*" :disabled="isUploading" />
            </label>
            <p class="text-xs text-gray-400">Format: PNG/JPG/SVG · Maksimal 2MB · Disarankan transparan (PNG)</p>
            <div v-if="form.logo" class="flex items-center gap-2">
              <Input type="text" v-model="form.logo"
                class="flex-1 h-9 px-3 text-xs border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-lg text-foreground" />
              <button type="button" @click="form.logo = ''"
                class="text-xs text-destructive hover:underline font-bold shrink-0">Hapus</button>
            </div>
          </div>
        </div>
      </div>

      <!-- ④ Identitas & Slug -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Identitas & Alamat Halaman</h3>

        <div class="grid md:grid-cols-2 gap-4">
          <!-- Nomor SK / NPSN -->
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nomor SK / NPSN</Label>
            <Input type="text" v-model="form.legal_number"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground"
              placeholder="contoh: 10293847" />
          </div>

          <!-- Slogan -->
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Slogan / Tagline</Label>
            <Input type="text" v-model="form.slogan"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground"
              placeholder="contoh: Mencerdaskan Kehidupan Bangsa" />
          </div>
        </div>

        <!-- Slug URL -->
        <div>
          <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Slug URL</Label>
          <div class="flex items-center rounded-xl border border-gray-200 dark:border-white/20 bg-white/50 dark:bg-background/30 overflow-hidden h-11 transition-all focus-within:border-primary/50 focus-within:ring-1 focus-within:ring-primary/50">
            <span class="flex items-center justify-center bg-gray-50 dark:bg-white/5 text-gray-500 px-4 h-full text-sm font-medium border-r border-gray-200 dark:border-white/20">/s/</span>
            <Input type="text" v-model="form.slug"
              class="flex-1 h-full rounded-none border-0 bg-transparent px-4 text-foreground focus-visible:ring-0 focus-visible:ring-offset-0"
              placeholder="contoh: sdit-nur-iman" required />
          </div>
        </div>
      </div>

      <!-- ⑤ Badge Hero -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Badge Hero Section</h3>
        <p class="text-xs text-gray-400">Teks kecil berbentuk badge yang muncul di atas judul utama hero. Biarkan kosong jika tidak ingin menampilkannya.</p>
        <div>
          <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Teks Badge (Opsional)</Label>
          <Input type="text" v-model="form.hero_badge_text"
            class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground"
            placeholder="contoh: ✨ Sekolah Terbaik 2024 · Akreditasi A" />
        </div>
      </div>

      <!-- ⑥ Meta SEO -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Meta SEO</h3>
        <div class="space-y-4">
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Judul Halaman (Meta Title)</Label>
            <Input type="text" v-model="form.meta_title"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground"
              placeholder="contoh: SDIT Nur Iman — Unggul & Islami" />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Deskripsi Singkat (Meta Description)</Label>
            <Textarea v-model="form.meta_description" rows="3"
              class="w-full px-4 py-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground"
              placeholder="Deskripsi singkat pencarian Google..." />
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div class="flex justify-end">
        <Button type="submit" class="flex items-center gap-2 px-6 rounded-xl font-bold transition-all h-11" :disabled="store.saving">
          <Save class="w-4 h-4" />
          {{ store.saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </Button>
      </div>

    </form>
  </BuilderLayout>
</template>
