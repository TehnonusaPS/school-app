<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import { useAuthStore } from '@/stores/authStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Save } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { toast } from 'vue-sonner'

const store = useLandingEditorStore()
const authStore = useAuthStore()

const themeOptions = [
  { id: 'modern',  name: '🔵 Modern Akademik',  desc: 'Clean, minimalis, profesional dengan glassmorphism & parallax.' },
  { id: 'islami',  name: '🟢 Islami Elegant',   desc: 'Warna emerald-emas yang hangat dengan geometri ornamentik islami.' },
  { id: 'playful', name: '🟡 Colorful Playful', desc: 'Sangat cocok untuk TK/SD dengan bentuk wavy, blob, dan emoji ceria.' }
]

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
  return [
    { name: 'Default',         primary: '#1e40af', secondary: '#f59e0b', accent: '#0ea5e9' },
    { name: 'Elegant Ruby',    primary: '#be123c', secondary: '#fbbf24', accent: '#172554' },
    { name: 'Forest Scholar',  primary: '#047857', secondary: '#eab308', accent: '#14b8a6' },
    { name: 'Executive Slate', primary: '#334155', secondary: '#94a3b8', accent: '#2563eb' }
  ]
})

const isCustomColor = ref(false)

const isAnyPaletteMatch = computed(() =>
  colorPalettes.value.some(p =>
    form.value.primary_color?.toLowerCase()   === p.primary.toLowerCase() &&
    form.value.secondary_color?.toLowerCase() === p.secondary.toLowerCase()
  )
)

const isPaletteMatch = (palette) =>
  form.value.primary_color?.toLowerCase()   === palette.primary.toLowerCase() &&
  form.value.secondary_color?.toLowerCase() === palette.secondary.toLowerCase()

const applyPalette = (palette) => {
  isCustomColor.value = false
  form.value.primary_color   = palette.primary
  form.value.secondary_color = palette.secondary
  form.value.accent_color    = palette.accent
}

const applyDefaultThemeColor = (newTheme) => {
  let p = '#1e40af', s = '#f59e0b', a = '#0ea5e9'
  if (newTheme === 'islami')  { p = '#047857'; s = '#fbbf24'; a = '#14b8a6' }
  if (newTheme === 'playful') { p = '#ec4899'; s = '#fcd34d'; a = '#06b6d4' }
  form.value.primary_color = p; form.value.secondary_color = s; form.value.accent_color = a
  isCustomColor.value = false
}

const form = ref({
  theme: 'modern', slug: '', logo: '',
  meta_description: '', slogan: '',
  primary_color: '#1e40af', secondary_color: '#f59e0b', accent_color: '#0ea5e9'
})

const entityName        = computed(() => store.landingPage?.meta_title || authStore.user?.name || '(Belum Diatur)')
const entityLegalNumber = computed(() => store.landingPage?.legal_number || '(Belum Diatur di Profil)')
const isYayasan         = computed(() => authStore.user?.role === 'admin_yayasan')

onMounted(async () => {
  await store.fetchLandingPage()
  if (store.landingPage) {
    const lp = store.landingPage
    form.value.theme            = lp.theme            || 'modern'
    form.value.slug             = lp.slug             || ''
    form.value.logo             = lp.logo             || ''
    form.value.meta_description = lp.meta_description || ''
    form.value.slogan           = lp.slogan           || ''
    form.value.primary_color    = lp.primary_color    || '#1e40af'
    form.value.secondary_color  = lp.secondary_color  || '#f59e0b'
    form.value.accent_color     = lp.accent_color     || '#0ea5e9'
  }
})

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
  } catch (err) { toast.error(err.message) }
  finally { isUploading.value = false }
}

async function handleSave() {
  try {
    await store.saveSettings(form.value)
    toast.success('Pengaturan umum berhasil disimpan!')
  } catch (err) { toast.error(err.message) }
}
</script>

