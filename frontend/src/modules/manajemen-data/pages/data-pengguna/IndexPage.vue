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
  Users,
  Plus,
  Eye,
  Pencil,
  Trash2,
  ToggleLeft,
  ToggleRight,
  UserCheck,
  UserX
} from 'lucide-vue-next'
import {
  getPengguna,
  savePengguna,
  columns,
  filters,
  ROLE_LABELS,
  ROLE_OPTIONS
} from './data/mockPengguna'
import { glassSlide, glassFade } from '@/config/motion'

// Import data Yayasan & Sekolah
import { items as yayasanSource } from '@/modules/manajemen-data/pages/yayasan/data/yayasan.js'
import { allItems as sekolahSource } from '@/modules/manajemen-data/pages/sekolah/data/sekolah.js'

// --- State ---
const penggunaList = ref([])
const perPage = ref(5)
const filterValues = ref({
  search: '',
  role: 'all',
  status: 'all'
})

onMounted(() => {
  penggunaList.value = getPengguna()
})

// --- Computed Stats ---
const totalCount = computed(() => penggunaList.value.length)
const aktifCount = computed(() => penggunaList.value.filter(p => p.status === 'aktif').length)
const nonaktifCount = computed(() => penggunaList.value.filter(p => p.status === 'nonaktif').length)

// --- Helpers ---
function getRoleLabel(role) {
  return ROLE_LABELS[role] || role
}

function getStatusLabel(status) {
  return status === 'aktif' ? 'Aktif' : 'Nonaktif'
}

function getStatusBadgeVariant(status) {
  return status === 'aktif' ? 'green' : 'gray'
}

// --- Options for Yayasan & Sekolah ---
const yayasanOptions = computed(() => {
  const list = yayasanSource.value || []
  return [
    { label: '-', value: '-' },
    ...list.map(y => ({ label: y.nama, value: y.nama }))
  ]
})

const filteredSekolahOptions = computed(() => {
  const selectedYayasan = formItem.value.yayasan
  let list = sekolahSource.value || []
  if (selectedYayasan && selectedYayasan !== '-') {
    list = list.filter(s => s.namaYayasan === selectedYayasan)
  }
  return [
    { label: '-', value: '-' },
    ...list.map(s => ({ label: s.nama, value: s.nama }))
  ]
})

// --- Search & Filter ---
const filteredItems = computed(() => {
  return penggunaList.value.filter(item => {
    const searchVal = filterValues.value.search?.trim().toLowerCase() || ''
    const searchMatch =
      !searchVal ||
      item.nama.toLowerCase().includes(searchVal) ||
      item.email.toLowerCase().includes(searchVal)

    const roleVal = filterValues.value.role
    const roleMatch = !roleVal || roleVal === 'all' || item.role === roleVal

    const statusVal = filterValues.value.status
    const statusMatch = !statusVal || statusVal === 'all' || item.status === statusVal

    return searchMatch && roleMatch && statusMatch
  })
})

const { currentPage, total, from, to, paginatedItems } = usePagination(filteredItems, perPage)

watch(filteredItems, () => {
  currentPage.value = 1
})

// --- Header Actions ---
const headerActions = computed(() => [
  {
    label: 'Tambah Pengguna',
    icon: Plus,
    click: handleCreate
  }
])

// --- Form Sheet ---
const isFormSheetOpen = ref(false)
const isEditMode = ref(false)
const formErrors = ref({})
const formItem = ref({
  id: '',
  nama: '',
  email: '',
  role: '',
  yayasan: '-',
  sekolah: '-',
  status: 'aktif'
})

function handleCreate() {
  isEditMode.value = false
  formErrors.value = {}
  formItem.value = {
    id: '',
    nama: '',
    email: '',
    role: '',
    yayasan: '-',
    sekolah: '-',
    status: 'aktif'
  }
  isFormSheetOpen.value = true
}

function handleEdit(item) {
  isEditMode.value = true
  formErrors.value = {}
  formItem.value = {
    id: item.id,
    nama: item.nama,
    email: item.email,
    role: item.role,
    yayasan: item.yayasan || '-',
    sekolah: item.sekolah || '-',
    status: item.status
  }
  isFormSheetOpen.value = true
}

function validateForm() {
  const errors = {}
  if (!formItem.value.nama?.trim()) errors.nama = 'Nama pengguna wajib diisi.'
  if (!formItem.value.email?.trim()) {
    errors.email = 'Email wajib diisi.'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formItem.value.email.trim())) {
    errors.email = 'Format email tidak valid.'
  }
  if (!formItem.value.role) errors.role = 'Peran wajib dipilih.'
  if (!formItem.value.status) errors.status = 'Status wajib dipilih.'
  formErrors.value = errors
  return Object.keys(errors).length === 0
}

