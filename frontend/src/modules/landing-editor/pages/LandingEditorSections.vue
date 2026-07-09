<script setup>
import { ref, onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Eye, EyeOff, Edit, MoveUp, MoveDown, GripVertical } from 'lucide-vue-next'
import VueDraggable from 'vuedraggable'
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
import { Button } from '@/components/ui/button'

import { toast } from 'vue-sonner'

const store = useLandingEditorStore()
const localSections = ref([])

onMounted(async () => {
  await fetchSections()
})

async function fetchSections() {
  await store.fetchLandingPage()
  if (store.landingPage) {
    localSections.value = [...(store.landingPage.sections || [])]
  }
}

async function move(idx, direction) {
  const targetIdx = idx + direction
  if (targetIdx < 0 || targetIdx >= localSections.value.length) return

  // swap
  const temp = localSections.value[idx]
  localSections.value[idx] = localSections.value[targetIdx]
  localSections.value[targetIdx] = temp

  await onDragEnd()
}

async function onDragEnd() {
  // recalculate sort_order
  localSections.value.forEach((sec, index) => {
    sec.sort_order = index + 1
  })
  await saveSectionOrdering()
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
    await saveSectionOrdering()
    showVisibilityDialog.value = false
    sectionToToggle.value = null
  }
}

async function saveSectionOrdering() {
  try {
    const payload = localSections.value.map(s => ({
      id: s.id,
      is_visible: s.is_visible,
      sort_order: s.sort_order,
      title: s.title,
      subtitle: s.subtitle
    }))
    await store.updateSectionOrders(payload)
    toast.success('Urutan dan visibilitas section berhasil diperbarui!')
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
      v-else
      class="space-y-6"
    >
      <div class="glass-mini rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-white/10 space-y-4">
        <h3 class="text-lg font-bold text-foreground">Susun dan Atur Tampilan Section</h3>
        <p class="text-xs text-gray-400">
          Geser (drag and drop) baris section di bawah ini untuk mengatur urutannya. 
          Gunakan tombol mata untuk menyembunyikan/menampilkan section dari publik. 
          <br/><span class="text-primary/80">Keterangan:</span> Teks <b>"Item"</b> menunjukkan jumlah sub-konten yang ada di dalam section tersebut (misal: jumlah pertanyaan di FAQ, jumlah testimoni, atau jumlah foto di galeri).
        </p>

        <VueDraggable
          v-model="localSections"
          item-key="id"
          class="space-y-6 relative"
          handle=".section-drag-handle"
          animation="200"
          tag="transition-group"
          :component-data="{ name: 'list' }"
          @end="onDragEnd"
        >
          <template #item="{ element: sec, index: idx }">
            <div
              class="glass-mini p-5 border border-gray-100 dark:border-white/10 rounded-2xl space-y-3 section-drag-handle cursor-grab active:cursor-grabbing hover:bg-white/50 dark:hover:bg-white/5 transition-colors"
              :class="sec.is_visible ? '' : 'opacity-60'"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span
                    class="text-xs font-bold uppercase tracking-wider text-primary bg-primary/15 px-2 py-0.5 rounded"
                  >
                    {{ sec.type || 'SECTION' }}
                  </span>
                  <span class="font-extrabold text-sm text-foreground">{{
                    sec.title || 'Section Tanpa Judul'
                  }}</span>
                </div>

                <!-- Action Controls -->
                <div class="flex items-center gap-1.5">
                  <div class="flex items-center mr-2 border-r border-gray-200 dark:border-white/10 pr-2">
                    <button
                      type="button"
                      @click.stop="move(idx, -1)"
                      :disabled="idx === 0"
                      class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-400 transition-colors disabled:opacity-30"
                    >
                      <MoveUp class="w-4 h-4" />
                    </button>
                    <button
                      type="button"
                      @click.stop="move(idx, 1)"
                      :disabled="idx === localSections.length - 1"
                      class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-400 transition-colors disabled:opacity-30"
                    >
                      <MoveDown class="w-4 h-4" />
                    </button>
                  </div>
                  <button
                    type="button"
                    @click="confirmToggleVisibility(sec)"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-400 transition-colors"
                  >
                    <Eye
                      v-if="sec.is_visible"
                      class="w-4 h-4 text-primary"
                    />
                    <EyeOff
                      v-else
                      class="w-4 h-4 text-gray-500"
                    />
                  </button>
                  <router-link
                    :to="`/landing-editor/sections/${sec.id}`"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary/15 hover:bg-primary/25 text-primary font-bold text-xs transition-colors"
                  >
                    <Edit class="w-3.5 h-3.5" /> Edit Konten
                  </router-link>
                </div>
              </div>

              <!-- Preview Daftar Item statis -->
              <div v-if="sec.items?.length" class="space-y-2 pointer-events-none">
                <div
                  v-for="item in sec.items.slice(0, 3)"
                  :key="item.id"
                  class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-white/5 text-xs"
                >
                  <div class="flex gap-4">
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
                </div>
                <div v-if="sec.items.length > 3" class="text-[10px] text-primary/70 font-bold ml-1">
                  + {{ sec.items.length - 3 }} item lainnya...
                </div>
              </div>
              <div v-else class="text-xs text-gray-400 mt-2 ml-1">
                Belum ada item ditambahkan pada section ini.
              </div>
            </div>
          </template>
        </VueDraggable>
      </div>
    </div>

    <!-- Alert Dialog Konfirmasi Visibilitas Section -->
    <AlertDialog :open="showVisibilityDialog" @update:open="showVisibilityDialog = $event">
      <AlertDialogContent class="sm:max-w-md">
        <AlertDialogHeader>
          <AlertDialogTitle>Konfirmasi Visibilitas Section</AlertDialogTitle>
          <AlertDialogDescription v-if="sectionToToggle">
            Anda akan {{ sectionToToggle.is_visible ? 'menyembunyikan' : 'menampilkan' }} bagian <strong>{{ sectionToToggle.title || 'Section ini' }}</strong> dari halaman publik. 
            Apakah Anda yakin ingin melanjutkan tindakan ini?
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

<style scoped>
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.4s cubic-bezier(0.55, 0, 0.1, 1);
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateY(30px);
}

.list-leave-active {
  position: absolute;
  width: 100%;
}
</style>
