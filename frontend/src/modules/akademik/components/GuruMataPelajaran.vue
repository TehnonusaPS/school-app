<script setup>
import { ref, computed, watch } from 'vue'
import { Book, BookOpen, Users, FolderOpen, Plus } from 'lucide-vue-next'
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

// Custom Components
import GuruMataPelajaranSheet from './GuruMataPelajaranSheet.vue'

// Common Components
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'

// Helpers
import { formatNumber } from '@/utils/formatNumber'

// --- State ---
const selectedMapel = ref('matematika')
const activeTab = ref('materi') // 'materi', 'tugas', 'ujian'

// Table Filters (Using DataTableCard's filterValues format)
const filterValues = ref({
  title: 'all',
  kelas: 'all',
  semester: 'all'
})

// Reset filters when tab changes
watch(activeTab, () => {
  filterValues.value = {
    title: 'all',
    kelas: 'all',
    semester: 'all'
  }
  page.value = 1
})

// --- Mock Students ---
const studentsMap = {
  'Kelas 1': [
    { id: 's1', name: 'Budi Santoso' },
    { id: 's2', name: 'Siti Aminah' },
    { id: 's3', name: 'Ahmad Wibowo' },
    { id: 's4', name: 'Dewi Lestari' },
    { id: 's5', name: 'Rani Wijaya' }
  ],
  'Kelas 2': [
    { id: 's6', name: 'Rian Hidayat' },
    { id: 's7', name: 'Putri Utami' },
    { id: 's8', name: 'Novianti' },
    { id: 's9', name: 'Eka Putra' },
    { id: 's10', name: 'Fajar Bahari' }
  ],
  'Kelas 3': [
    { id: 's11', name: 'Hadi Wijaya' },
    { id: 's12', name: 'Lina Marlina' },
    { id: 's13', name: 'Andi Pratama' },
    { id: 's14', name: 'Siska Amelia' },
    { id: 's15', name: 'Tommy Hermawan' }
  ]
}


// --- Mock Lists ---
const materiList = ref([
  { id: 'm1', title: 'Bab 1 : Pengenalan Aljabar Dasar.pdf', kelas: 'Kelas 1', semester: 'Semester 1', status: 'Aktif', grades: {} },
  { id: 'm2', title: 'Bab 2 : Relasi dan Fungsi Aljabar.pdf', kelas: 'Kelas 1', semester: 'Semester 1', status: 'Aktif', grades: {} },
  { id: 'm3', title: 'Bab 3 : Geometri Bidang Datar.docx', kelas: 'Kelas 1', semester: 'Semester 1', status: 'Aktif', grades: {} },
  { id: 'm4', title: 'Bab 4 : Teorema Phytagoras.pdf', kelas: 'Kelas 1', semester: 'Semester 1', status: 'Aktif', grades: {} },
  { id: 'm5', title: 'Bab 5 : Persamaan Linear Dua Variabel.pdf', kelas: 'Kelas 2', semester: 'Semester 1', status: 'Aktif', grades: {} },
  { id: 'm6', title: 'Bab 6 : Statistika Deskriptif.pptx', kelas: 'Kelas 2', semester: 'Semester 1', status: 'Aktif', grades: {} },
  { id: 'm7', title: 'Bab 7 : Peluang Kejadian Sederhana.pdf', kelas: 'Kelas 2', semester: 'Semester 2', status: 'Aktif', grades: {} },
  { id: 'm8', title: 'Bab 8 : Matriks dan Operasinya.pdf', kelas: 'Kelas 3', semester: 'Semester 1', status: 'Aktif', grades: {} },
  { id: 'm9', title: 'Bab 9 : Persamaan Lingkaran.docx', kelas: 'Kelas 3', semester: 'Semester 1', status: 'Aktif', grades: {} },
  { id: 'm10', title: 'Bab 10 : Turunan Fungsi Aljabar.pptx', kelas: 'Kelas 3', semester: 'Semester 2', status: 'Aktif', grades: {} }
])

