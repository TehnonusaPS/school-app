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
const entityType = computed(() => {
  if (authStore.user?.role === 'admin_yayasan') return 'Yayasan'
  if (authStore.user?.role === 'admin_sekolah' || authStore.user?.role === 'kepala_sekolah') return 'Sekolah'
  return 'Instansi'
})

const form = ref({
  about_title: '',
  about_description: '',
  about_vision: '',
  about_mission: [],
  about_image: ''
})

const newMissionItem = ref('')

onMounted(async () => {
  await store.fetchLandingPage()
  if (store.landingPage) {
    const lp = store.landingPage
    form.value.about_title = lp.about_title || ''
    form.value.about_description = lp.about_description || ''
    form.value.about_vision = lp.about_vision || ''
    form.value.about_mission = Array.isArray(lp.about_mission) ? [...lp.about_mission] : []
    form.value.about_image = lp.about_image || ''
  }
})

async function handleSave() {
  try {
    await store.saveSettings(form.value)
    toast.success('Profil & Visi Misi berhasil disimpan!')
  } catch (err) {
    toast.error(err.message)
  }
}

async function onAboutImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { toast.error('Ukuran file maksimal 2MB'); return }
  try {
    const res = await store.uploadImage(file)
    form.value.about_image = res.url
    toast.success('Foto profil berhasil diupload!')
  } catch (err) {
    toast.error(err.message)
  }
}

function addMission() {
  if (newMissionItem.value.trim()) {
    if (!Array.isArray(form.value.about_mission)) form.value.about_mission = []
    form.value.about_mission.push(newMissionItem.value.trim())
    newMissionItem.value = ''
  }
}

function removeMission(index) {
  form.value.about_mission.splice(index, 1)
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
      <h4 class="text-sm font-bold text-foreground">Profil Singkat &amp; Visi Misi</h4>
      
      <div class="grid md:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Image Preview -->
        <div class="md:col-span-1 flex flex-col space-y-2">
          <Label class="text-xs text-muted-foreground block text-center uppercase font-bold">Foto Profil {{ entityType }}</Label>
          <div class="relative rounded-2xl overflow-hidden border border-border/50 aspect-video bg-background/30 flex items-center justify-center">
            <img v-if="form.about_image" :src="form.about_image" class="w-full h-full object-cover" />
            <span v-else class="text-3xl">🏫</span>
          </div>
          <label class="block text-center py-2 rounded-xl border border-border/50 hover:bg-muted/50 font-bold text-[10px] cursor-pointer transition-colors bg-background/50 text-foreground">
            Ganti Foto
            <input type="file" @change="onAboutImageUpload" class="sr-only" accept="image/*" />
          </label>
        </div>
        
        <!-- Kolom Kanan: Title & Description -->
        <div class="md:col-span-2 flex flex-col space-y-3">
          <div>
            <Label class="text-xs text-muted-foreground mb-1.5 block uppercase font-bold">Judul Profil Tentang Kami</Label>
            <Input
              type="text"
              v-model="form.about_title"
              maxlength="100"
              class="rounded-xl text-xs"
              :placeholder="isYayasan ? 'Contoh: Sejarah Yayasan Pendidikan Nusantara' : 'Contoh: Profil Singkat SMA Nusantara'"
            />
          </div>
          <div class="flex-1 flex flex-col">
            <Label class="text-xs text-muted-foreground mb-1.5 block uppercase font-bold">Deskripsi Tentang {{ entityType }}</Label>
            <Textarea
              v-model="form.about_description"
              maxlength="1000"
              class="rounded-xl text-xs flex-1 min-h-[110px] resize-none"
              placeholder="Tuliskan latar belakang, sejarah, atau filosofi instansi di sini..."
            ></Textarea>
          </div>
        </div>
      </div>

      <!-- Visi -->
      <div>
        <Label class="text-xs text-muted-foreground mb-1.5 block">Visi Instansi</Label>
        <Textarea
          v-model="form.about_vision"
          rows="2"
          maxlength="500"
          class="rounded-xl text-xs"
          placeholder="Contoh: Mewujudkan generasi muda yang beriman, bertakwa, berakhlak mulia..."
        ></Textarea>
      </div>

      <!-- Misi -->
      <div class="space-y-2">
        <Label class="text-xs text-muted-foreground block">Misi Instansi</Label>
        <div
          v-for="(mission, index) in form.about_mission"
          :key="index"
          class="flex gap-2 items-center"
        >
          <span class="text-primary font-bold text-xs bg-primary/10 w-6 h-6 flex items-center justify-center rounded-lg">{{ index + 1 }}</span>
          <Input
            type="text"
            v-model="form.about_mission[index]"
            maxlength="255"
            class="flex-1 rounded-xl text-xs"
          />
          <Button
            type="button"
            variant="ghost"
            size="icon"
            @click="removeMission(index)"
            class="text-destructive hover:text-destructive hover:bg-destructive/10"
          ><Trash2 class="w-4 h-4" /></Button>
        </div>
        
        <div class="flex gap-2">
          <input
            type="text"
            v-model="newMissionItem"
            maxlength="255"
            class="flex-1 px-3 py-2 text-xs bg-background/30 border border-border/50 rounded-xl text-foreground focus:outline-none"
            placeholder="Tambah misi..."
            @keydown.enter.prevent="addMission"
          />
          <Button
            type="button"
            variant="secondary"
            size="sm"
            @click="addMission"
            class="text-xs font-bold rounded-xl"
          >Tambah</Button>
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
