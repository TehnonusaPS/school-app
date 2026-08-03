<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import { useAuthStore } from '@/stores/authStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Save, Plus, Trash2 } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { toast } from 'vue-sonner'

const store = useLandingEditorStore()
const authStore = useAuthStore()

const isYayasan = computed(() => authStore.user?.role === 'admin_yayasan')

const form = ref({
  hero_badge_text: '',
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
    const lp = store.landingPage
    form.value.hero_badge_text  = lp.hero_badge_text  || ''
    form.value.hero_title       = lp.hero_title       || ''
    form.value.hero_subtitle    = lp.hero_subtitle    || ''
    form.value.hero_description = lp.hero_description || ''
    form.value.hero_cta_text    = lp.hero_cta_text    || ''
    form.value.hero_cta_link    = lp.hero_cta_link    || ''
    form.value.hero_images      = Array.isArray(lp.hero_images) ? [...lp.hero_images] : []
  }
})

async function handleSave() {
  try {
    await store.saveSettings(form.value)
    toast.success('Hero Banner berhasil disimpan!')
  } catch (err) {
    toast.error(err.message)
  }
}

async function onHeroImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { toast.error('Ukuran file maksimal 2MB'); return }
  try {
    const res = await store.uploadImage(file)
    if (!Array.isArray(form.value.hero_images)) form.value.hero_images = []
    form.value.hero_images.push({ url: res.url, caption: '' })
    toast.success('Gambar berhasil ditambahkan ke carousel!')
  } catch (err) {
    toast.error(err.message)
  }
}

function removeHeroImage(index) {
  form.value.hero_images.splice(index, 1)
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

    <form v-else @submit.prevent="handleSave" class="space-y-4">
      <h4 class="text-sm font-bold text-foreground">Konten Banner Hero</h4>

      <!-- ① Slide Gambar Carousel -->
      <div>
        <Label class="text-xs text-muted-foreground mb-1.5 block">Slide Gambar Carousel</Label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
          <div
            v-for="(img, idx) in form.hero_images"
            :key="idx"
            class="relative group rounded-xl overflow-hidden border border-border/50 aspect-video"
          >
            <img :src="img.url" class="w-full h-full object-cover" />
            <button
              type="button"
              @click="removeHeroImage(idx)"
              class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition-opacity duration-300"
            >
              <Trash2 class="w-5 h-5 text-red-400" />
            </button>
          </div>

          <label class="flex flex-col items-center justify-center border-2 border-dashed border-border/50 rounded-xl cursor-pointer hover:bg-muted/50 aspect-video transition-colors bg-background/30">
            <Plus class="w-6 h-6 text-muted-foreground" />
            <span class="text-[10px] text-muted-foreground mt-2 font-bold">Tambah Gambar</span>
            <input type="file" @change="onHeroImageUpload" class="sr-only" accept="image/*" />
          </label>
        </div>
        <p class="text-[10px] text-muted-foreground mt-2">
          Rekomendasi ukuran: 1920x1080px (Landscape). Maks 2MB per gambar. Upload lebih dari 1 untuk carousel.
        </p>
      </div>

      <!-- ② Badge + Headline -->
      <div>
        <Label class="text-xs text-muted-foreground mb-1.5 block">Teks Badge / Label Atas (Opsional)</Label>
        <Input
          type="text"
          v-model="form.hero_badge_text"
          maxlength="50"
          class="rounded-xl text-xs mb-4"
          :placeholder="isYayasan ? 'Contoh: Pendaftaran Donatur Baru' : 'Contoh: Pendaftaran Siswa Baru Dibuka'"
        />
        <p class="text-[10px] text-muted-foreground -mt-2 mb-2">Kosongkan jika tidak ada event/pendaftaran yang sedang dibuka.</p>

        <Label class="text-xs text-muted-foreground mb-1.5 block">Headline Utama</Label>
        <Input
          type="text"
          v-model="form.hero_title"
          maxlength="100"
          class="rounded-xl text-xs"
          :placeholder="isYayasan ? 'Contoh: Membangun Generasi Emas' : 'Contoh: Sekolah Masa Depan Anda'"
        />
      </div>

      <!-- ③ Sub-headline -->
      <div>
        <Label class="text-xs text-muted-foreground mb-1.5 block">Sub-headline</Label>
        <Input
          type="text"
          v-model="form.hero_subtitle"
          maxlength="150"
          class="rounded-xl text-xs"
          :placeholder="isYayasan ? 'Contoh: Berkhidmat Membangun Peradaban' : 'Contoh: Terakreditasi A dan Berkarakter'"
        />
      </div>

      <!-- ④ Deskripsi -->
      <div>
        <Label class="text-xs text-muted-foreground mb-1.5 block">Deskripsi Singkat</Label>
        <Textarea
          v-model="form.hero_description"
          rows="3"
          maxlength="300"
          class="rounded-xl text-xs"
          :placeholder="isYayasan
            ? 'Contoh: Yayasan kami bergerak di bidang pendidikan, sosial, dan keagamaan dengan mengedepankan pembentukan karakter dan kemanfaatan umat...'
            : 'Contoh: Mari bergabung dengan ekosistem belajar yang modern, inovatif, dan berpusat pada minat bakat siswa...'"
        />
      </div>

      <!-- ⑤ CTA -->
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">Label Tombol CTA</Label>
          <Input
            type="text"
            v-model="form.hero_cta_text"
            maxlength="30"
            class="rounded-xl text-xs"
            :placeholder="isYayasan ? 'misal: Profil Yayasan / Donasi' : 'misal: Daftar Sekarang'"
          />
        </div>
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">Link Tombol CTA</Label>
          <Input
            type="text"
            v-model="form.hero_cta_link"
            maxlength="255"
            class="rounded-xl text-xs"
            placeholder="misal: #registration_cta"
          />
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
