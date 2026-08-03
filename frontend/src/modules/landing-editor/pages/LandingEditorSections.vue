<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Save, Plus, Trash2, Edit, GripVertical, Eye, EyeOff } from 'lucide-vue-next'
import VueDraggable from 'vuedraggable'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { toast } from 'vue-sonner'

const store = useLandingEditorStore()
const sections = ref([])

const selectedSectionIndex = ref(null)
const editingSectionItem = ref(null)
const isAddingSectionItem = ref(false)

const sectionItemForm = ref({
  id: null,
  title: '',
  description: '',
  icon: 'star',
  image: '',
  link: '',
  value: ''
})

const activeSectionType = computed(() => {
  if (selectedSectionIndex.value === null) return null
  return sections.value[selectedSectionIndex.value]?.type || null
})

onMounted(async () => {
  await fetchSections()
})

async function fetchSections() {
  await store.fetchLandingPage()
  if (store.landingPage) {
    sections.value = Array.isArray(store.landingPage.sections)
      ? JSON.parse(JSON.stringify(store.landingPage.sections))
      : []
  }
}

const openSectionItemEditor = (sectionIdx, item = null) => {
  selectedSectionIndex.value = sectionIdx
  if (item) {
    editingSectionItem.value = item
    isAddingSectionItem.value = false
    sectionItemForm.value = { ...item }
  } else {
    editingSectionItem.value = null
    isAddingSectionItem.value = true
    sectionItemForm.value = { title: '', description: '', icon: 'star', image: '', link: '', value: '' }
  }
}

const saveSectionItem = async () => {
  const section = sections.value[selectedSectionIndex.value]
  if (!section) return

  if (!Array.isArray(section.items)) section.items = []

  if (isAddingSectionItem.value) {
    const newItem = { id: Date.now(), ...sectionItemForm.value }
    section.items.push(newItem)
    toast.success('Item berhasil ditambahkan!')
  } else {
    const idx = section.items.findIndex(i => i.id === editingSectionItem.value.id)
    if (idx !== -1) {
      section.items[idx] = { ...section.items[idx], ...sectionItemForm.value }
      toast.success('Item berhasil diperbarui!')
    }
  }
  closeSectionItemEditor()
  await saveSectionsToBackend(false)
}

const showDeleteDialog = ref(false)
const itemToDelete = ref(null)

const confirmDeleteSectionItem = (sectionIdx, item) => {
  itemToDelete.value = { sectionIdx, id: item.id, title: item.title }
  showDeleteDialog.value = true
}

const executeDeleteSectionItem = async () => {
  if (!itemToDelete.value) return
  const { sectionIdx, id } = itemToDelete.value
  const section = sections.value[sectionIdx]
  if (section && Array.isArray(section.items)) {
    section.items = section.items.filter(i => i.id !== id)
    toast.success('Item berhasil dihapus!')
    await saveSectionsToBackend(false)
  }
  showDeleteDialog.value = false
  itemToDelete.value = null
}

const closeSectionItemEditor = () => {
  selectedSectionIndex.value = null
  editingSectionItem.value = null
  isAddingSectionItem.value = false
}

async function onSectionItemImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { toast.error('Ukuran file maksimal 2MB'); return }
  try {
    const res = await store.uploadImage(file)
    sectionItemForm.value.image = res.url
    toast.success('Gambar item berhasil diupload!')
  } catch (err) {
    toast.error(err.message)
  }
}

async function onDragEnd() {
  sections.value.forEach((sec, index) => {
    sec.sort_order = index + 1
  })
  await saveSectionsToBackend(false, 'Urutan section berhasil diperbarui!')
}

async function onItemDragEnd() {
  await saveSectionsToBackend(false, 'Urutan item berhasil diperbarui!')
}

const showVisibilityDialog = ref(false)
const sectionToToggle = ref(null)

function confirmToggleVisibility(sec) {
  sectionToToggle.value = sec
  showVisibilityDialog.value = true
}

async function executeToggleVisibility() {
  if (sectionToToggle.value) {
    sectionToToggle.value.is_visible = !sectionToToggle.value.is_visible
    await saveSectionsToBackend(false, 'Visibilitas section berhasil diperbarui!')
    showVisibilityDialog.value = false
    sectionToToggle.value = null
  }
}

async function handleSave() {
  await saveSectionsToBackend(true, 'Semua perubahan section berhasil disimpan!')
}

