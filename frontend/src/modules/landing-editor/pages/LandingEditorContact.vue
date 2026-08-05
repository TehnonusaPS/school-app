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

const isYayasan = computed(() => authStore.user?.role === 'admin_yayasan')
const entityType = computed(() => {
  if (authStore.user?.role === 'admin_yayasan') return 'Yayasan'
  if (authStore.user?.role === 'admin_sekolah' || authStore.user?.role === 'kepala_sekolah') return 'Sekolah'
  return 'Instansi'
})

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
    const lp = store.landingPage
    form.value.contact_email      = lp.contact_email || ''
    form.value.contact_phone      = lp.contact_phone || ''
    form.value.contact_address    = lp.contact_address || ''
    form.value.contact_maps_embed = lp.contact_maps_embed || ''
    form.value.social_instagram   = lp.social_instagram || ''
    form.value.social_facebook    = lp.social_facebook || ''
    form.value.social_youtube     = lp.social_youtube || ''
    form.value.social_tiktok      = lp.social_tiktok || ''
  }
})

async function handleSave() {
  try {
    await store.saveSettings(form.value)
    toast.success('Kontak & Media Sosial berhasil disimpan!')
  } catch (err) {
    toast.error(err.message)
  }
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
      <h4 class="text-sm font-bold text-foreground">Hubungi Kami &amp; Media Sosial</h4>
      <p class="text-[10px] text-muted-foreground -mt-4">Info dengan label biru otomatis sinkron dengan pengaturan profil utama instansi.</p>

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">
            Email {{ entityType }} <span class="text-[10px] text-blue-500 font-normal">(Sinkron Profil)</span>
          </Label>
          <Input
            type="email"
            v-model="form.contact_email"
            maxlength="255"
            class="rounded-xl text-xs"
            :placeholder="isYayasan ? 'Contoh: info@yayasan.com' : 'Contoh: info@sekolah.com'"
          />
        </div>
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">
            Telepon <span class="text-[10px] text-blue-500 font-normal">(Sinkron Profil)</span>
          </Label>
          <Input
            type="text"
            v-model="form.contact_phone"
            maxlength="20"
            class="rounded-xl text-xs"
            placeholder="Contoh: 021-1234567"
          />
        </div>
        <div class="md:col-span-2">
          <Label class="text-xs text-muted-foreground mb-1.5 block">
            Alamat Lengkap <span class="text-[10px] text-blue-500 font-normal">(Sinkron Profil)</span>
          </Label>
          <Textarea
            v-model="form.contact_address"
            rows="2"
            maxlength="500"
            class="rounded-xl text-xs"
            :placeholder="isYayasan ? 'Masukkan alamat lengkap yayasan di sini...' : 'Masukkan alamat lengkap sekolah di sini...'"
          ></Textarea>
        </div>
        <div class="md:col-span-2">
          <Label class="text-xs text-muted-foreground mb-1.5 block">Google Maps Embed URL</Label>
          <Textarea
            v-model="form.contact_maps_embed"
            rows="2"
            maxlength="1000"
            class="rounded-xl text-xs"
            placeholder="Salin tag <iframe src='...'> dari Google Maps"
          ></Textarea>
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-4 pt-2">
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">
            Instagram URL <span class="text-[10px] text-blue-500 font-normal">(Sinkron Profil)</span>
          </Label>
          <Input
            type="text"
            v-model="form.social_instagram"
            maxlength="255"
            class="rounded-xl text-xs"
            :placeholder="isYayasan ? 'https://instagram.com/yayasan' : 'https://instagram.com/sekolah'"
          />
        </div>
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">
            Facebook URL <span class="text-[10px] text-blue-500 font-normal">(Sinkron Profil)</span>
          </Label>
          <Input
            type="text"
            v-model="form.social_facebook"
            maxlength="255"
            class="rounded-xl text-xs"
            :placeholder="isYayasan ? 'https://facebook.com/yayasan' : 'https://facebook.com/sekolah'"
          />
        </div>
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">TikTok URL</Label>
          <Input
            type="text"
            v-model="form.social_tiktok"
            maxlength="255"
            class="rounded-xl text-xs"
            :placeholder="isYayasan ? 'https://tiktok.com/@yayasan' : 'https://tiktok.com/@sekolah'"
          />
        </div>
        <div>
          <Label class="text-xs text-muted-foreground mb-1.5 block">YouTube URL</Label>
          <Input
            type="text"
            v-model="form.social_youtube"
            maxlength="255"
            class="rounded-xl text-xs"
            :placeholder="isYayasan ? 'https://youtube.com/c/yayasan' : 'https://youtube.com/c/sekolah'"
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
