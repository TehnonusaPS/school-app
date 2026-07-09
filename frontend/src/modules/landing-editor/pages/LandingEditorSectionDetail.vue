<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Plus, Trash2, Edit, Save, ArrowLeft, Image as ImageIcon } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'

import { toast } from 'vue-sonner'

const route = useRoute()
const router = useRouter()
const store = useLandingEditorStore()

const sectionId = parseInt(route.params.sectionId)
const section = computed(() => {
  return store.landingPage?.sections?.find(s => s.id === sectionId) || null
})

const editingItem = ref(null)
const isCreate = ref(false)

const itemForm = ref({
  title: '',
  description: '',
  icon: 'star',
  image: '',
  value: '',
  link: '',
  sort_order: 0
})

onMounted(async () => {
  await store.fetchLandingPage()
})

function startCreate() {
  isCreate.value = true
  editingItem.value = null
  itemForm.value = {
    title: '',
    description: '',
    icon: 'star',
    image: '',
    value: '',
    link: '',
    sort_order: 0
  }
}

function startEdit(item) {
  isCreate.value = false
  editingItem.value = item
  itemForm.value = { ...item }
}

function cancelEdit() {
  editingItem.value = null
  isCreate.value = false
}

async function handleSaveItem() {
  try {
    if (isCreate.value) {
      await store.addItem(sectionId, itemForm.value)
      toast.success('Item baru berhasil ditambahkan!')
    } else {
      await store.updateItem(sectionId, editingItem.value.id, itemForm.value)
      toast.success('Item berhasil diupdate!')
    }
    cancelEdit()
  } catch (err) {
    toast.error(err.message)
  }
}

async function handleDelete(itemId) {
  if (confirm('Yakin ingin menghapus item ini?')) {
    try {
      await store.removeItem(sectionId, itemId)
      toast.success('Item berhasil dihapus!')
    } catch (err) {
      toast.error(err.message)
    }
  }
}

async function onImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  try {
    const res = await store.uploadImage(file, 'gallery')
    itemForm.value.image = res.url
    toast.success('Gambar item berhasil diupload!')
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

    <div
      v-else-if="section"
      class="space-y-6"
    >
      <div class="flex items-center gap-3">
        <Button
          variant="outline"
          size="icon"
          @click="router.push('/landing-editor/sections')"
          class="h-9 w-9 border-white/10 bg-white/5 hover:bg-white/10 text-foreground"
        >
          <ArrowLeft class="w-4 h-4" />
        </Button>
        <div>
          <h2 class="text-xl font-bold text-gray-800">
            Section: {{ section.title || section.type }}
          </h2>
          <p class="text-xs text-gray-400 mt-0.5">Kelola item konten di dalam section ini.</p>
        </div>
      </div>

      <!-- Item Form (Create or Edit) -->
      <div
        v-if="editingItem || isCreate"
        class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4"
      >
        <h3 class="font-bold text-foreground">{{ isCreate ? 'Tambah Item Baru' : 'Edit Item' }}</h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2">Judul Item</Label>
            <Input
              type="text"
              v-model="itemForm.title"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              required
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Nilai / Statistik (Opsional)</Label
            >
            <Input
              type="text"
              v-model="itemForm.value"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="contoh: 120+, Akreditasi A"
            />
          </div>
          <div class="md:col-span-2">
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Deskripsi / Detail</Label
            >
            <Textarea
              v-model="itemForm.description"
              rows="3"
              class="w-full px-4 py-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Icon Lucide (kebab-case)</Label
            >
            <Input
              type="text"
              v-model="itemForm.icon"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="contoh: book-open, award"
            />
          </div>
          <div>
            <Label class="block text-xs font-bold text-gray-400 uppercase mb-2"
              >Link Tujuan / URL</Label
            >
            <Input
              type="text"
              v-model="itemForm.link"
              class="w-full h-11 px-4 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="contoh: https://..."
            />
          </div>

          <!-- Image Upload for item -->
          <div class="md:col-span-2 space-y-2">
            <Label class="block text-xs font-bold text-gray-400 uppercase"
              >Gambar / Foto Item</Label
            >
            <div class="flex items-center gap-4">
              <div
                class="w-20 h-20 rounded-xl border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 flex items-center justify-center overflow-hidden"
              >
                <img
                  v-if="itemForm.image"
                  :src="itemForm.image"
                  class="w-full h-full object-cover"
                />
                <ImageIcon
                  v-else
                  class="w-8 h-8 text-gray-500"
                />
              </div>
              <label
                class="px-4 py-2 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/50 hover:bg-gray-50 dark:hover:bg-white/5 rounded-xl font-bold text-xs cursor-pointer text-foreground transition-colors"
              >
                Upload Foto
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

        <div class="flex justify-end gap-2 pt-4">
          <Button
            type="button"
            variant="secondary"
            @click="cancelEdit"
            class="px-4 rounded-xl font-bold text-xs h-10"
          >
            Batal
          </Button>
          <Button
            type="button"
            @click="handleSaveItem"
            class="flex items-center gap-1.5 px-4 rounded-xl font-bold text-xs h-10"
          >
            <Save class="w-4 h-4" /> Simpan
          </Button>
        </div>
      </div>

      <!-- List of Items -->
      <div
        v-else
        class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4"
      >
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-foreground">Daftar Item</h3>
          <Button
            @click="startCreate"
            class="flex items-center gap-1.5 rounded-xl font-bold text-xs h-9"
          >
            <Plus class="w-4 h-4" /> Tambah Item
          </Button>
        </div>

        <div
          v-if="!section.items?.length"
          class="text-center py-12 text-gray-400 text-sm"
        >
          Belum ada item ditambahkan.
        </div>

        <div
          v-else
          class="space-y-2"
        >
          <div
            v-for="item in section.items"
            :key="item.id"
            class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-white/10 bg-white/50 dark:bg-white/5"
          >
            <div class="flex items-center gap-4">
              <div
                v-if="item.image"
                class="w-12 h-12 rounded-xl overflow-hidden border border-white/10"
              >
                <img
                  :src="item.image"
                  class="w-full h-full object-cover"
                />
              </div>
              <div>
                <span class="font-bold text-sm text-foreground block">{{ item.title }}</span>
                <span
                  v-if="item.value"
                  class="text-xs text-primary font-bold"
                  >{{ item.value }}</span
                >
                <p
                  v-if="item.description"
                  class="text-xs text-gray-400 mt-1 line-clamp-1 max-w-lg"
                >
                  {{ item.description }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-1">
              <Button
                variant="ghost"
                size="icon"
                @click="startEdit(item)"
              >
                <Edit class="w-4 h-4" />
              </Button>
              <Button
                variant="ghost"
                size="icon"
                @click="handleDelete(item.id)"
                class="text-destructive hover:text-destructive hover:bg-destructive/10"
              >
                <Trash2 class="w-4 h-4" />
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BuilderLayout>
</template>
