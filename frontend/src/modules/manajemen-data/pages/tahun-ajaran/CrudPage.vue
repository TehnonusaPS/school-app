<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import DataSheet from '@/components/data-sheet/DataSheet.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { usePagination } from '@/composables/usePagination'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription } from '@/components/ui/sheet'
import {
  Calendar,
  Plus,
  Eye,
  Pencil,
  Trash2,
  ToggleLeft,
  ToggleRight,
  CheckCircle,
  XCircle
} from 'lucide-vue-next'
import {
  fetchAllAcademicYears,
  createAcademicYear,
  updateAcademicYear,
  deleteAcademicYear
} from '@/services/academicYearService'
import { glassSlide, glassFade } from '@/config/motion'

// --- State ---
const dbItems = ref([])
const perPage = ref(5)
const filterValues = ref({
  search: '',
  status: 'all'
})
const isLoading = ref(false)

async function fetchData() {
  isLoading.value = true
  try {
    const res = await fetchAllAcademicYears()
    dbItems.value = res.data.map(item => ({
      id: item.id,
      tahun: item.name,
      semester: item.semester,
      tanggalMulai: item.start_date ? item.start_date.substring(0, 10) : '',
      tanggalSelesai: item.end_date ? item.end_date.substring(0, 10) : '',
      status: item.is_active ? 'aktif' : 'nonaktif'
    }))
  } catch (err) {
    toast.error('Gagal mengambil data tahun ajaran')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchData()
})

// --- Computed Stats ---
const totalCount = computed(() => dbItems.value.length)
const aktifCount = computed(() => dbItems.value.filter(t => t.status === 'aktif').length)
const nonaktifCount = computed(() => dbItems.value.filter(t => t.status === 'nonaktif').length)

// --- Helpers ---
function getStatusLabel(status) {
  return status === 'aktif' ? 'Aktif' : 'Nonaktif'
}

