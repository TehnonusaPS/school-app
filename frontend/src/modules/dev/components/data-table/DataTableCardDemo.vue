<script setup>
import { ref, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { Download, Plus } from 'lucide-vue-next'
import { usePagination } from '@/composables/usePagination'
import { tableRawItems } from './data/dataTableDemos'
import { tableColumns, tableFilters } from './data/dataTableColumns'

const filterValues = ref({
  search: '',
  kelas: 'all',
  status: 'all'
})

const perPage = ref(5)

const filteredItems = computed(() => {
  return tableRawItems.filter(item => {
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
      toast.info('Tambah Siswa', { description: 'Membuka form tambah siswa...' })
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
    @view="
      item =>
        toast.info(`Detail: ${item.nama}`, {
          description: `Kelas ${item.kelas} · NISN ${item.nisn}`
        })
    "
    @edit="item => toast(`Edit: ${item.nama}`, { description: 'Membuka form edit...' })"
    @delete="
      (id, item) =>
        toast.success('Berhasil dihapus', {
          description: `${item.nama} telah dihapus dari sistem.`
        })
    "
  />
</template>
