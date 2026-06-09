<script setup>
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { usePagination } from '@/composables/usePagination'
import { columns, filters, actions, stats, allItems } from './data/guruStaff.js'
import StatCard from '@/components/stat-card/StatCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { computed, ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import DataSheet from '@/components/data-sheet/DataSheet.vue'
import { guruStaffSheetSections, rawGuruStaffItem } from './data/dataSheetDetail.js'

const auth = useAuthStore()
const isAdminYayasan = computed(() => auth.user?.role === 'admin_yayasan')
const perPage = ref(5)
const tableItems = ref([...allItems.value])

const filterValues = ref({
  search: '',
  status: 'all'
})

const items = computed(() => {
  if (isAdminYayasan.value) {
    return tableItems.value
  }

  return tableItems.value.filter(
    item => item.unitId === auth.user?.unitId
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
      item.nama.toLowerCase().includes(searchVal)


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
      title="Data Guru dan Staff"
      description="Kelola informasi data tenaga pendidik dan kependidikan secara lengkap dan terstruktur di sini"
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
        :sub="stat.sub"
        :progress="stat.progress"
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
      @edit="$router.push('/manajemen-data/guru-staff/edit')"
      @delete="deleteItem"
    />

    <DataSheet
      v-model:open="isDetailSheetOpen"
      :item="rawGuruStaffItem"
      title-key="nama"
      description-key="nip_nuptk"
      description-prefix="NIP/NUPTK: "
      avatar-key="foto"
      badge-key="status_kepegawaian"
      :sections="guruStaffSheetSections"
    />
  </div>
</template>