const tugasList = ref([
  { id: 't1', title: 'Tugas I : Latihan Aljabar Dasar', kelas: 'Kelas 1', semester: 'Semester 1', status: 'Aktif', grades: { s1: 90, s2: 95, s3: 92, s4: 85, s5: 98 } },
  { id: 't2', title: 'Tugas II : Soal Relasi Fungsi', kelas: 'Kelas 1', semester: 'Semester 1', status: 'Aktif', grades: { s1: 80, s2: 85, s3: 82, s4: 78, s5: 88 } },
  { id: 't3', title: 'Tugas III : Gambar Geometri Datar', kelas: 'Kelas 1', semester: 'Semester 1', status: 'Aktif', grades: { s1: 95, s2: 90, s3: 92, s4: 90, s5: 94 } },
  { id: 't4', title: 'Tugas IV : Latihan Phytagoras', kelas: 'Kelas 2', semester: 'Semester 1', status: 'Aktif', grades: { s6: 85, s7: 90, s8: 88, s9: 82, s10: 89 } },
  { id: 't5', title: 'Tugas V : Soal Rata-Rata Statistika', kelas: 'Kelas 3', semester: 'Semester 1', status: 'Aktif', grades: { s11: 88, s12: 92, s13: 85, s14: 90, s15: 91 } }
])

const ujianList = ref([
  { id: 'u1', title: 'Ujian Harian I : Aljabar', kelas: 'Kelas 1', semester: 'Semester 1', status: 'Aktif', grades: { s1: 82, s2: 88, s3: 85, s4: 75, s5: 90 } },
  { id: 'u2', title: 'UTS Matematika Semester Ganjil', kelas: 'Kelas 1', semester: 'Semester 1', status: 'Aktif', grades: { s1: 88, s2: 92, s3: 90, s4: 82, s5: 95 } },
  { id: 'u3', title: 'Ujian Harian II : Geometri', kelas: 'Kelas 2', semester: 'Semester 1', status: 'Aktif', grades: { s6: 80, s7: 85, s8: 82, s9: 88, s10: 84 } },
  { id: 'u4', title: 'Ujian Harian III : Statistika', kelas: 'Kelas 2', semester: 'Semester 2', status: 'Aktif', grades: { s6: 78, s7: 82, s8: 80, s9: 85, s10: 81 } },
  { id: 'u5', title: 'UAS Matematika Semester Ganjil', kelas: 'Kelas 3', semester: 'Semester 1', status: 'Aktif', grades: { s11: 85, s12: 90, s13: 88, s14: 82, s15: 92 } }
])

