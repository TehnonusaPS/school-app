<script setup>
import { ref, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import DataSheet from '@/components/data-sheet/DataSheet.vue'
import FormSheet from '@/components/data-sheet/FormSheet.vue'
import { Download, Plus } from 'lucide-vue-next'
import { usePagination } from '@/composables/usePagination'
import { tableRawItems } from './data/dataTableDemos'
import { tableColumns, tableFilters } from './data/dataTableColumns'
import { studentSections } from './data/dataSheetSchema'

const itemsList = ref([...tableRawItems])

const filterValues = ref({
  search: '',
  kelas: 'all',
  status: 'all'
})

const perPage = ref(5)

const isDetailSheetOpen = ref(false)
const selectedItemForDetail = ref(null)

const isFormSheetOpen = ref(false)
const selectedItemForEdit = ref(null)
const isSaving = ref(false)

const handleViewDetail = id => {
  const item = itemsList.value.find(x => x.id === id)
  if (item) {
    selectedItemForDetail.value = item
    isDetailSheetOpen.value = true
  }
}

const handleEdit = item => {
  selectedItemForEdit.value = item
  isFormSheetOpen.value = true
}

const handleFormSubmit = formData => {
  isSaving.value = true
  setTimeout(() => {
    isSaving.value = false
    isFormSheetOpen.value = false

    if (formData.id) {
      // Edit mode
      const idx = itemsList.value.findIndex(x => x.id === formData.id)
      if (idx !== -1) {
        itemsList.value[idx] = {
          ...itemsList.value[idx],
          ...formData
        }
        toast.success('Siswa diperbarui', {
          description: `${formData.nama} berhasil diperbarui.`
        })
      }
    } else {
      // Create mode
      const newId = itemsList.value.length ? Math.max(...itemsList.value.map(x => x.id)) + 1 : 1
      const newItem = {
        id: newId,
        ...formData,
        foto: null,
        tanggal_masuk: new Date().toISOString().split('T')[0]
      }
      itemsList.value.unshift(newItem)
      toast.success('Siswa ditambahkan', {
        description: `${formData.nama} berhasil ditambahkan ke sistem.`
      })
    }
  }, 1000)
}







const filteredItems = computed(() => {
  return itemsList.value.filter(item => {
    const searchVal = filterValues.value.search?.trim().toLowerCase() || ''
    const searchMatch =
      !searchVal ||
      item.nama.toLowerCase().includes(searchVal) ||
      item.email.toLowerCase().includes(searchVal)

    const kelasVal = filterValues.value.kelas
    const classMatch = !kelasVal || kelasVal === 'all' || item.kelas.startsWith(kelasVal)

    const statusVal = filterValues.value.status
    const statusMatch = !statusVal || statusVal === 'all' || item.status === statusVal

    return searchMatch && classMatch && statusMatch
  })
})

const { currentPage, total, from, to, paginatedItems } = usePagination(filteredItems, perPage)

watch(filteredItems, () => {
  currentPage.value = 1
})

const isExporting = ref(false)
const customTableActions = computed(() => [
  {
    label: 'Tambah Siswa',
    icon: Plus,
    click: () => {
      selectedItemForEdit.value = null
      isFormSheetOpen.value = true
    }
  },
  {
    label: 'Export',
    icon: Download,
    variant: 'outline',
    loading: isExporting.value,
    click: () => {
      isExporting.value = true
      setTimeout(() => {
        isExporting.value = false
        toast.success('Export berhasil', {
          description: 'Data berhasil diexport ke format Excel/PDF.'
        })
      }, 1500)
    }
  }
])
</script>

<template>
  <DataTableCard
    :columns="tableColumns"
    :items="paginatedItems"
    :filters="tableFilters"
    :actions="customTableActions"
    v-model:filterValues="filterValues"
    v-model:perPage="perPage"
    :from="from"
    :to="to"
    :total="total"
    :page="currentPage"
    @update:page="currentPage = $event"
    @view="handleViewDetail"
    @edit="handleEdit"
    @delete="
      (id, item) => {
        itemsList.value = itemsList.value.filter(x => x.id !== id)
        toast.success('Berhasil dihapus', {
          description: `${item.nama} telah dihapus dari sistem.`
        })
      }
    "
  />

  <!-- Detail Sheet -->
  <DataSheet
    v-model:open="isDetailSheetOpen"
    :item="selectedItemForDetail"
    title-key="nama"
    description-key="nisn"
    description-prefix="NISN: "
    avatar-key="foto"
    badge-key="status"
    :sections="studentSections"
  />

  <!-- Form Sheet (Add / Edit) -->
  <FormSheet
    v-model:open="isFormSheetOpen"
    :item="selectedItemForEdit"
    :title="selectedItemForEdit ? 'Edit Siswa' : 'Tambah Siswa'"
    :description="selectedItemForEdit ? 'Ubah data rincian siswa' : 'Tambahkan siswa baru ke database'"
    :sections="studentSections"
    :loading="isSaving"
    @submit="handleFormSubmit"
  />
</template>
