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
  columns,
  filters,
  ROLE_LABELS
} from './data/mockPengguna'
import { glassSlide, glassFade } from '@/config/motion'
import { getUsers, createUser, updateUser, deleteUser, getRoles, getFoundations, getSchools } from '@/services/managementService'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()

// --- State ---
const penggunaList = ref([])
const roleOptions = ref([])
const yayasanList = ref([])
const sekolahList = ref([])
const isLoading = ref(false)

const perPage = ref(5)
const currentPage = ref(1)
const total = ref(0)
const from = ref(1)
const to = ref(1)

const filterValues = ref({
  search: '',
  role: 'all',
  status: 'all'
})

// --- Computed Stats ---
const totalCount = ref(0)
const aktifCount = ref(0)
const nonaktifCount = ref(0)

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
  return [
    { label: '-', value: '-' },
    ...yayasanList.value.map(y => ({ label: y.name, value: String(y.id) }))
  ]
})

const filteredSekolahOptions = computed(() => {
  const selectedYayasan = formItem.value.yayasan
  let list = sekolahList.value
  if (selectedYayasan && selectedYayasan !== '-') {
    list = list.filter(s => String(s.foundation_id) === String(selectedYayasan))
  }
  return [
    { label: '-', value: '-' },
    ...list.map(s => ({ label: s.name, value: String(s.id) }))
  ]
})

// --- Fetch Data ---
const fetchUsers = async () => {
  isLoading.value = true
  try {
    const params = {
      page: currentPage.value,
      per_page: perPage.value,
      search: filterValues.value.search,
    }
    if (filterValues.value.role !== 'all') {
      const selectedRole = roleOptions.value.find(r => r.name === filterValues.value.role)
      if (selectedRole) params.role_id = selectedRole.id
    }
    if (filterValues.value.status !== 'all') {
      params.is_active = filterValues.value.status === 'aktif' ? 1 : 0
    }
    const res = await getUsers(params)
    penggunaList.value = res.data.data.map(user => ({
      id: user.id,
      nama: user.name,
      email: user.email,
      role: user.role ? user.role.name : '',
      yayasan: user.foundation ? user.foundation.name : '-',
      sekolah: user.school ? user.school.name : '-',
      status: user.is_active ? 'aktif' : 'nonaktif',
      foundation_id: user.foundation_id,
      school_id: user.school_id,
      role_id: user.role_id,
      phone: user.phone,
      photo: user.photo,
      ...user
    }))
    total.value = res.data.total
    from.value = res.data.from || 1
    to.value = res.data.to || 1

    // Quick stats count
    totalCount.value = res.data.total
    aktifCount.value = penggunaList.value.filter(p => p.status === 'aktif').length
    nonaktifCount.value = penggunaList.value.filter(p => p.status === 'nonaktif').length
  } catch (err) {
    toast.error('Gagal mengambil data pengguna')
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  try {
    const resRoles = await getRoles()
    roleOptions.value = resRoles.data

    const resFoundations = await getFoundations()
    yayasanList.value = resFoundations.data.data

    const resSchools = await getSchools()
    sekolahList.value = resSchools.data.data

    // Dynamic filters
    const roleFilter = filters.find(f => f.key === 'role')
    if (roleFilter) {
      roleFilter.options = [
        { label: 'Semua Peran', value: 'all' },
        ...roleOptions.value.map(r => ({ label: r.label, value: r.name }))
      ]
    }
  } catch (err) {
    console.error('Error loading dropdown lists', err)
  }

  fetchUsers()
})

watch([currentPage, perPage, filterValues], () => {
  fetchUsers()
}, { deep: true })

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
  status: 'aktif',
  password: ''
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
    status: 'aktif',
    password: ''
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
    yayasan: item.foundation_id ? String(item.foundation_id) : '-',
    sekolah: item.school_id ? String(item.school_id) : '-',
    status: item.status,
    password: ''
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
  if (!isEditMode.value && !formItem.value.password) {
    errors.password = 'Password wajib diisi.'
  }
  if (!formItem.value.role) errors.role = 'Peran wajib dipilih.'
  if (!formItem.value.status) errors.status = 'Status wajib dipilih.'
  formErrors.value = errors
  return Object.keys(errors).length === 0
}

