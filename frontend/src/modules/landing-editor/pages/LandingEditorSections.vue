<script setup>
import { ref, onMounted } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import BuilderLayout from '../components/BuilderLayout.vue'
import { Eye, EyeOff, Edit, MoveUp, MoveDown, GripVertical } from 'lucide-vue-next'
import VueDraggable from 'vuedraggable'

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

async function toggleVisibility(sec) {
  sec.is_visible = !sec.is_visible
  await saveSectionOrdering()
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
          class="space-y-2"
          handle=".section-drag-handle"
          animation="200"
          @end="onDragEnd"
        >
          <template #item="{ element: sec, index: idx }">
            <div
              class="flex items-center justify-between p-4 rounded-xl border border-gray-200 dark:border-white/10 transition-colors section-drag-handle cursor-grab active:cursor-grabbing"
              :class="sec.is_visible ? 'bg-white/50 dark:bg-white/5 hover:bg-gray-50 dark:hover:bg-white/10' : 'bg-transparent opacity-60 hover:bg-white/50 dark:hover:bg-white/5'"
            >
              <div class="flex items-center gap-4">
                <div>
                  <div class="flex items-center gap-2">
                    <span
                      class="text-xs font-bold uppercase tracking-wider text-primary bg-primary/15 px-2 py-0.5 rounded"
                    >
                      {{ sec.type }}
                    </span>
                    <span class="font-extrabold text-sm text-foreground">{{
                      sec.title || 'Section Tanpa Judul'
                    }}</span>
                  </div>
                  <span class="text-xs text-gray-400 block mt-1"
                    >Item: {{ sec.items?.length || 0 }} sub-konten terdaftar</span
                  >
                </div>
              </div>

              <!-- Action Controls -->
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  @click="toggleVisibility(sec)"
                  class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-400"
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
                  class="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-primary/15 hover:bg-primary/25 text-primary font-bold text-xs"
                >
                  <Edit class="w-3.5 h-3.5" /> Edit Konten
                </router-link>
              </div>
            </div>
          </template>
        </VueDraggable>
      </div>
    </div>
  </BuilderLayout>
</template>
