<script setup>
import { ref, computed, watch } from 'vue'
import { 
  FolderOpen, 
  Play, 
  FileText, 
  Download, 
  ChevronLeft, 
  ChevronRight 
} from 'lucide-vue-next'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import AppCard from '@/components/app-card/AppCard.vue'
import { formatNumber } from '@/utils/formatNumber'

const props = defineProps({
  subjectName: {
    type: String,
    default: ''
  },
  materials: {
    type: Array,
    default: () => []
  },
  assessments: {
    type: Array,
    default: () => []
  },
  selectedMaterialId: {
    type: String,
    default: 'all'
  }
})

const emit = defineEmits(['update:selectedMaterialId', 'download'])

const materiPage = ref(1)

// Reset page when selectedMaterialId changes
watch(() => props.selectedMaterialId, () => {
  materiPage.value = 1
})

const currentMaterial = computed(() => {
  if (!props.materials || props.materials.length === 0) return null
  if (!props.selectedMaterialId || props.selectedMaterialId === 'all') {
    return props.materials[0]
  }
  return props.materials.find(m => String(m.id) === String(props.selectedMaterialId)) || props.materials[0]
})

const currentMateriItems = computed(() => {
  const items = []
  const mat = currentMaterial.value
  if (!mat) return items

  // File download item
  items.push({
    id: mat.id,
    type: 'file',
    title: mat.title,
    file_name: mat.file_name,
    file_type: mat.file_type || 'pdf',
    file_size: mat.file_size || 0,
    created_at: mat.created_at
  })

  // Assessments linked to this material
  if (props.assessments) {
    props.assessments.forEach(a => {
      if (String(a.material_id) === String(mat.id)) {
        items.push({
          id: a.id,
          type: 'assessment',
          category: a.category,
          title: a.title,
          score: a.score
        })
      }
    })
  }

  return items
})

const paginatedMateriItems = computed(() => {
  const start = (materiPage.value - 1) * 3
  return currentMateriItems.value.slice(start, start + 3)
})

const totalMateriPages = computed(() => {
  return Math.ceil(currentMateriItems.value.length / 3) || 1
})

const formatSize = (bytes) => {
  if (!bytes) return '0 KB'
  const kb = bytes / 1024
  if (kb < 1024) return `${kb.toFixed(1)} KB`
  return `${(kb / 1024).toFixed(1)} MB`
}
</script>

<template>
  <AppCard header-class="pb-3" content-class="space-y-3">
    <template #header>
      <div class="flex flex-wrap items-center justify-between gap-3 w-full">
        <div class="flex items-center gap-3">
          <div class="rounded-lg bg-muted p-2">
            <FolderOpen class="size-5 text-muted-foreground" />
          </div>
          <div>
            <div class="font-semibold text-foreground text-sm sm:text-base">{{ subjectName }}</div>
            <div class="text-xs text-muted-foreground">Unduh materi & nilai terkait</div>
          </div>
        </div>

        <Select 
          :model-value="selectedMaterialId"
          @update:model-value="val => emit('update:selectedMaterialId', val)"
        >
          <SelectTrigger class="w-[200px] h-9 bg-background/40">
            <SelectValue placeholder="Pilih Materi Pelajaran" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem 
              v-for="m in materials" 
              :key="m.id" 
              :value="String(m.id)"
            >
              {{ m.title }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>
    </template>

    <!-- List Items (Materials & Scores) -->
    <div v-if="paginatedMateriItems.length > 0" class="space-y-2.5 min-h-[180px]">
      <div 
        v-for="item in paginatedMateriItems" 
        :key="item.id + '-' + item.type"
        class="flex items-center justify-between p-3 rounded-xl border border-border/40 bg-background/20 backdrop-blur-sm"
      >
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-lg bg-muted/60 shrink-0">
            <Play v-if="item.type === 'file'" class="size-4 text-blue-500" />
            <FileText v-else class="size-4 text-amber-500" />
          </div>
          <div class="text-left min-w-0">
            <div class="text-xs sm:text-sm font-semibold text-foreground truncate max-w-[240px] sm:max-w-[320px]">
              {{ item.title }}
            </div>
            <div class="text-[10px] text-muted-foreground">
              <span v-if="item.type === 'file'">
                Berkas &bull; {{ formatSize(item.file_size) }}
              </span>
              <span v-else>
                {{ item.category === 'tugas' ? 'Nilai Tugas' : 'Nilai Ujian' }}
              </span>
            </div>
          </div>
        </div>

        <div>
          <button
            v-if="item.type === 'file'"
            class="flex items-center gap-1 text-xs font-semibold text-blue-500 hover:text-blue-600 transition-colors bg-blue-500/10 px-2.5 py-1 rounded-lg cursor-pointer"
            @click="emit('download', item.id, item.file_name)"
          >
            <Download class="size-3" /> Unduh
          </button>
          <div v-else class="text-right">
            <span class="text-sm font-bold text-foreground bg-amber-500/10 px-2.5 py-1 rounded-lg">
              {{ item.score !== null ? formatNumber(item.score) : '-' }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="flex flex-col items-center justify-center py-12 text-center text-muted-foreground border border-dashed rounded-xl min-h-[180px]">
      <FolderOpen class="size-8 mb-2 opacity-50" />
      <p class="text-sm font-semibold">Belum Ada Materi</p>
      <p class="text-xs">Guru belum mengunggah materi pelajaran.</p>
    </div>

    <!-- Pagination -->
    <div v-if="totalMateriPages > 1" class="flex items-center justify-between pt-3 border-t border-border/40">
      <span class="text-[10px] sm:text-xs text-muted-foreground">Halaman {{ materiPage }} dari {{ totalMateriPages }}</span>
      <div class="flex gap-1.5">
        <button
          v-if="materiPage > 1"
          class="p-1.5 rounded-lg border border-border/40 hover:bg-muted/40 transition-colors cursor-pointer"
          @click="materiPage--"
        >
          <ChevronLeft class="size-4" />
        </button>
        <button
          v-if="materiPage < totalMateriPages"
          class="p-1.5 rounded-lg border border-border/40 hover:bg-muted/40 transition-colors cursor-pointer"
          @click="materiPage++"
        >
          <ChevronRight class="size-4" />
        </button>
      </div>
    </div>
  </AppCard>
</template>
