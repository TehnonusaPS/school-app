<script setup>
import { ref, onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Save, Plus, Trash2, Eye } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'

import { toast } from 'vue-sonner'

const store = useLandingEditorStore()

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
    form.value.about_title = store.landingPage.about_title || ''
    form.value.about_description = store.landingPage.about_description || ''
    form.value.about_vision = store.landingPage.about_vision || ''
    form.value.about_mission = store.landingPage.about_mission || []
    form.value.about_image = store.landingPage.about_image || ''
  }
})

async function handleSave() {
  try {
    await store.saveSettings(form.value)
    toast.success('Tentang kami berhasil disimpan!')
  } catch (err) {
    toast.error(err.message)
  }
}

async function onImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  try {
    const res = await store.uploadImage(file, 'about')
    form.value.about_image = res.url
    toast.success('Foto profil berhasil diupload!')
  } catch (err) {
    toast.error(err.message)
  }
}

function addMission() {
  if (newMissionItem.value.trim()) {
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
      <!-- Foto Kepala Sekolah / Profil -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Profil & Deskripsi</h3>
        <div class="grid md:grid-cols-4 gap-6">
          <div class="md:col-span-1">
            <div>
              <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
                >Foto Sekolah / Profil</Label
              >
              <div class="space-y-2">
                <div
                  class="w-full aspect-square md:w-32 md:h-32 rounded-2xl overflow-hidden border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 flex items-center justify-center relative group"
                >
                  <img
                    v-if="form.about_image"
                    :src="form.about_image"
                    class="w-full h-full object-cover"
                  />
                  <span
                    v-else
                    class="text-3xl"
                    >🏫</span
                  >
                </div>
                <label
                  class="px-4 py-2 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/50 hover:bg-gray-50 dark:hover:bg-white/5 rounded-xl font-bold text-xs cursor-pointer text-foreground transition-colors"
                >
                  Ganti Foto
                  <input
                    type="file"
                    @change="onImageUpload"
                    class="sr-only"
                    accept="image/*"
                  />
                </label>
              </div>
            </div>
          </div>
          <div class="md:col-span-3 space-y-4">
            <div>
              <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
                >Judul Profil Tentang Kami</Label
              >
              <Input
                type="text"
                v-model="form.about_title"
                class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                required
              />
            </div>
            <div>
              <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
                >Deskripsi Tentang Sekolah</Label
              >
              <Textarea
                v-model="form.about_description"
                rows="5"
                class="w-full px-4 py-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
                required
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Visi & Misi -->
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-6">
        <h3 class="text-lg font-bold text-foreground">Visi & Misi Sekolah</h3>

        <div>
          <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Visi Sekolah</Label>
          <Textarea
            v-model="form.about_vision"
            rows="3"
            class="w-full px-4 py-3 border border-gray-200 dark:border-white/10 bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
            placeholder="Tuliskan visi utama sekolah..."
          />
        </div>

        <div class="space-y-3">
          <Label class="block text-xs font-bold text-gray-400 uppercase">Misi Sekolah</Label>
          <div
            v-for="(mission, index) in form.about_mission"
            :key="index"
            class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-white/5 bg-white/50 dark:bg-white/5"
          >
            <span
              class="text-primary font-bold text-sm bg-primary/10 w-7 h-7 flex items-center justify-center rounded-lg shrink-0"
              >{{ index + 1 }}</span
            >
            <Input
              type="text"
              v-model="form.about_mission[index]"
              class="flex-1 h-11 px-4 border border-white/10 bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
            />
            <Button
              type="button"
              variant="ghost"
              size="icon"
              @click="removeMission(index)"
              class="text-destructive hover:text-destructive hover:bg-destructive/10"
            >
              <Trash2 class="w-4 h-4" />
            </Button>
          </div>

          <div class="flex gap-2">
            <Input
              type="text"
              v-model="newMissionItem"
              class="flex-1 h-11 px-4 border border-white/10 bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="Tambah misi baru..."
              @keydown.enter.prevent="addMission"
            />
            <Button
              type="button"
              variant="secondary"
              @click="addMission"
              class="flex items-center gap-1.5 rounded-xl font-bold text-xs h-10"
            >
              <Plus class="w-4 h-4" /> Tambah
            </Button>
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