<template>
  <BuilderLayout>
    <div v-if="store.loading" class="flex justify-center items-center py-12">
      <div class="w-8 h-8 border-4 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else-if="store.error" class="flex flex-col items-center justify-center py-16 text-center gap-4">
      <p class="text-destructive font-semibold">{{ store.error }}</p>
      <button @click="store.fetchLandingPage()" class="text-sm text-primary underline">Coba lagi</button>
    </div>

    <form v-else @submit.prevent="handleSave" class="space-y-6">
      <h4 class="text-sm font-bold text-foreground">Pengaturan Umum &amp; Branding</h4>

      <!-- Tema -->
      <div class="space-y-4">
        <Label class="text-xs text-muted-foreground block">Pilih Tema Visual</Label>
        <div class="grid md:grid-cols-3 gap-4">
          <label
            v-for="opt in themeOptions" :key="opt.id"
            class="flex flex-col p-3 rounded-xl border-2 cursor-pointer transition-all hover:bg-muted/50"
            :class="form.theme === opt.id ? 'border-primary bg-primary/10' : 'border-border'"
          >
            <input type="radio" v-model="form.theme" :value="opt.id" class="sr-only" @change="applyDefaultThemeColor(opt.id)" />
            <span class="font-bold text-xs text-foreground">{{ opt.name }}</span>
            <span class="text-[10px] text-muted-foreground mt-1">{{ opt.desc }}</span>
          </label>
        </div>
      </div>

      <!-- Slug -->
      <div class="space-y-1">
        <Label class="text-xs text-muted-foreground block">Alamat Halaman (Slug URL)</Label>
        <div class="flex rounded-xl shadow-sm border border-border/50 overflow-hidden bg-background/50 focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all">
          <span class="bg-muted/50 text-muted-foreground px-3 py-2 text-xs flex items-center border-r border-border/50 font-bold">/s/</span>
          <Input type="text" v-model="form.slug" maxlength="50" class="flex-1 rounded-none border-0 text-xs focus-visible:ring-0 bg-transparent" placeholder="contoh: sdit-nur-iman" />
        </div>
        <p class="text-[10px] text-muted-foreground">Kosongkan untuk membuat slug otomatis dari nama profil.</p>
      </div>

      <!-- Palet Warna -->
      <div class="space-y-3 pt-4 border-t border-border">
        <Label class="text-xs font-bold text-foreground block">🎨 Rekomendasi Palet Harmonik &amp; Kustomisasi</Label>
        <div class="flex flex-wrap gap-3">
          <button
            v-for="(palette, idx) in colorPalettes" :key="idx"
            @click="applyPalette(palette)" type="button"
            class="group relative flex flex-col items-center gap-1.5 p-2 rounded-xl border transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
            :class="isPaletteMatch(palette) && !isCustomColor ? 'border-primary bg-primary/10 ring-2 ring-primary/20' : 'border-border bg-background/50 hover:bg-muted/50 hover:border-primary/50'"
            :title="palette.name"
          >
            <div class="flex items-center -space-x-1">
              <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[3]" :style="{ backgroundColor: palette.primary }"></div>
              <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[2]" :style="{ backgroundColor: palette.secondary }"></div>
              <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[1]" :style="{ backgroundColor: palette.accent }"></div>
            </div>
            <span class="text-[9px] font-medium" :class="isPaletteMatch(palette) && !isCustomColor ? 'text-primary font-bold' : 'text-muted-foreground group-hover:text-foreground'">{{ palette.name }}</span>
          </button>

          <button
            @click="isCustomColor = true" type="button"
            class="group relative flex flex-col items-center gap-1.5 p-2 rounded-xl border transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
            :class="(!isAnyPaletteMatch || isCustomColor) ? 'border-primary bg-primary/10' : 'border-border bg-background/50 hover:bg-muted/50 hover:border-primary/50'"
          >
            <div class="flex items-center -space-x-1">
              <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[3]" :style="{ backgroundColor: (!isAnyPaletteMatch || isCustomColor) ? form.primary_color : '#000000' }"></div>
              <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[2]" :style="{ backgroundColor: (!isAnyPaletteMatch || isCustomColor) ? form.secondary_color : '#000000' }"></div>
              <div class="w-5 h-5 rounded-full shadow-sm ring-2 ring-background z-[1]" :style="{ backgroundColor: (!isAnyPaletteMatch || isCustomColor) ? form.accent_color : '#000000' }"></div>
            </div>
            <span class="text-[9px] font-medium" :class="(!isAnyPaletteMatch || isCustomColor) ? 'text-primary font-bold' : 'text-muted-foreground group-hover:text-foreground'">Warna Custom</span>
          </button>
        </div>

        <!-- Custom pickers — muncul hanya saat mode custom -->
        <div v-if="!isAnyPaletteMatch || isCustomColor" class="grid grid-cols-3 gap-4 pt-4 border-t border-border mt-4">
          <div>
            <Label class="text-[10px] font-bold text-muted-foreground uppercase mb-1 block">Warna Utama</Label>
            <div class="flex items-center gap-2">
              <Input type="color" v-model="form.primary_color" class="w-8 h-8 p-0 border-0 rounded-lg cursor-pointer bg-transparent shrink-0" />
              <Input type="text" v-model="form.primary_color" class="flex-1 px-2 py-1.5 text-[10px] rounded-lg uppercase bg-background/30" />
            </div>
          </div>
          <div>
            <Label class="text-[10px] font-bold text-muted-foreground uppercase mb-1 block">Warna Sekunder</Label>
            <div class="flex items-center gap-2">
              <Input type="color" v-model="form.secondary_color" class="w-8 h-8 p-0 border-0 rounded-lg cursor-pointer bg-transparent shrink-0" />
              <Input type="text" v-model="form.secondary_color" class="flex-1 px-2 py-1.5 text-[10px] rounded-lg uppercase bg-background/30" />
            </div>
          </div>
          <div>
            <Label class="text-[10px] font-bold text-muted-foreground uppercase mb-1 block">Warna Aksen</Label>
            <div class="flex items-center gap-2">
              <Input type="color" v-model="form.accent_color" class="w-8 h-8 p-0 border-0 rounded-lg cursor-pointer bg-transparent shrink-0" />
              <Input type="text" v-model="form.accent_color" class="flex-1 px-2 py-1.5 text-[10px] rounded-lg uppercase bg-background/30" />
            </div>
          </div>
        </div>
      </div>

      <!-- Logo + Meta -->
      <div class="space-y-4">
        <!-- Logo -->
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">Logo Instansi</Label>
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 shrink-0 rounded-xl flex items-center justify-center overflow-hidden transition-all"
              :class="form.logo ? 'shadow-md border border-border/50 bg-white dark:bg-gray-800' : 'border-2 border-dashed border-border bg-muted/30'">
              <img v-if="form.logo" :src="form.logo" alt="Logo" class="w-full h-full object-contain" />
              <span v-else class="text-2xl">🏫</span>
            </div>
            <div class="flex-1">
              <Input type="file" accept="image/*" @change="onLogoUpload" :disabled="isUploading" class="text-xs max-w-sm rounded-xl cursor-pointer" />
              <p class="text-[10px] text-muted-foreground font-medium mt-1">Format PNG/JPG transparan direkomendasikan. Maks 2MB.</p>
            </div>
          </div>
        </div>

        <!-- Meta Title — readonly -->
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">
            Meta Title SEO <span class="text-[10px] text-blue-500 font-normal">(Otomatis dari Profil)</span>
          </Label>
          <Input type="text" :model-value="entityName" readonly class="rounded-xl text-xs bg-muted/30 text-muted-foreground cursor-not-allowed" />
        </div>

        <!-- Meta Description -->
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">Meta Description SEO</Label>
          <Textarea
            v-model="form.meta_description" rows="2" maxlength="160" class="rounded-xl text-xs"
            :placeholder="isYayasan
              ? 'Contoh: Lembaga penaung institusi pendidikan terbaik yang berfokus pada pengembangan umat.'
              : 'Contoh: Sekolah menengah atas terbaik se-DKI Jakarta yang berfokus pada karakter.'"
          />
        </div>
      </div>

      <!-- Legal + Slogan -->
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">
            Nomor Legalitas / Izin <span class="text-[10px] text-blue-500 font-normal">(Otomatis dari Profil)</span>
          </Label>
          <Input type="text" :model-value="entityLegalNumber" readonly class="rounded-xl text-xs bg-muted/30 text-muted-foreground cursor-not-allowed" />
          <p class="text-[10px] text-muted-foreground mt-1.5">Ditampilkan di bagian catatan kaki (footer) web.</p>
        </div>
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">Motto / Slogan (Opsional)</Label>
          <Input
            type="text" v-model="form.slogan" maxlength="100" class="rounded-xl text-xs"
            :placeholder="isYayasan ? 'Contoh: Membangun Generasi Rabbani' : 'Contoh: Cerdas, Kreatif, Berkarakter'"
          />
          <p class="text-[10px] text-muted-foreground mt-1.5">Semboyan yang mencerminkan instansi Anda.</p>
        </div>
      </div>

      <!-- Submit -->
      <div class="flex justify-end pt-2">
        <Button type="submit" class="flex items-center gap-2 px-6 rounded-xl font-bold h-11" :disabled="store.saving">
          <Save class="w-4 h-4" />
          {{ store.saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </Button>
      </div>
    </form>
  </BuilderLayout>
</template>