// --- Computed Stats ---
const avgTugas = computed(() => {
  let total = 0
  let count = 0
  tugasList.value.forEach(item => {
    if (item.grades) {
      Object.values(item.grades).forEach(grade => {
        total += Number(grade)
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
    if (item.grades) {
      Object.values(item.grades).forEach(grade => {
        total += Number(grade)
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
  return [
    { key: 'title', label: activeTabLabel.value + ' Pelajaran' },
    { key: 'kelas', label: 'Kelas' },
    { key: 'semester', label: 'Semester' },
    { key: 'status', label: 'Status', badge: true },
    { key: 'actions', label: 'Aksi' }
  ]
})

// Current working list for active tab
const currentTabList = computed(() => {
  if (activeTab.value === 'materi') return materiList.value
  if (activeTab.value === 'tugas') return tugasList.value
  return ujianList.value
})

const filterItemOptions = computed(() => {
  const titles = currentTabList.value.map(item => item.title)
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
    options: [
      { value: 'Kelas 1', label: 'Kelas 1' },
      { value: 'Kelas 2', label: 'Kelas 2' },
      { value: 'Kelas 3', label: 'Kelas 3' }
    ]
  },
  {
    key: 'semester',
    label: 'Semester',
    type: 'select',
    placeholder: 'Pilih Semester',
    options: [
      { value: 'Semester 1', label: 'Semester 1' },
      { value: 'Semester 2', label: 'Semester 2' }
    ]
  }
])

// Toolbar Action button config for DataTableCard
const toolbarActions = computed(() => [
  {
    label: 'Tambah ' + activeTabLabel.value,
    icon: Plus,
    variant: 'default',
    click: openAddSheet
  }
])

// Filter items reactively based on DataTableCard's filterValues
const filteredItems = computed(() => {
  return currentTabList.value.filter(item => {
    const matchItem = filterValues.value.title === 'all' || item.title === filterValues.value.title
    const matchKelas = filterValues.value.kelas === 'all' || item.kelas === filterValues.value.kelas
    const matchSemester = filterValues.value.semester === 'all' || item.semester === filterValues.value.semester
    return matchItem && matchKelas && matchSemester
  })
})

// Pagination states
const page = ref(1)
const perPage = ref(5)

const total = computed(() => filteredItems.value.length)
const from = computed(() => (page.value - 1) * perPage.value + 1)
const to = computed(() => Math.min(page.value * perPage.value, total.value))

// --- Data Sheet Form State ---
const showSheet = ref(false)
const sheetMode = ref('add') // 'add', 'edit', 'view'
const activeEditId = ref(null)

const form = ref({
  kelas: '',
  title: '',
  fileName: '',
  grades: {}
})

// --- CRUD Actions ---
const openAddSheet = () => {
  sheetMode.value = 'add'
  activeEditId.value = null
  form.value = {
    kelas: '',
    title: '',
    fileName: '',
    grades: {}
  }
  showSheet.value = true
}

const editItem = (idOrItem, maybeItem) => {
  const item = maybeItem || idOrItem
  sheetMode.value = 'edit'
  activeEditId.value = item.id
  form.value = {
    kelas: item.kelas,
    title: item.title,
    fileName: activeTab.value === 'materi' ? item.title : '',
    grades: { ...item.grades }
  }
  showSheet.value = true
}

const viewItem = (idOrItem, maybeItem) => {
  const item = maybeItem || idOrItem
  sheetMode.value = 'view'
  activeEditId.value = item.id
  form.value = {
    kelas: item.kelas,
    title: item.title,
    fileName: activeTab.value === 'materi' ? item.title : '',
    grades: { ...item.grades }
  }
  showSheet.value = true
}

const handleDelete = (id) => {
  if (activeTab.value === 'materi') {
    materiList.value = materiList.value.filter(x => x.id !== id)
  } else if (activeTab.value === 'tugas') {
    tugasList.value = tugasList.value.filter(x => x.id !== id)
  } else {
    ujianList.value = ujianList.value.filter(x => x.id !== id)
  }

  toast.success(`${activeTabLabel.value} berhasil dihapus!`)
  page.value = 1
}

const handleSave = (savedForm) => {
  if (sheetMode.value === 'add') {
    const newItem = {
      id: Math.random().toString(36).substr(2, 9),
      title: activeTab.value === 'materi' ? savedForm.fileName : savedForm.title,
      kelas: savedForm.kelas,
      semester: filterValues.value.semester !== 'all' ? filterValues.value.semester : 'Semester 1',
      status: 'Aktif',
      grades: savedForm.grades
    }

    if (activeTab.value === 'materi') {
      materiList.value.unshift(newItem)
    } else if (activeTab.value === 'tugas') {
      tugasList.value.unshift(newItem)
    } else {
      ujianList.value.unshift(newItem)
    }

    toast.success(`${activeTabLabel.value} baru berhasil ditambahkan!`)
  } else if (sheetMode.value === 'edit') {
    const listToUpdate = activeTab.value === 'materi'
      ? materiList
      : activeTab.value === 'tugas'
        ? tugasList
        : ujianList

    const idx = listToUpdate.value.findIndex(x => x.id === activeEditId.value)
    if (idx !== -1) {
      listToUpdate.value[idx].title = activeTab.value === 'materi' ? savedForm.fileName : savedForm.title
      listToUpdate.value[idx].kelas = savedForm.kelas
      listToUpdate.value[idx].grades = savedForm.grades
    }

    toast.success(`${activeTabLabel.value} berhasil diubah!`)
  }

  showSheet.value = false
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
        sub="Total Ukuran File Upload : 120MB"
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
            <SelectItem value="matematika">Matematika</SelectItem>
            <SelectItem value="bahasa-indonesia">Bahasa Indonesia</SelectItem>
            <SelectItem value="ipa">IPA</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <!-- Tab Buttons (Omitted Absensi) -->
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
        :actions="toolbarActions"
        :on-view="viewItem"
        :on-edit="editItem"
        :on-delete="handleDelete"
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
        <template #cell-status="{ value }">
          <Badge :variant="value === 'Aktif' ? 'green' : 'gray'" class="font-semibold">
            ✓ {{ value }}
          </Badge>
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
      :students-map="studentsMap"
      @save="handleSave"
    />
  </div>
</template>
