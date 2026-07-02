<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Book, BookOpen, Users, FolderOpen, Plus, Download, Eye, Pencil, Trash2 } from 'lucide-vue-next'
import { toast } from 'vue-sonner'

// UI Components
import { Badge } from '@/components/ui/badge'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import {
  AlertDialog,
  AlertDialogContent,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogCancel,
  AlertDialogAction
} from '@/components/ui/alert-dialog'

// Custom Components
import GuruMataPelajaranSheet from './GuruMataPelajaranSheet.vue'

// Common Components
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'

// Helpers
import { formatNumber } from '@/utils/formatNumber'

// Composable
import { useAkademik } from '../composables/useAkademik'
import * as akademikService from '@/services/akademikService'

// --- Initialize Composable ---
const {
  subjects,
  classrooms,
  students,
  activeAcademicYear,
  materiList,
  tugasList,
  ujianList,
  isLoading,
  isSubmitting,
  draftForm,
  fetchActiveAcademicYear,
  fetchSubjects,
  fetchClassrooms,
  fetchStudents,
  fetchMaterials,
  fetchAssessments,
  saveMaterial,
  updateMaterial,
  saveAssessment,
  updateAssessment,
  deleteItem,
  toggleStatus,
  handleDownload,
  clearDraft,
  saveDraft
} = useAkademik()

// --- State ---
const selectedMapel = ref('')
const activeTab = ref('materi') // 'materi', 'tugas', 'ujian'

// Table Filters
const filterValues = ref({
  title: 'all',
  kelas: 'all'
})

// Pagination states
const page = ref(1)
const perPage = ref(5)

// --- Dialog state for delete ---
const deleteDialogOpen = ref(false)
const pendingDeleteItem = ref(null)

// --- Fetch Data on Mount ---
onMounted(async () => {
  await fetchActiveAcademicYear()
  await fetchSubjects()
  
  // Set default subject
  if (subjects.value.length > 0) {
    selectedMapel.value = String(subjects.value[0].id)
  }
})

// Watch subject change to refresh classrooms and data
watch(selectedMapel, async (newMapel) => {
  if (newMapel) {
    await fetchClassrooms(newMapel)
    refreshData()
  }
})

// Reset filters and refetch when tab changes
watch(activeTab, () => {
  filterValues.value = {
    title: 'all',
    kelas: 'all'
  }
  page.value = 1
  refreshData()
})

const refreshData = () => {
  if (!selectedMapel.value) return
  
  if (activeTab.value === 'materi') {
    fetchMaterials(selectedMapel.value)
  } else {
    fetchAssessments(selectedMapel.value, activeTab.value)
  }
}

// --- Computed Stats ---
const avgTugas = computed(() => {
  let total = 0
  let count = 0
  tugasList.value.forEach(item => {
    if (item.scores) {
      item.scores.forEach(s => {
        total += Number(s.score)
        count++
      })
    }
  })
  return count > 0 ? (total / count) : 0
})

const avgUjian = computed(() => {
  let total = 0
  let count = 0
  ujianList.value.forEach(item => {
    if (item.scores) {
      item.scores.forEach(s => {
        total += Number(s.score)
        count++
      })
    }
  })
  return count > 0 ? (total / count) : 0
})

const totalMateriUploaded = computed(() => materiList.value.length)

// --- Table Configuration ---
const activeTabLabel = computed(() => {
  if (activeTab.value === 'materi') return 'Materi'
  if (activeTab.value === 'tugas') return 'Tugas'
  return 'Ujian'
})

const tableColumns = computed(() => {
  const columns = [
    { key: 'title', label: activeTabLabel.value + ' Pelajaran' },
    { key: 'classroom_name', label: 'Kelas' },
    { key: 'semester_name', label: 'Semester' }
  ]
  
  if (activeTab.value !== 'materi') {
    columns.push({ key: 'type_label', label: 'Tipe' })
  }
  
  columns.push({ key: 'uploaded_by_name', label: 'Diupload Oleh' })
  columns.push({ key: 'status', label: 'Status', badge: true })
  columns.push({ key: 'actions', label: 'Aksi' })
  
  return columns
})

const currentTabList = computed(() => {
  if (activeTab.value === 'materi') return materiList.value
  if (activeTab.value === 'tugas') return tugasList.value
  return ujianList.value
})