function handleSave() {
  if (!validateForm()) {
    toast.error('Gagal Menyimpan', { description: 'Harap lengkapi semua field yang wajib diisi.' })
    return
  }

  const email = formItem.value.email.trim().toLowerCase()

  // Cek duplikat email
  const isDuplicate = penggunaList.value.some(p => {
    const sameEmail = p.email.toLowerCase() === email
    return isEditMode.value ? sameEmail && p.id !== formItem.value.id : sameEmail
  })

  if (isDuplicate) {
    formErrors.value.email = `Email "${email}" sudah terdaftar.`
    toast.error('Email Sudah Digunakan', { description: `Pengguna dengan email "${email}" sudah ada.` })
    return
  }

  const payload = {
    nama: formItem.value.nama.trim(),
    email,
    role: formItem.value.role,
    yayasan: formItem.value.yayasan,
    sekolah: formItem.value.sekolah,
    status: formItem.value.status
  }

  if (isEditMode.value) {
    const updated = penggunaList.value.map(p =>
      p.id === formItem.value.id ? { ...p, ...payload } : p
    )
    penggunaList.value = updated
    savePengguna(updated)
    toast.success('Berhasil Diperbarui', { description: `Pengguna "${payload.nama}" telah diperbarui.` })
  } else {
    const newItem = { id: String(Date.now()), ...payload }
    const updated = [newItem, ...penggunaList.value]
    penggunaList.value = updated
    savePengguna(updated)
    toast.success('Berhasil Ditambahkan', { description: `Pengguna "${payload.nama}" telah ditambahkan.` })
  }

  isFormSheetOpen.value = false
}

// --- Detail Sheet ---
const isDetailSheetOpen = ref(false)
const selectedItemForDetail = ref(null)

function handleViewDetail(id) {
  const item = penggunaList.value.find(p => p.id === id)
  if (item) {
    selectedItemForDetail.value = item
    isDetailSheetOpen.value = true
  }
}

const rawDetailItem = computed(() => {
  if (!selectedItemForDetail.value) return {}
  return {
    nama: selectedItemForDetail.value.nama,
    status: getStatusLabel(selectedItemForDetail.value.status)
  }
})

const detailSections = computed(() => {
  if (!selectedItemForDetail.value) return []
  const p = selectedItemForDetail.value
  return [
    {
      id: 'info',
      title: 'Informasi Pengguna',
      fields: [
        { label: 'Nama Lengkap', value: p.nama },
        { label: 'Email', value: p.email },
        { label: 'Peran', value: getRoleLabel(p.role) },
        { label: 'Yayasan', value: p.yayasan || '-' },
        { label: 'Sekolah', value: p.sekolah || '-' },
        { label: 'Status Akun', value: getStatusLabel(p.status) }
      ]
    }
  ]
})

// --- Toggle Status ---
function handleToggleStatus(item) {
  const nextStatus = item.status === 'aktif' ? 'nonaktif' : 'aktif'
  const updated = penggunaList.value.map(p =>
    p.id === item.id ? { ...p, status: nextStatus } : p
  )
  penggunaList.value = updated
  savePengguna(updated)
  toast.success(
    nextStatus === 'aktif' ? 'Akun Diaktifkan' : 'Akun Dinonaktifkan',
    { description: `Pengguna "${item.nama}" kini berstatus ${nextStatus}.` }
  )
}

// --- Delete ---
const isDeleteConfirmOpen = ref(false)
const selectedItemToDelete = ref(null)

function openDeleteConfirm(item) {
  selectedItemToDelete.value = item
  isDeleteConfirmOpen.value = true
}

