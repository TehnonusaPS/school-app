<script setup>
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { usePagination } from '@/composables/usePagination'
import { stats, columns, filters, actions, allItems } from './data/sekolah.js'
import StatCard from '@/components/stat-card/StatCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { computed } from 'vue'
import { ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import DataSheet from '@/components/data-sheet/DataSheet.vue'
import { rawSekolahItem, sekolahSheetSections } from './data/dataSheetDetail.js'

const auth = useAuthStore()
const isSuperAdmin = computed(() => auth.user?.role === 'superadmin')
const perPage = ref(5)
const tableItems = ref([...allItems.value])

const filterValues = ref({
  search: '',
  status: 'all'
})

const items = computed(() => {
  if (isSuperAdmin.value) {
    return tableItems.value
  }

  return tableItems.value.filter(
    item => item.yayasanId === auth.user?.yayasanId
  )
})

const deleteItem = (id, item) => {
  tableItems.value = tableItems.value.filter(
    item => item.id !== id
  )

  toast.success('Berhasil dihapus', {
    description: `${item.nama} telah dihapus dari sistem.`
  })
}

const filteredItems = computed(() => {
  return items.value.filter(item => {
    const searchVal = filterValues.value.search?.trim().toLowerCase() || ''
    const searchMatch =
      !searchVal ||
      item.nama.toLowerCase().includes(searchVal) ||
      item.namaYayasan.toLowerCase().includes(searchVal)


    const statusVal = filterValues.value.status
    const statusMatch = !statusVal || statusVal === 'all' || item.status === statusVal

    return searchMatch && statusMatch
  })
})

const { currentPage, total, from, to, paginatedItems } = usePagination(filteredItems, perPage)

watch(filteredItems, () => {
  currentPage.value = 1
})

const isDetailSheetOpen = ref(false)
const selectedItemForDetail = ref(null)

const handleViewDetail = id => {
  const item = items.value.find(x => x.id === id)
  if (item) {
    selectedItemForDetail.value = item
    isDetailSheetOpen.value = true
  }
}

</script>

<template>
  <div class="space-y-6 w-full mx-auto px-0">
    <PageHeader
      title="Data Sekolah"
      description="Kelola informasi dan profil sekolah secara lengkap dan terstruktur di sini"
    />
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <StatCard
      v-for="(stat, index) in stats"
        :key="index"
        :label="stat.label"
        :value="stat.value"
        :trend="stat.trend"
        :trendDirection="stat.trendDirection"
        :icon="stat.icon"
        :variant="stat.variant"
      />
    </div>

    <DataTableCard
    :columns="columns"
    :items="paginatedItems"
    :filters="filters"
    :actions="actions"
    v-model:filterValues="filterValues"
    v-model:perPage="perPage"
    :from="from"
    :to="to"
    :total="total"
    :page="currentPage"
    @update:page="currentPage = $event"
    @view="handleViewDetail"
    @edit="$router.push('/manajemen-data/sekolah/edit')"
    @delete="deleteItem"
  />

  </div>

  <!-- Detail Sheet -->
  <DataSheet
    v-model:open="isDetailSheetOpen"
    :item="rawSekolahItem"
    title-key="nama"
    description-key="npsn"
    description-prefix="NPSN: "
    avatar-key="logo"
    badge-key="status"
    :sections="sekolahSheetSections"
  />
</template>