function getStatusBadgeVariant(status) {
  return status === 'aktif' ? 'green' : 'gray'
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

// --- Search & Filter ---
const filteredItems = computed(() => {
  return dbItems.value.filter(item => {
    const searchVal = filterValues.value.search?.trim().toLowerCase() || ''
    const searchMatch =
      !searchVal ||
      item.tahun.toLowerCase().includes(searchVal)

    const statusVal = filterValues.value.status
    const statusMatch = !statusVal || statusVal === 'all' || item.status === statusVal

    return searchMatch && statusMatch
  })
})

const { currentPage, total, from, to, paginatedItems } = usePagination(filteredItems, perPage)

watch(filteredItems, () => {
  currentPage.value = 1
})

// --- Header Actions ---
const headerActions = computed(() => [])

// --- Data Table Configurations ---
const columns = [
  { key: 'tahun', label: 'Tahun Ajaran' },
  { key: 'semester', label: 'Semester' },
  { key: 'tanggalMulai', label: 'Tanggal Mulai' },
  { key: 'tanggalSelesai', label: 'Tanggal Selesai' },
  {
    key: 'status',
    label: 'Status',
    badge: true,
    badgeVariant: {
      aktif: 'green',
      nonaktif: 'gray'
    }
  },
  { key: 'actions', label: 'Aksi' }
]

const filters = [
  {
    type: 'search',
    key: 'search',
    placeholder: 'Cari tahun ajaran...'
  },
  {
    type: 'select',
    key: 'status',
    label: 'Status:',
    placeholder: 'Semua Status',
    options: [
      { label: 'Aktif', value: 'aktif' },
      { label: 'Nonaktif', value: 'nonaktif' }
    ]
  }
]

// --- Form Sheet ---
const isFormSheetOpen = ref(false)
const isEditMode = ref(false)
const formMode = ref('full') // 'full' (Ganjil & Genap) or 'single'
const formErrors = ref({})
const formItem = ref({
  id: '',
  tahun: '',
  semester: 'odd',
  tanggalMulai: '',
  tanggalSelesai: '',
  // Dual semester date ranges
  oddTanggalMulai: '',
  oddTanggalSelesai: '',
  evenTanggalMulai: '',
  evenTanggalSelesai: '',
  activeSemester: 'odd',
  status: 'nonaktif'
})

function handleCreate() {
  isEditMode.value = false
  formMode.value = 'full'
  formErrors.value = {}
  formItem.value = {
    id: '',
    tahun: '',
    semester: 'odd',
    tanggalMulai: '',
    tanggalSelesai: '',
    oddTanggalMulai: '',
    oddTanggalSelesai: '',
    evenTanggalMulai: '',
    evenTanggalSelesai: '',
    activeSemester: 'odd',
    status: 'nonaktif'
  }
  isFormSheetOpen.value = true
}

function handleEdit(item) {
  isEditMode.value = true
  formMode.value = 'single'
  formErrors.value = {}
  formItem.value = {
    id: item.id,
    tahun: item.tahun,
    semester: item.semester,
    tanggalMulai: item.tanggalMulai,
    tanggalSelesai: item.tanggalSelesai,
    oddTanggalMulai: '',
    oddTanggalSelesai: '',
    evenTanggalMulai: '',
    evenTanggalSelesai: '',
    activeSemester: 'odd',
    status: item.status
  }
  isFormSheetOpen.value = true
}

function validateForm() {
  const errors = {}
  if (!formItem.value.tahun?.trim()) {
    errors.tahun = 'Tahun ajaran wajib diisi.'
  } else if (!/^\d{4}\/\d{4}$/.test(formItem.value.tahun.trim())) {
    errors.tahun = 'Format harus YYYY/YYYY (contoh: 2025/2026).'
  }

  if (!isEditMode.value && formMode.value === 'full') {
    if (!formItem.value.oddTanggalMulai) errors.oddTanggalMulai = 'Tanggal mulai ganjil wajib diisi.'
    if (!formItem.value.oddTanggalSelesai) errors.oddTanggalSelesai = 'Tanggal selesai ganjil wajib diisi.'
    if (formItem.value.oddTanggalMulai && formItem.value.oddTanggalSelesai) {
      if (new Date(formItem.value.oddTanggalMulai) >= new Date(formItem.value.oddTanggalSelesai)) {
        errors.oddTanggalSelesai = 'Tanggal selesai ganjil harus setelah tanggal mulai ganjil.'
      }
    }

    if (!formItem.value.evenTanggalMulai) errors.evenTanggalMulai = 'Tanggal mulai genap wajib diisi.'
    if (!formItem.value.evenTanggalSelesai) errors.evenTanggalSelesai = 'Tanggal selesai genap wajib diisi.'
    if (formItem.value.evenTanggalMulai && formItem.value.evenTanggalSelesai) {
      if (new Date(formItem.value.evenTanggalMulai) >= new Date(formItem.value.evenTanggalSelesai)) {
        errors.evenTanggalSelesai = 'Tanggal selesai genap harus setelah tanggal mulai genap.'
      }
    }
  } else {
    if (!formItem.value.semester) errors.semester = 'Semester wajib dipilih.'
    if (!formItem.value.tanggalMulai) errors.tanggalMulai = 'Tanggal mulai wajib dipilih.'
    if (!formItem.value.tanggalSelesai) errors.tanggalSelesai = 'Tanggal selesai wajib dipilih.'
    if (formItem.value.tanggalMulai && formItem.value.tanggalSelesai) {
      if (new Date(formItem.value.tanggalMulai) >= new Date(formItem.value.tanggalSelesai)) {
        errors.tanggalSelesai = 'Tanggal selesai harus setelah tanggal mulai.'
      }
    }
  }

  formErrors.value = errors
  return Object.keys(errors).length === 0
}

async function handleSave() {
  if (!validateForm()) {
    toast.error('Gagal Menyimpan', { description: 'Harap periksa kembali isian formulir Anda.' })
    return
  }

  try {
    if (isEditMode.value) {
      const payload = {
        name: formItem.value.tahun.trim(),
        semester: formItem.value.semester,
        start_date: formItem.value.tanggalMulai,
        end_date: formItem.value.tanggalSelesai,
        is_active: formItem.value.status === 'aktif'
      }
      await updateAcademicYear(formItem.value.id, payload)
      toast.success('Berhasil Diperbarui', { description: `Tahun ajaran "${formItem.value.tahun}" telah diperbarui.` })
    } else {
      if (formMode.value === 'full') {
        const payload = {
          name: formItem.value.tahun.trim(),
          odd_start_date: formItem.value.oddTanggalMulai,
          odd_end_date: formItem.value.oddTanggalSelesai,
          even_start_date: formItem.value.evenTanggalMulai,
          even_end_date: formItem.value.evenTanggalSelesai,
          active_semester: formItem.value.activeSemester
        }
        await createAcademicYear(payload)
        toast.success('Berhasil Ditambahkan', { description: `Tahun ajaran "${formItem.value.tahun}" (Ganjil & Genap) telah ditambahkan.` })
      } else {
        const payload = {
          name: formItem.value.tahun.trim(),
          semester: formItem.value.semester,
          start_date: formItem.value.tanggalMulai,
          end_date: formItem.value.tanggalSelesai,
          is_active: formItem.value.status === 'aktif'
        }
        await createAcademicYear(payload)
        toast.success('Berhasil Ditambahkan', { description: `Tahun ajaran "${formItem.value.tahun}" telah ditambahkan.` })
      }
    }
    fetchData()
    isFormSheetOpen.value = false
  } catch (err) {
    toast.error('Gagal menyimpan tahun ajaran')
  }
}

// --- Detail Sheet ---
const isDetailSheetOpen = ref(false)
const selectedItemForDetail = ref(null)

function handleViewDetail(id) {
  const item = dbItems.value.find(t => t.id === id)
  if (item) {
    selectedItemForDetail.value = item
    isDetailSheetOpen.value = true
  }
}

const rawDetailItem = computed(() => {
  if (!selectedItemForDetail.value) return {}
  return {
    tahun: 'Tahun Ajaran ' + selectedItemForDetail.value.tahun,
    status: getStatusLabel(selectedItemForDetail.value.status)
  }
})

const detailSections = computed(() => {
  if (!selectedItemForDetail.value) return []
  const t = selectedItemForDetail.value
  return [
    {
      id: 'info',
      title: 'Detail Informasi',
      fields: [
        { label: 'Tahun Ajaran', value: t.tahun },
        { label: 'Semester', value: t.semester === 'odd' ? 'Ganjil' : 'Genap' },
        { label: 'Tanggal Mulai', value: formatDate(t.tanggalMulai) },
        { label: 'Tanggal Selesai', value: formatDate(t.tanggalSelesai) },
        { label: 'Status', value: getStatusLabel(t.status) }
      ]
    }
  ]
})

// --- Aktivasi Status (Toggle) ---
async function handleToggleStatus(item) {
  if (item.status === 'aktif') {
    toast.warning('Aksi Dibatalkan', { description: 'Harus ada minimal satu tahun ajaran yang aktif pada sistem.' })
    return
  }
  
  try {
    await updateAcademicYear(item.id, {
      is_active: true
    })
    toast.success('Tahun Ajaran Diaktifkan', {
      description: `Tahun ajaran "${item.tahun}" kini aktif. Tahun ajaran lainnya telah dinonaktifkan.`
    })
    fetchData()
  } catch (err) {
    toast.error('Gagal mengaktifkan tahun ajaran')
  }
}

// --- Delete ---
const isDeleteConfirmOpen = ref(false)
const selectedItemToDelete = ref(null)

function openDeleteConfirm(item) {
  if (item.status === 'aktif') {
    toast.error('Tidak Dapat Dihapus', { description: 'Tahun ajaran yang berstatus aktif tidak dapat dihapus.' })
    return
  }
  selectedItemToDelete.value = item
  isDeleteConfirmOpen.value = true
}

async function confirmDelete() {
  if (!selectedItemToDelete.value) return
  try {
    await deleteAcademicYear(selectedItemToDelete.value.id)
    isDeleteConfirmOpen.value = false
    toast.success('Berhasil Dihapus', { description: `Tahun ajaran "${selectedItemToDelete.value.tahun}" telah dihapus.` })
    selectedItemToDelete.value = null
    fetchData()
  } catch (err) {
    toast.error('Gagal menghapus tahun ajaran')
  }
}
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 w-full mx-auto px-0 text-left"
  >
    <!-- Header -->
    <PageHeader
      title="Daftar Tahun Ajaran"
      description="Lihat daftar periode tahun akademik dan pilih tahun ajaran mana yang aktif."
      :actions="headerActions"
    />

    <!-- Informative Banner -->
    <div class="p-4 rounded-xl bg-primary/5 dark:bg-primary/10 border border-primary/20 flex items-start gap-3 text-sm">
      <Calendar class="h-5 w-5 text-primary shrink-0 mt-0.5" />
      <div>
        <h4 class="font-semibold text-foreground dark:text-zinc-100">Penyusunan & Pembuatan Tahun Ajaran Baru</h4>
        <p class="text-xs text-muted-foreground dark:text-zinc-400 mt-0.5">
          Tahun Ajaran baru secara otomatis dibuat saat menyusun <strong>Kalender Akademik Baru</strong>. Pada halaman ini, Anda hanya perlu menentukan Tahun Ajaran yang berstatus <strong>Aktif</strong> atau <strong>Nonaktif</strong>.
        </p>
      </div>
    </div>

    <!-- Stat Cards -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
      class="grid gap-4 grid-cols-1 sm:grid-cols-3"
    >
      <StatCard
        label="TOTAL TAHUN AJARAN"
        :value="String(totalCount)"
        :icon="Calendar"
        illustration="globe"
        variant="primary"
      />
      <StatCard
        label="TAHUN AJARAN AKTIF"
        :value="String(aktifCount)"
        :icon="CheckCircle"
        illustration="school_bell"
        variant="emerald"
      />
      <StatCard
        label="TAHUN AJARAN NONAKTIF"
        :value="String(nonaktifCount)"
        :icon="XCircle"
        illustration="abc_board"
        variant="violet"
      />
    </div>

    <!-- Data Table Card -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }"
    >
      <DataTableCard
        :columns="columns"
        :items="paginatedItems"
        :filters="filters"
        v-model:filterValues="filterValues"
        v-model:perPage="perPage"
        :from="from"
        :to="to"
        :total="total"
        :page="currentPage"
        @update:page="currentPage = $event"
      >
        <!-- Format Date Cells -->
        <template #cell-semester="{ value }">
          <span class="text-sm font-medium text-foreground">
            {{ value === 'odd' ? 'Ganjil' : 'Genap' }}
          </span>
        </template>

        <template #cell-tanggalMulai="{ value }">
          <span class="text-sm font-medium text-foreground">
            {{ formatDate(value) }}
          </span>
        </template>
        
        <template #cell-tanggalSelesai="{ value }">
          <span class="text-sm font-medium text-foreground">
            {{ formatDate(value) }}
          </span>
        </template>

        <!-- Custom Status Badge -->
        <template #cell-status="{ value }">
          <Badge :variant="getStatusBadgeVariant(value)">
            {{ getStatusLabel(value) }}
          </Badge>
        </template>

        <!-- Custom Actions -->
        <template #cell-actions="{ item }">
          <div class="flex items-center justify-center gap-3">
            <!-- Detail -->
            <button
              class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
              title="Lihat Detail"
              @click="handleViewDetail(item.id)"
            >
              <Eye class="size-4 transition-transform group-hover/btn:scale-110" />
              <span class="text-[9px] font-semibold leading-none">Detail</span>
            </button>

            <!-- Toggle Status (Aktivasi) -->
            <button
              class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none transition-colors"
              :class="item.status === 'aktif' ? 'text-emerald-500 hover:text-emerald-600' : 'text-muted-foreground hover:text-foreground'"
              :title="item.status === 'aktif' ? 'Status Aktif' : 'Klik untuk mengaktifkan'"
              @click="handleToggleStatus(item)"
            >
              <component
                :is="item.status === 'aktif' ? ToggleRight : ToggleLeft"
                class="size-4 transition-transform group-hover/btn:scale-110"
              />
              <span class="text-[9px] font-semibold leading-none">
                {{ item.status === 'aktif' ? 'Aktif' : 'Aktifkan' }}
              </span>
            </button>
          </div>
        </template>
      </DataTableCard>
    </div>

    <!-- Detail Sheet -->
    <DataSheet
      v-model:open="isDetailSheetOpen"
      :item="rawDetailItem"
      title-key="tahun"
      :badge="rawDetailItem.status"
      :badge-variant="selectedItemForDetail?.status === 'aktif' ? 'green' : 'gray'"
      :sections="detailSections"
    />

    <!-- Form Sheet (Create / Edit) -->
    <Sheet v-model:open="isFormSheetOpen">
      <SheetContent :show-close-button="false" class="sm:max-w-[500px] flex flex-col h-full gap-2">
        <SheetHeader class="border-b border-border pb-3 text-left">
          <SheetTitle class="text-base font-bold text-foreground">
            {{ isEditMode ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}
          </SheetTitle>
          <SheetDescription class="text-xs text-muted-foreground">
            {{ isEditMode ? 'Perbarui detail data tahun ajaran sekolah.' : 'Tambahkan periode tahun ajaran baru ke dalam sistem.' }}
          </SheetDescription>
        </SheetHeader>

        <div class="flex-1 overflow-y-auto py-6 pr-1 space-y-5 no-scrollbar text-left">
          <!-- Tahun Ajaran -->
          <div class="space-y-1.5">
            <label class="text-xs font-semibold text-muted-foreground">Tahun Ajaran <span class="text-rose-500">*</span></label>
            <Input
              v-model="formItem.tahun"
              placeholder="Contoh: 2025/2026"
              class="h-10 rounded-xl"
              :class="formErrors.tahun ? 'border-rose-500' : ''"
            />
            <p v-if="formErrors.tahun" class="text-[10px] text-rose-500">{{ formErrors.tahun }}</p>
          </div>

          <!-- Dual Semester Form (Create Mode) -->
          <template v-if="!isEditMode && formMode === 'full'">
            <!-- Semester Ganjil Section -->
            <div class="p-3.5 rounded-2xl border border-border bg-muted/20 space-y-3">
              <span class="text-xs font-bold text-foreground flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-primary inline-block"></span>
                Semester Ganjil
              </span>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="text-[11px] font-semibold text-muted-foreground">Tanggal Mulai <span class="text-rose-500">*</span></label>
                  <Input
                    type="date"
                    v-model="formItem.oddTanggalMulai"
                    class="h-9 text-xs rounded-xl"
                    :class="formErrors.oddTanggalMulai ? 'border-rose-500' : ''"
                  />
                  <p v-if="formErrors.oddTanggalMulai" class="text-[9px] text-rose-500">{{ formErrors.oddTanggalMulai }}</p>
                </div>
                <div class="space-y-1">
                  <label class="text-[11px] font-semibold text-muted-foreground">Tanggal Selesai <span class="text-rose-500">*</span></label>
                  <Input
                    type="date"
                    v-model="formItem.oddTanggalSelesai"
                    class="h-9 text-xs rounded-xl"
                    :class="formErrors.oddTanggalSelesai ? 'border-rose-500' : ''"
                  />
                  <p v-if="formErrors.oddTanggalSelesai" class="text-[9px] text-rose-500">{{ formErrors.oddTanggalSelesai }}</p>
                </div>
              </div>
            </div>

            <!-- Semester Genap Section -->
            <div class="p-3.5 rounded-2xl border border-border bg-muted/20 space-y-3">
              <span class="text-xs font-bold text-foreground flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-emerald-500 inline-block"></span>
                Semester Genap
              </span>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="text-[11px] font-semibold text-muted-foreground">Tanggal Mulai <span class="text-rose-500">*</span></label>
                  <Input
                    type="date"
                    v-model="formItem.evenTanggalMulai"
                    class="h-9 text-xs rounded-xl"
                    :class="formErrors.evenTanggalMulai ? 'border-rose-500' : ''"
                  />
                  <p v-if="formErrors.evenTanggalMulai" class="text-[9px] text-rose-500">{{ formErrors.evenTanggalMulai }}</p>
                </div>
                <div class="space-y-1">
                  <label class="text-[11px] font-semibold text-muted-foreground">Tanggal Selesai <span class="text-rose-500">*</span></label>
                  <Input
                    type="date"
                    v-model="formItem.evenTanggalSelesai"
                    class="h-9 text-xs rounded-xl"
                    :class="formErrors.evenTanggalSelesai ? 'border-rose-500' : ''"
                  />
                  <p v-if="formErrors.evenTanggalSelesai" class="text-[9px] text-rose-500">{{ formErrors.evenTanggalSelesai }}</p>
                </div>
              </div>
            </div>

            <!-- Active Semester Selector -->
            <div class="space-y-1.5">
              <label class="text-xs font-semibold text-muted-foreground">Semester Aktif Pertama <span class="text-rose-500">*</span></label>
              <Select v-model="formItem.activeSemester">
                <SelectTrigger class="h-10 rounded-xl">
                  <SelectValue placeholder="Pilih Semester Aktif..." />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="odd">Semester Ganjil (Aktif)</SelectItem>
                  <SelectItem value="even">Semester Genap (Aktif)</SelectItem>
                  <SelectItem value="none">Nonaktifkan Keduanya</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </template>

          <!-- Single Semester Form (Edit Mode) -->
          <template v-else>
            <!-- Semester -->
            <div class="space-y-1.5">
              <label class="text-xs font-semibold text-muted-foreground">Semester <span class="text-rose-500">*</span></label>
              <Select v-model="formItem.semester">
                <SelectTrigger class="h-10 rounded-xl">
                  <SelectValue placeholder="Pilih Semester..." />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="odd">Ganjil (Odd)</SelectItem>
                  <SelectItem value="even">Genap (Even)</SelectItem>
                </SelectContent>
              </Select>
              <p v-if="formErrors.semester" class="text-[10px] text-rose-500">{{ formErrors.semester }}</p>
            </div>

            <!-- Durasi Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="text-xs font-semibold text-muted-foreground">Tanggal Mulai <span class="text-rose-500">*</span></label>
                <Input
                  type="date"
                  v-model="formItem.tanggalMulai"
                  class="h-10 rounded-xl"
                  :class="formErrors.tanggalMulai ? 'border-rose-500' : ''"
                />
                <p v-if="formErrors.tanggalMulai" class="text-[10px] text-rose-500">{{ formErrors.tanggalMulai }}</p>
              </div>

              <div class="space-y-1.5">
                <label class="text-xs font-semibold text-muted-foreground">Tanggal Selesai <span class="text-rose-500">*</span></label>
                <Input
                  type="date"
                  v-model="formItem.tanggalSelesai"
                  class="h-10 rounded-xl"
                  :class="formErrors.tanggalSelesai ? 'border-rose-500' : ''"
                />
                <p v-if="formErrors.tanggalSelesai" class="text-[10px] text-rose-500">{{ formErrors.tanggalSelesai }}</p>
              </div>
            </div>

            <!-- Status -->
            <div class="space-y-1.5">
              <label class="text-xs font-semibold text-muted-foreground">Status <span class="text-rose-500">*</span></label>
              <Select v-model="formItem.status" :disabled="isEditMode && formItem.status === 'aktif'">
                <SelectTrigger class="h-10 rounded-xl">
                  <SelectValue placeholder="Pilih Status..." />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="aktif">Aktif</SelectItem>
                  <SelectItem value="nonaktif">Nonaktif</SelectItem>
                </SelectContent>
              </Select>
              <p v-if="isEditMode && formItem.status === 'aktif'" class="text-[10px] text-muted-foreground italic">
                Status aktif hanya dapat dialihkan dari luar melalui tombol aktivasi di baris tabel.
              </p>
            </div>
          </template>
        </div>

        <div class="border-t border-border pt-4 flex items-center justify-end gap-2 shrink-0">
          <Button
            type="button"
            variant="ghost"
            class="text-xs font-bold rounded-xl cursor-pointer"
            @click="isFormSheetOpen = false"
          >
            Batal
          </Button>
          <Button
            type="button"
            class="text-xs font-bold rounded-xl cursor-pointer bg-primary text-primary-foreground hover:bg-primary/90 shadow-none border-none"
            @click="handleSave"
          >
            {{ isEditMode ? 'Simpan Perubahan' : 'Simpan' }}
          </Button>
        </div>
      </SheetContent>
    </Sheet>

    <!-- Delete Confirm Dialog -->
    <Dialog v-model:open="isDeleteConfirmOpen">
      <DialogContent class="sm:max-w-[400px] rounded-2xl p-6">
        <DialogHeader>
          <DialogTitle class="text-sm font-bold text-foreground flex items-center gap-1.5">
            <Trash2 class="h-5 w-5 text-rose-500 animate-bounce" />
            Hapus Tahun Ajaran
          </DialogTitle>
          <DialogDescription class="text-[10px] text-muted-foreground leading-relaxed mt-2 text-left">
            Apakah Anda yakin ingin menghapus tahun ajaran <strong class="text-foreground">"{{ selectedItemToDelete?.tahun }}"</strong>? Tindakan ini tidak dapat dibatalkan.
          </DialogDescription>
        </DialogHeader>

        <DialogFooter class="flex flex-row items-center justify-end gap-2 pt-4 border-t mt-4">
          <Button
            type="button"
            variant="ghost"
            class="text-xs font-bold rounded-xl cursor-pointer"
            @click="isDeleteConfirmOpen = false"
          >
            Batal
          </Button>
          <Button
            type="button"
            class="text-xs font-bold rounded-xl cursor-pointer bg-rose-500 text-white hover:bg-rose-600 border-none shadow-none"
            @click="confirmDelete"
          >
            Ya, Hapus
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