async function saveSectionsToBackend(isFormSubmit = false, successMsg = '') {
  try {
    await store.updateSectionOrders([...sections.value])
    if (successMsg) toast.success(successMsg)
    else if (isFormSubmit) toast.success('Kelola section berhasil disimpan!')
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
      <h4 class="text-sm font-bold text-foreground">Kelola Section Konten</h4>

      <!-- Inline Section Item Sub-Editor (Persis Super Admin) -->
      <div
        v-if="selectedSectionIndex !== null"
        class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4"
      >
        <div class="flex items-center gap-2 mb-4">
          <h5 class="font-bold text-xs text-primary">
            {{ isAddingSectionItem ? 'Tambah Item Baru' : 'Edit Item' }}
          </h5>
          <span class="text-[10px] text-gray-400 font-medium">untuk section</span>
          <span class="text-[9px] font-extrabold uppercase tracking-widest text-primary bg-primary/15 px-2 py-0.5 rounded-md">
            {{ sections[selectedSectionIndex]?.title || sections[selectedSectionIndex]?.type || 'Tanpa Judul' }}
          </span>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Judul Item</Label>
            <Input
              type="text"
              v-model="sectionItemForm.title"
              maxlength="100"
              class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="Masukkan nama atau judul item"
            />
          </div>
          
          <div v-if="['programs'].includes(activeSectionType)">
            <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Link (Tautan) - Opsional</Label>
            <Input
              type="text"
              v-model="sectionItemForm.link"
              maxlength="255"
              class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="https://..."
            />
          </div>

          <div v-if="['stats', 'testimonials'].includes(activeSectionType)">
            <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">
              {{ activeSectionType === 'stats' ? 'Angka Statistik' : 'Nilai / Peran' }}
            </Label>
            <Input
              type="text"
              v-model="sectionItemForm.value"
              maxlength="50"
              class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              :placeholder="activeSectionType === 'stats' ? 'misal: 100+' : 'misal: Wali Murid'"
            />
          </div>

          <div v-if="['features'].includes(activeSectionType)">
            <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Nama Ikon (Opsional)</Label>
            <Input
              type="text"
              v-model="sectionItemForm.icon"
              maxlength="50"
              class="w-full h-10 px-3 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              placeholder="misal: star, award, book"
            />
          </div>

          <div v-if="['features', 'programs', 'testimonials', 'faq'].includes(activeSectionType)" class="md:col-span-2">
            <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">
              {{ activeSectionType === 'faq' ? 'Jawaban' : 'Deskripsi Singkat' }}
            </Label>
            <Textarea
              v-model="sectionItemForm.description"
              rows="2"
              maxlength="300"
              class="w-full px-3 py-2 border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 rounded-xl text-xs text-foreground focus-visible:ring-1 focus-visible:border-primary/50"
              :placeholder="activeSectionType === 'faq' ? 'Tuliskan jawaban pertanyaan...' : 'Tuliskan deskripsi ringkas...'"
            ></Textarea>
          </div>

          <div v-if="['programs', 'gallery', 'testimonials'].includes(activeSectionType)" class="md:col-span-2">
            <Label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Media Visual (Opsional)</Label>
            <div class="flex items-center gap-4 bg-white/30 dark:bg-background/20 p-3 rounded-xl border border-gray-100 dark:border-white/5">
              <div class="w-12 h-12 rounded-lg border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-background/30 flex items-center justify-center overflow-hidden shrink-0">
                <img v-if="sectionItemForm.image" :src="sectionItemForm.image" class="w-full h-full object-cover" />
                <span v-else class="text-lg opacity-40">🖼️</span>
              </div>
              <div>
                <label class="px-4 py-2 inline-flex items-center border border-gray-200 dark:border-white/10 bg-white dark:bg-background/50 hover:bg-gray-50 dark:hover:bg-white/5 rounded-lg font-bold text-xs cursor-pointer text-foreground transition-colors shadow-sm">
                  Pilih File Gambar
                  <input type="file" @change="onSectionItemImageUpload" class="sr-only" accept="image/*" />
                </label>
                <p class="text-[10px] text-muted-foreground mt-1.5 font-medium">Format: JPG, PNG, atau WebP.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2">
          <Button
            type="button"
            variant="outline"
            size="sm"
            @click="closeSectionItemEditor"
            class="rounded-lg text-xs"
          >Batal</Button>
          <Button
            type="button"
            size="sm"
            @click="saveSectionItem"
            class="rounded-lg text-xs font-bold"
          >Simpan</Button>
        </div>
      </div>

      <!-- List Section items (Persis Super Admin) -->
      <div v-else class="space-y-6">
        <VueDraggable v-model="sections" item-key="id" class="space-y-6 relative" handle=".section-drag-handle" animation="200" @end="onDragEnd">
          <template #item="{ element: sec, index: secIdx }">
            <div
              class="glass-mini p-5 border border-gray-100 dark:border-white/10 rounded-2xl space-y-3 section-drag-handle transition-colors cursor-grab active:cursor-grabbing hover:bg-white/50 dark:hover:bg-white/5 group/section"
              :class="sec.is_visible ? '' : 'opacity-60'"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 sm:gap-3">
                  <div class="p-1.5 rounded-lg text-gray-400 dark:text-white/30 group-hover/section:text-primary group-has-[.item-drag-handle:hover]/section:!text-gray-400 dark:group-has-[.item-drag-handle:hover]/section:!text-white/30 transition-colors cursor-grab active:cursor-grabbing" title="Tahan dan geser untuk memindahkan urutan">
                    <GripVertical class="w-5 h-5" />
                  </div>
                  <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/15 px-2 py-0.5 rounded">{{ sec.type || 'SECTION' }}</span>
                    <div class="font-extrabold text-sm text-foreground mt-1">{{ sec.title || 'Section Tanpa Judul' }}</div>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <button
                    type="button"
                    @click.stop="confirmToggleVisibility(sec)"
                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-400 transition-colors"
                    title="Sembunyikan / Tampilkan Section"
                  >
                    <Eye v-if="sec.is_visible" class="w-4 h-4 text-primary" />
                    <EyeOff v-else class="w-4 h-4 text-gray-400" />
                  </button>
                  <button
                    type="button"
                    @click.stop="openSectionItemEditor(secIdx)"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary/15 hover:bg-primary/25 text-primary font-bold text-xs transition-colors"
                  >
                    <Plus class="w-3.5 h-3.5" /> Tambah Item
                  </button>
                </div>
              </div>
              
              <VueDraggable v-if="sec.items && sec.items.length" v-model="sec.items" item-key="id" class="space-y-2 relative" handle=".item-drag-handle" animation="200" @end="onItemDragEnd">
                <template #item="{ element: item, index: itemIdx }">
                  <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-white/5 text-xs item-drag-handle transition-colors cursor-grab active:cursor-grabbing hover:bg-gray-50 dark:hover:bg-white/10 group/item">
                    <div class="flex gap-2 sm:gap-3 items-center">
                      <div class="p-1.5 rounded-lg text-gray-400 dark:text-white/30 group-hover/item:text-primary transition-colors cursor-grab active:cursor-grabbing" title="Tahan dan geser untuk memindahkan urutan">
                        <GripVertical class="w-4 h-4" />
                      </div>
                      <div v-if="item.image" class="w-10 h-10 rounded-lg overflow-hidden shrink-0 border border-gray-200 dark:border-white/10">
                        <img :src="item.image" class="w-full h-full object-cover" />
                      </div>
                      <div v-else-if="item.icon" class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0 border border-primary/20">
                        <span class="font-bold text-xs uppercase">{{ item.icon.substring(0, 2) }}</span>
                      </div>
                      <div>
                        <span class="font-semibold text-foreground">{{ item.title }}</span>
                        <span v-if="item.value" class="text-primary font-bold ml-2">({{ item.value }})</span>
                        <p class="text-[10px] text-muted-foreground mt-0.5 line-clamp-1">{{ item.description }}</p>
                      </div>
                    </div>
                    <div class="flex gap-1 shrink-0">
                      <button type="button" @click.stop="openSectionItemEditor(secIdx, item)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 transition-colors"><Edit class="w-3.5 h-3.5" /></button>
                      <button type="button" @click.stop="confirmDeleteSectionItem(secIdx, item)" class="p-1.5 rounded-lg hover:bg-destructive/10 text-destructive transition-colors"><Trash2 class="w-3.5 h-3.5" /></button>
                    </div>
                  </div>
                </template>
              </VueDraggable>
              <div v-else class="text-xs text-muted-foreground/70 italic p-2 bg-background/30 rounded-xl text-center border border-dashed border-border/50">
                Belum ada item konten pada section ini. Klik tombol Tambah Item di atas.
              </div>
            </div>
          </template>
        </VueDraggable>
      </div>

      <!-- Submit -->
      <div v-if="selectedSectionIndex === null" class="flex justify-end pt-2">
        <Button type="submit" class="flex items-center gap-2 px-6 rounded-xl font-bold h-11" :disabled="store.saving">
          <Save class="w-4 h-4" />
          {{ store.saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </Button>
      </div>
    </form>

    <!-- Alert Dialog Konfirmasi Hapus Item -->
    <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
      <AlertDialogContent class="sm:max-w-md">
        <AlertDialogHeader>
          <AlertDialogTitle>Konfirmasi Hapus Item</AlertDialogTitle>
          <AlertDialogDescription v-if="itemToDelete">
            Apakah Anda yakin ingin menghapus item <strong>{{ itemToDelete.title || 'ini' }}</strong> dari section?
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel @click="showDeleteDialog = false">Batal</AlertDialogCancel>
          <AlertDialogAction @click="executeDeleteSectionItem">Ya, Hapus</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>

    <!-- Alert Dialog Konfirmasi Visibilitas Section -->
    <AlertDialog :open="showVisibilityDialog" @update:open="showVisibilityDialog = $event">
      <AlertDialogContent class="sm:max-w-md">
        <AlertDialogHeader>
          <AlertDialogTitle>Konfirmasi Visibilitas Section</AlertDialogTitle>
          <AlertDialogDescription v-if="sectionToToggle">
            Anda akan {{ sectionToToggle.is_visible ? 'menyembunyikan' : 'menampilkan' }} bagian <strong>{{ sectionToToggle.title || 'Section ini' }}</strong> dari halaman publik.
            Apakah Anda yakin ingin melanjutkan?
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel @click="showVisibilityDialog = false">Batal</AlertDialogCancel>
          <AlertDialogAction @click="executeToggleVisibility">Ya, Lanjutkan</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  </BuilderLayout>
</template>