async function handleSave() {
  formErrors.value = {}
  if (!validateForm()) {
    toast.error('Gagal Menyimpan', { description: 'Harap lengkapi semua field yang wajib diisi.' })
    return
  }

  const selectedRole = roleOptions.value.find(r => r.name === formItem.value.role)
  if (!selectedRole) {
    toast.error('Gagal', { description: 'Peran tidak valid.' })
    return
  }

  const payload = {
    name: formItem.value.nama.trim(),
    email: formItem.value.email.trim().toLowerCase(),
    role_id: selectedRole.id,
    foundation_id: formItem.value.yayasan !== '-' ? Number(formItem.value.yayasan) : null,
    school_id: formItem.value.sekolah !== '-' ? Number(formItem.value.sekolah) : null,
    is_active: formItem.value.status === 'aktif',
  }

  if (formItem.value.password) {
    payload.password = formItem.value.password
  }

  try {
    if (isEditMode.value) {
      await updateUser(formItem.value.id, payload)
      toast.success('Berhasil Diperbarui', { description: `Pengguna "${payload.name}" telah diperbarui.` })
    } else {
      await createUser(payload)
      toast.success('Berhasil Ditambahkan', { description: `Pengguna "${payload.name}" telah ditambahkan.` })
    }
    isFormSheetOpen.value = false
    fetchUsers()
  } catch (err) {
    if (err.response?.status === 422 && err.response?.data?.errors) {
      const serverErrors = err.response.data.errors
      const localErrors = {}
      if (serverErrors.name) localErrors.nama = serverErrors.name[0]
      if (serverErrors.email) localErrors.email = serverErrors.email[0]
      if (serverErrors.password) localErrors.password = serverErrors.password[0]
      if (serverErrors.role_id) localErrors.role = serverErrors.role_id[0]
      formErrors.value = localErrors
      toast.error('Gagal Menyimpan', { description: 'Terdapat kesalahan validasi.' })
    } else {
      const errorMsg = err.response?.data?.message || 'Gagal menyimpan pengguna.'
      toast.error('Gagal', { description: errorMsg })
    }
  }
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
async function handleToggleStatus(item) {
  const nextActive = item.status !== 'aktif'
  const selectedRole = roleOptions.value.find(r => r.name === item.role)
  const payload = {
    name: item.nama,
    email: item.email,
    role_id: selectedRole ? selectedRole.id : item.role_id,
    is_active: nextActive
  }

  try {
    await updateUser(item.id, payload)
    toast.success(
      nextActive ? 'Akun Diaktifkan' : 'Akun Dinonaktifkan',
      { description: `Pengguna "${item.nama}" kini berstatus ${nextActive ? 'Aktif' : 'Nonaktif'}.` }
    )
    fetchUsers()
  } catch (err) {
    toast.error('Gagal memperbarui status akun')
  }
}

// --- Delete ---
const isDeleteConfirmOpen = ref(false)
const selectedItemToDelete = ref(null)

function openDeleteConfirm(item) {
  selectedItemToDelete.value = item
  isDeleteConfirmOpen.value = true
}

async function confirmDelete() {
  if (!selectedItemToDelete.value) return
  try {
    await deleteUser(selectedItemToDelete.value.id)
    isDeleteConfirmOpen.value = false
    toast.success('Berhasil Dihapus', { description: `Pengguna "${selectedItemToDelete.value.nama}" telah dihapus.` })
    selectedItemToDelete.value = null
    fetchUsers()
  } catch (err) {
    toast.error('Gagal menghapus pengguna')
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
        :items="penggunaList"
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
              class="flex flex-col items-center justify-center gap-0.5 group/btn focus:outline-none text-muted-foreground hover:text-foreground transition-colors"
              :title="item.status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'"
              @click="handleToggleStatus(item)"
            >
              <component
                :is="item.status === 'aktif' ? ToggleRight : ToggleLeft"
                class="size-4 transition-transform group-hover/btn:scale-110"
                :class="item.status === 'aktif' ? 'text-primary' : 'text-muted-foreground'"
              />
              <span class="text-[9px] font-semibold leading-none">
                {{ item.status === 'aktif' ? 'Mati' : 'Hidup' }}
              </span>
            </button>

            <!-- Delete -->
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

          <!-- Password (Only on Create Mode) -->
          <div v-if="!isEditMode" class="space-y-1.5">
            <label class="text-xs font-semibold text-muted-foreground">Password <span class="text-rose-500">*</span></label>
            <Input
              v-model="formItem.password"
              type="password"
              placeholder="Masukkan password..."
              class="h-10 rounded-xl"
              :class="formErrors.password ? 'border-rose-500' : ''"
            />
            <p v-if="formErrors.password" class="text-[10px] text-rose-500">{{ formErrors.password }}</p>
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
                    v-for="opt in roleOptions"
                    :key="opt.name"
                    :value="opt.name"
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