function confirmDelete() {
  if (!selectedItemToDelete.value) return
  const updated = penggunaList.value.filter(p => p.id !== selectedItemToDelete.value.id)
  penggunaList.value = updated
  savePengguna(updated)
  isDeleteConfirmOpen.value = false
  toast.success('Berhasil Dihapus', { description: `Pengguna "${selectedItemToDelete.value.nama}" telah dihapus.` })
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
      title="Pengguna"
      description="Kelola akun pengguna, peran, dan hak akses pada sistem"
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
        label="TOTAL PENGGUNA"
        :value="String(totalCount)"
        :icon="Users"
        variant="primary"
      />
      <StatCard
        label="AKUN AKTIF"
        :value="String(aktifCount)"
        :icon="UserCheck"
        variant="emerald"
      />
      <StatCard
        label="AKUN NONAKTIF"
        :value="String(nonaktifCount)"
        :icon="UserX"
        variant="violet"
      />
    </div>

    <!-- Data Table -->
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
        <!-- Custom Role Badge -->
        <template #cell-role="{ value }">
          <Badge variant="outline" class="text-[10px] font-semibold">
            {{ getRoleLabel(value) }}
          </Badge>
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

            <!-- Toggle Status -->
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

            <!-- Hapus (Tanpa warna mencolok, menggunakan class text-muted-foreground) -->
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
      title-key="nama"
      :badge="rawDetailItem.status"
      :badge-variant="selectedItemForDetail?.status === 'aktif' ? 'green' : 'gray'"
      :sections="detailSections"
    />

    <!-- Form Sheet (Create / Edit) -->
    <Sheet v-model:open="isFormSheetOpen">
      <SheetContent :show-close-button="false" class="sm:max-w-[500px] flex flex-col h-full gap-2">
        <SheetHeader class="border-b border-border pb-3 text-left">
          <SheetTitle class="text-base font-bold text-foreground">
            {{ isEditMode ? 'Edit Pengguna' : 'Tambah Pengguna' }}
          </SheetTitle>
          <SheetDescription class="text-xs text-muted-foreground">
            {{ isEditMode ? 'Perbarui informasi akun pengguna.' : 'Tambahkan akun pengguna baru ke dalam sistem.' }}
          </SheetDescription>
        </SheetHeader>

        <div class="flex-1 overflow-y-auto py-6 pr-1 space-y-5 no-scrollbar">
          <!-- Nama -->
          <div class="space-y-1.5">
            <label class="text-xs font-semibold text-muted-foreground">Nama Lengkap <span class="text-rose-500">*</span></label>
            <Input
              v-model="formItem.nama"
              placeholder="Masukkan nama lengkap..."
              class="h-10 rounded-xl"
              :class="formErrors.nama ? 'border-rose-500' : ''"
            />
            <p v-if="formErrors.nama" class="text-[10px] text-rose-500">{{ formErrors.nama }}</p>
          </div>

          <!-- Email -->
          <div class="space-y-1.5">
            <label class="text-xs font-semibold text-muted-foreground">Email <span class="text-rose-500">*</span></label>
            <Input
              v-model="formItem.email"
              type="email"
              placeholder="Masukkan alamat email..."
              class="h-10 rounded-xl"
              :class="formErrors.email ? 'border-rose-500' : ''"
            />
            <p v-if="formErrors.email" class="text-[10px] text-rose-500">{{ formErrors.email }}</p>
          </div>

          <!-- Peran & Status -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="text-xs font-semibold text-muted-foreground">Peran <span class="text-rose-500">*</span></label>
              <Select v-model="formItem.role">
                <SelectTrigger class="h-10 rounded-xl" :class="formErrors.role ? 'border-rose-500' : ''">
                  <SelectValue placeholder="Pilih Peran..." />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="opt in ROLE_OPTIONS"
                    :key="opt.value"
                    :value="opt.value"
                  >
                    {{ opt.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <p v-if="formErrors.role" class="text-[10px] text-rose-500">{{ formErrors.role }}</p>
            </div>

            <div class="space-y-1.5">
              <label class="text-xs font-semibold text-muted-foreground">Status <span class="text-rose-500">*</span></label>
              <Select v-model="formItem.status">
                <SelectTrigger class="h-10 rounded-xl" :class="formErrors.status ? 'border-rose-500' : ''">
                  <SelectValue placeholder="Pilih Status..." />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="aktif">Aktif</SelectItem>
                  <SelectItem value="nonaktif">Nonaktif</SelectItem>
                </SelectContent>
              </Select>
              <p v-if="formErrors.status" class="text-[10px] text-rose-500">{{ formErrors.status }}</p>
            </div>
          </div>

          <!-- Yayasan -->
          <div class="space-y-1.5">
            <label class="text-xs font-semibold text-muted-foreground">Yayasan</label>
            <Select v-model="formItem.yayasan" @update:modelValue="formItem.sekolah = '-'">
              <SelectTrigger class="h-10 rounded-xl">
                <SelectValue placeholder="Pilih Yayasan..." />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="opt in yayasanOptions"
                  :key="opt.value"
                  :value="opt.value"
                >
                  {{ opt.label }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Sekolah -->
          <div class="space-y-1.5">
            <label class="text-xs font-semibold text-muted-foreground">Sekolah</label>
            <Select v-model="formItem.sekolah">
              <SelectTrigger class="h-10 rounded-xl">
                <SelectValue placeholder="Pilih Sekolah..." />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="opt in filteredSekolahOptions"
                  :key="opt.value"
                  :value="opt.value"
                >
                  {{ opt.label }}
                </SelectItem>
              </SelectContent>
            </Select>
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
            Hapus Pengguna
          </DialogTitle>
          <DialogDescription class="text-[10px] text-muted-foreground leading-relaxed mt-2 text-left">
            Apakah Anda yakin ingin menghapus akun pengguna <strong class="text-foreground">"{{ selectedItemToDelete?.nama }}"</strong>? Tindakan ini tidak dapat dibatalkan.
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

