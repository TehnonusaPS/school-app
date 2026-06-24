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
  getTahunAjaranList,
  saveTahunAjaranList
} from './data/mockTahunAjaran'
import { glassSlide, glassFade } from '@/config/motion'

// --- State ---
const dbItems = ref([])
const perPage = ref(5)
const filterValues = ref({
  search: '',
  status: 'all'
})

onMounted(() => {
  dbItems.value = getTahunAjaranList()
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
const headerActions = computed(() => [
  {
    label: 'Tambah Tahun Ajaran',
    icon: Plus,
    click: handleCreate
  }
])

// --- Data Table Configurations ---
const columns = [
  { key: 'tahun', label: 'Tahun Ajaran' },
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
const formErrors = ref({})
const formItem = ref({
  id: '',
  tahun: '',
  tanggalMulai: '',
  tanggalSelesai: '',
  status: 'nonaktif'
})

function handleCreate() {
  isEditMode.value = false
  formErrors.value = {}
  formItem.value = {
    id: '',
    tahun: '',
    tanggalMulai: '',
    tanggalSelesai: '',
    status: 'nonaktif'
  }
  isFormSheetOpen.value = true
}

function handleEdit(item) {
  isEditMode.value = true
  formErrors.value = {}
  formItem.value = {
    id: item.id,
    tahun: item.tahun,
    tanggalMulai: item.tanggalMulai,
    tanggalSelesai: item.tanggalSelesai,
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
  if (!formItem.value.tanggalMulai) errors.tanggalMulai = 'Tanggal mulai wajib dipilih.'
  if (!formItem.value.tanggalSelesai) errors.tanggalSelesai = 'Tanggal selesai wajib dipilih.'
  if (formItem.value.tanggalMulai && formItem.value.tanggalSelesai) {
    if (new Date(formItem.value.tanggalMulai) >= new Date(formItem.value.tanggalSelesai)) {
      errors.tanggalSelesai = 'Tanggal selesai harus setelah tanggal mulai.'
    }
  }
  formErrors.value = errors
  return Object.keys(errors).length === 0
}

function handleSave() {
  if (!validateForm()) {
    toast.error('Gagal Menyimpan', { description: 'Harap periksa kembali isian formulir Anda.' })
    return
  }

  const payload = {
    tahun: formItem.value.tahun.trim(),
    tanggalMulai: formItem.value.tanggalMulai,
    tanggalSelesai: formItem.value.tanggalSelesai,
    status: formItem.value.status
  }

  let updatedList = []
  let activeIdToSet = null
  if (isEditMode.value) {
    updatedList = dbItems.value.map(item =>
      String(item.id) === String(formItem.value.id) ? { ...item, ...payload } : item
    )
    if (payload.status === 'aktif') activeIdToSet = formItem.value.id
    toast.success('Berhasil Diperbarui', { description: `Tahun ajaran "${payload.tahun}" telah diperbarui.` })
  } else {
    const newItem = { id: String(Date.now()), ...payload }
    updatedList = [newItem, ...dbItems.value]
    if (payload.status === 'aktif') activeIdToSet = newItem.id
    toast.success('Berhasil Ditambahkan', { description: `Tahun ajaran "${payload.tahun}" telah ditambahkan.` })
  }

  // Jika item yang disimpan berstatus aktif, handler saveTahunAjaranList otomatis menonaktifkan yang lain
  dbItems.value = saveTahunAjaranList(updatedList, activeIdToSet)
  isFormSheetOpen.value = false
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
        { label: 'Tanggal Mulai', value: formatDate(t.tanggalMulai) },
        { label: 'Tanggal Selesai', value: formatDate(t.tanggalSelesai) },
        { label: 'Status', value: getStatusLabel(t.status) }
      ]
    }
  ]
})

// --- Aktivasi Status (Toggle) ---
function handleToggleStatus(item) {
  // Hanya bisa mengaktifkan. Jika dinonaktifkan langsung, sistem harus tetap memiliki minimal 1 yang aktif.
  // Oleh karena itu, jika klik nonaktifkan, tidak terjadi apa-apa / peringatkan. Jika klik aktifkan, maka yang lain dinonaktifkan.
  if (item.status === 'aktif') {
    toast.warning('Aksi Dibatalkan', { description: 'Harus ada minimal satu tahun ajaran yang aktif pada sistem.' })
    return
  }
  
  dbItems.value = saveTahunAjaranList(dbItems.value, item.id)
  toast.success('Tahun Ajaran Diaktifkan', {
    description: `Tahun ajaran "${item.tahun}" kini aktif. Tahun ajaran lainnya telah dinonaktifkan.`
  })
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

function confirmDelete() {
  if (!selectedItemToDelete.value) return
  const updated = dbItems.value.filter(t => t.id !== selectedItemToDelete.value.id)
  dbItems.value = saveTahunAjaranList(updated)
  isDeleteConfirmOpen.value = false
  toast.success('Berhasil Dihapus', { description: `Tahun ajaran "${selectedItemToDelete.value.tahun}" telah dihapus.` })
  selectedItemToDelete.value = null
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
      title="Manajemen Tahun Ajaran"
      description="Kelola daftar periode tahun akademik sekolah, durasi kalender, dan aktivasi semester aktif"
      :actions="headerActions"
    />

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

            <!-- Edit -->
            <button
              class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
              title="Edit"
              @click="handleEdit(item)"
            >
              <Pencil class="size-4 transition-transform group-hover/btn:scale-110" />
              <span class="text-[9px] font-semibold leading-none">Edit</span>
            </button>

            <!-- Toggle Status (Aktivasi) -->
            <button
              class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none transition-colors"
              :class="item.status === 'aktif' ? 'text-emerald-500 hover:text-emerald-600' : 'text-muted-foreground hover:text-foreground'"
              :title="item.status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'"
              @click="handleToggleStatus(item)"
            >
              <component
                :is="item.status === 'aktif' ? ToggleRight : ToggleLeft"
                class="size-4 transition-transform group-hover/btn:scale-110"
              />
              <span class="text-[9px] font-semibold leading-none">
                {{ item.status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
              </span>
            </button>

            <!-- Hapus (Netral / Tanpa Warna) -->
            <button
              class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
              title="Hapus"
              @click="openDeleteConfirm(item)"
            >
              <Trash2 class="size-4 transition-transform group-hover/btn:scale-110" />
              <span class="text-[9px] font-semibold leading-none">Hapus</span>
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

        <div class="flex-1 overflow-y-auto py-6 pr-1 space-y-5 no-scrollbar">
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