const formatTypeLabel = (type) => {
  const labels = {
    tugas_sekolah: 'Tugas Sekolah',
    tugas_rumah: 'Tugas Rumah',
    ujian_harian: 'Ujian Harian',
    uts: 'UTS',
    uas: 'UAS'
  }
  return labels[type] || type
}

// Map raw items to include visual labels
const mappedItems = computed(() => {
  return currentTabList.value.map(item => ({
    ...item,
    classroom_name: item.classroom?.name || '',
    semester_name: item.academic_year ? `${item.academic_year.name} (${item.academic_year.semester === 'odd' ? 'Ganjil' : 'Genap'})` : '',
    type_label: item.type ? formatTypeLabel(item.type) : '',
    status: item.is_active ? 'Aktif' : 'Nonaktif'
  }))
})

const filterItemOptions = computed(() => {
  const titles = mappedItems.value.map(item => item.title)
  return [...new Set(titles)]
})

// Build dynamic filters for DataTableCard
const filtersConfig = computed(() => [
  {
    key: 'title',
    label: activeTabLabel.value,
    type: 'select',
    placeholder: 'Pilih ' + activeTabLabel.value,
    options: filterItemOptions.value.map(opt => ({ value: opt, label: opt }))
  },
  {
    key: 'kelas',
    label: 'Kelas',
    type: 'select',
    placeholder: 'Pilih Kelas',
    options: classrooms.value.map(c => ({ value: String(c.id), label: c.name }))
  }
])

const toolbarActions = computed(() => [
  {
    label: 'Tambah ' + activeTabLabel.value,
    icon: Plus,
    variant: 'default',
    click: openAddSheet
  }
])

const filteredItems = computed(() => {
  return mappedItems.value.filter(item => {
    const matchItem = filterValues.value.title === 'all' || item.title === filterValues.value.title
    const matchKelas = filterValues.value.kelas === 'all' || String(item.classroom_id) === String(filterValues.value.kelas)
    return matchItem && matchKelas
  })
})

const total = computed(() => filteredItems.value.length)
const from = computed(() => (page.value - 1) * perPage.value + 1)
const to = computed(() => Math.min(page.value * perPage.value, total.value))

// --- Data Sheet Form State ---
const showSheet = ref(false)
const sheetMode = ref('add') // 'add', 'edit', 'view'
const activeEditId = ref(null)

const form = ref({
  classroom_id: '',
  type: '',
  title: '',
  file: null,
  scores: {}
})

// --- CRUD Actions ---
const openAddSheet = () => {
  sheetMode.value = 'add'
  activeEditId.value = null
  
  // Load from draft if exists
  const currentDraft = draftForm.value[activeTab.value]
  form.value = {
    classroom_id: currentDraft.classroom_id || '',
    type: currentDraft.type || '',
    title: currentDraft.title || '',
    file: currentDraft.file || null,
    scores: { ...currentDraft.scores }
  }
  showSheet.value = true
}

const editItem = async (idOrItem, maybeItem) => {
  const item = maybeItem || idOrItem
  sheetMode.value = 'edit'
  activeEditId.value = item.id
  
  if (activeTab.value === 'materi') {
    form.value = {
      classroom_id: String(item.classroom_id),
      type: '',
      title: item.title,
      file: null,
      scores: {}
    }
  } else {
    // Fetch assessment detail with scores
    try {
      const res = await akademikService.getAssessment(item.id)
      const detail = res.data
      
      const scoresObj = {}
      if (detail.scores_list) {
        detail.scores_list.forEach(s => {
          scoresObj[s.student_id] = s.score
        })
      }
      
      form.value = {
        classroom_id: String(detail.classroom_id),
        type: detail.type,
        title: detail.title,
        file: null,
        scores: scoresObj
      }
    } catch (err) {
      toast.error('Gagal memuat detail penilaian.')
    }
  }
  showSheet.value = true
}

const viewItem = async (idOrItem, maybeItem) => {
  const item = maybeItem || idOrItem
  sheetMode.value = 'view'
  activeEditId.value = item.id
  
  if (activeTab.value === 'materi') {
    form.value = {
      classroom_id: String(item.classroom_id),
      type: '',
      title: item.title,
      file: null,
      scores: {}
    }
  } else {
    try {
      const res = await akademikService.getAssessment(item.id)
      const detail = res.data
      
      const scoresObj = {}
      if (detail.scores_list) {
        detail.scores_list.forEach(s => {
          scoresObj[s.student_id] = s.score
        })
      }
      
      form.value = {
        classroom_id: String(detail.classroom_id),
        type: detail.type,
        title: detail.title,
        file: null,
        scores: scoresObj
      }
    } catch (err) {
      toast.error('Gagal memuat detail penilaian.')
    }
  }
  showSheet.value = true
}

const triggerDelete = (item) => {
  pendingDeleteItem.value = item
  deleteDialogOpen.value = true
}

const confirmDelete = async () => {
  if (pendingDeleteItem.value) {
    const success = await deleteItem(activeTab.value, pendingDeleteItem.value.id)
    if (success) {
      refreshData()
    }
    deleteDialogOpen.value = false
    pendingDeleteItem.value = null
  }
}

const handleToggleStatus = async (item) => {
  const success = await toggleStatus(activeTab.value, item.id)
  if (success) {
    refreshData()
  }
}

const handleSave = async (savedForm) => {
  let success = false
  
  if (activeTab.value === 'materi') {
    const formData = new FormData()
    formData.append('subject_id', selectedMapel.value)
    formData.append('classroom_id', savedForm.classroom_id)
    formData.append('academic_year_id', String(activeAcademicYear.value?.id))
    formData.append('title', savedForm.title)
    
    if (savedForm.file) {
      formData.append('file', savedForm.file)
    }

    if (sheetMode.value === 'add') {
      success = await saveMaterial(formData)
    } else {
      success = await updateMaterial(activeEditId.value, formData)
    }
  } else {
    // Format scores payload: [{ student_id, score }]
    const scoresPayload = Object.keys(savedForm.scores).map(studentId => ({
      student_id: studentId,
      score: savedForm.scores[studentId] === '' ? 0 : Number(savedForm.scores[studentId])
    }))

    const payload = {
      subject_id: selectedMapel.value,
      classroom_id: savedForm.classroom_id,
      academic_year_id: String(activeAcademicYear.value?.id),
      category: activeTab.value,
      type: savedForm.type,
      title: savedForm.title,
      scores: scoresPayload
    }

    if (sheetMode.value === 'add') {
      success = await saveAssessment(payload)
    } else {
      success = await updateAssessment(activeEditId.value, payload)
    }
  }

  if (success) {
    showSheet.value = false
    refreshData()
  }
}

const handleSaveDraft = ({ tab, data }) => {
  saveDraft(tab, data)
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="space-y-1">
      <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-foreground leading-tight">
        Manajemen Akademik
      </h1>
      <p class="text-muted-foreground text-xs sm:text-sm leading-relaxed">
        Manejemen Nilai Akademik Siswa/i, dan Absensi Siswa/i
      </p>
    </div>

    <!-- Stat Cards Row -->
    <StatCardGrid cols="4">
      <StatCard
        label="Nilai Rata-Rata Tugas"
        :value="formatNumber(avgTugas)"
        sub="Dalam Satu Semester"
        trend="Turun 2,65%"
        trendDirection="down"
        :icon="Book"
        illustration="graded_paper"
        variant="violet"
      />
      <StatCard
        label="Rata-Rata Nilai Ujian"
        :value="formatNumber(avgUjian)"
        sub="Dalam Satu Semester"
        trend="Naik 2,65%"
        trendDirection="up"
        :icon="BookOpen"
        illustration="open_book"
        variant="emerald"
      />
      <StatCard
        label="Presentase Absensi"
        value="98%"
        sub="Dalam Satu Semester"
        trend="Naik 2,65%"
        trendDirection="up"
        :icon="Users"
        illustration="school_bell"
        variant="amber"
      />
      <StatCard
        label="Jumlah Materi Diupload"
        :value="totalMateriUploaded"
        sub="Total Berkas Pelajaran"
        :icon="FolderOpen"
        illustration="closed_book"
        variant="blue"
      />
    </StatCardGrid>

    <!-- Selection Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 glass-ui p-4 rounded-2xl shadow-sm border border-white/10">
      <div class="flex items-center gap-3">
        <span class="text-sm font-semibold text-foreground">Mata Pelajaran</span>
        <Select v-model="selectedMapel">
          <SelectTrigger class="w-[200px] h-9 bg-background/50 backdrop-blur-sm">
            <SelectValue placeholder="Pilih Mata Pelajaran" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem 
              v-for="sub in subjects" 
              :key="sub.id" 
              :value="String(sub.id)"
            >
              {{ sub.name }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <!-- Tab Buttons -->
      <div class="flex items-center gap-2 bg-muted/60 p-1.5 rounded-xl border border-white/5 shadow-inner">
        <button
          v-for="tab in ['materi', 'tugas', 'ujian']"
          :key="tab"
          class="px-5 py-1.5 rounded-lg text-sm font-medium transition-all capitalize"
          :class="activeTab === tab 
            ? 'bg-background text-foreground shadow-sm font-semibold border border-white/10' 
            : 'text-muted-foreground hover:text-foreground'"
          @click="activeTab = tab"
        >
          {{ tab }}
        </button>
      </div>
    </div>

    <!-- Reusable DataTableCard -->
    <div class="text-left">
      <DataTableCard
        :columns="tableColumns"
        :items="filteredItems"
        :filters="filtersConfig"
        v-model:filterValues="filterValues"
        illustration="open_book"
        :actions="toolbarActions"
        :from="from"
        :to="to"
        :total="total"
        :page="page"
        :per-page="perPage"
        @update:page="page = $event"
        @update:perPage="perPage = $event"
        delete-label="title"
      >
        <!-- Custom cell status override -->
        <template #cell-status="{ item }">
          <Badge 
            :variant="item.is_active ? 'green' : 'gray'" 
            class="font-semibold cursor-pointer select-none transition-all hover:scale-105 active:scale-95"
            @click="handleToggleStatus(item)"
          >
            ✓ {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
          </Badge>
        </template>

        <!-- Custom Cell Actions Override -->
        <template #cell-actions="{ item }">
          <div class="flex items-center justify-center gap-3">
            <button
              v-if="activeTab === 'materi'"
              class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
              title="Unduh Berkas"
              @click="handleDownload(item.id)"
            >
              <Download class="size-4 transition-transform group-hover/btn:scale-110" />
              <span class="text-[9px] font-semibold leading-none">Unduh</span>
            </button>
            
            <button
              class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
              title="Lihat Rincian"
              @click="viewItem(item)"
            >
              <Eye class="size-4 transition-transform group-hover/btn:scale-110" />
              <span class="text-[9px] font-semibold leading-none">Detail</span>
            </button>

            <button
              class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
              title="Sunting"
              @click="editItem(item)"
            >
              <Pencil class="size-4 transition-transform group-hover/btn:scale-110" />
              <span class="text-[9px] font-semibold leading-none">Edit</span>
            </button>

            <button
              class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-destructive transition-colors"
              title="Hapus"
              @click="triggerDelete(item)"
            >
              <Trash2 class="size-4 transition-transform group-hover/btn:scale-110" />
              <span class="text-[9px] font-semibold leading-none">Hapus</span>
            </button>
          </div>
        </template>
      </DataTableCard>
    </div>

    <!-- Data Sheet Drawer -->
    <GuruMataPelajaranSheet
      v-model:open="showSheet"
      :mode="sheetMode"
      :active-tab="activeTab"
      :active-tab-label="activeTabLabel"
      :initial-form="form"
      :selected-subject-id="selectedMapel"
      :draft="draftForm[activeTab]"
      @save="handleSave"
      @save-draft="handleSaveDraft"
    />

    <!-- Local Delete Confirmation Dialog -->
    <AlertDialog :open="deleteDialogOpen">
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Konfirmasi Hapus</AlertDialogTitle>
          <AlertDialogDescription>
            Apakah Anda yakin ingin menghapus
            <span v-if="pendingDeleteItem" class="font-semibold text-foreground">
              "{{ pendingDeleteItem.title }}"
            </span>
            ? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel @click="deleteDialogOpen = false; pendingDeleteItem = null">Batal</AlertDialogCancel>
          <AlertDialogAction
            class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
            @click="confirmDelete"
          >
            Hapus
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  </div>
</template>
