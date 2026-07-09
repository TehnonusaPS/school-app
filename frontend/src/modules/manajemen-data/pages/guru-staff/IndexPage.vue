<script setup>
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { columns, filters, actions } from './data/guruStaff.js'
import StatCard from '@/components/stat-card/StatCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { usePagination } from '@/composables/usePagination'
import { computed, ref, watch, onMounted } from 'vue'
import { toast } from 'vue-sonner'
import DataSheet from '@/components/data-sheet/DataSheet.vue'
import { guruStaffSheetSections } from './data/dataSheetDetail.js'
import { getTeachers, deleteTeacher } from '@/services/managementService'
import { School, BookCheck, GraduationCap, BookX } from 'lucide-vue-next'

const auth = useAuthStore()
const isAdminYayasan = computed(() => auth.user?.role === 'admin_yayasan')
const perPage = ref(5)
const tableItems = ref([])
const isLoading = ref(false)

const stats = computed(() => {
  const list = items.value || []
  const totalVal = list.length
  const aktifVal = list.filter(item => item.status_aktif === 'Aktif').length
  const guruVal = list.filter(item => ['guru', 'wali_kelas', 'kepala_sekolah'].includes(item.role)).length
  const staffVal = list.filter(item => ['tata_usaha', 'admin_sekolah', 'admin_yayasan'].includes(item.role)).length

  return [
    {
      label: 'TOTAL PEGAWAI',
      value: String(totalVal),
      trend: '+8.4% bln ini',
      trendDirection: 'up',
      icon: School,
      illustration: 'globe',
      variant: 'primary'
    },
    {
      label: 'PEGAWAI AKTIF',
      value: String(aktifVal),
      trend: '+12 Baru',
      trendDirection: 'up',
      icon: BookCheck,
      illustration: 'school_bell',
      variant: 'emerald'
    },
    {
      label: 'TOTAL GURU',
      value: String(guruVal),
      sub: 'Kehadiran',
      progress: 98,
      icon: GraduationCap,
      illustration: 'open_book',
      variant: 'amber'
    },
    {
      label: 'TOTAL STAFF',
      value: String(staffVal),
      sub: 'Kehadiran',
      progress: 90,
      icon: BookX,
      illustration: 'abc_board',
      variant: 'violet'
    }
  ]
})

const filterValues = ref({
  search: '',
  status: 'all'
})

const items = computed(() => {
  if (isAdminYayasan.value) {
    return tableItems.value
  }

  return tableItems.value.filter(
    item => item.unitId === auth.user?.unitId || item.unit_id === auth.user?.unitId
  )
})

const fetchTeachers = async () => {
  isLoading.value = true
  try {
    const res = await getTeachers()
    tableItems.value = res.data.map(item => ({
      ...item,
      nip: item.nip_nuptk || '-',
      unitKerja: item.unit_kerja || '-',
      unitId: item.unit_id,
      status: item.status_kepegawaian || '-'
    }))
  } catch (error) {
    toast.error('Gagal mengambil data guru dan staff')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchTeachers()
})

const deleteItem = async (id, item) => {
  try {
    await deleteTeacher(id)
    toast.success('Berhasil dihapus', {
      description: `${item.nama} telah dihapus dari sistem.`
    })
    fetchTeachers()
  } catch (err) {
    toast.error('Gagal menghapus guru/staff')
  }
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
        :illustration="stat.illustration"
      />
    </div>

    <DataTableCard
      :columns="columns"
      :items="paginatedItems"
      :filters="filters"
      :actions="actions"
      v-model:filterValues="filterValues"
      v-model:perPage="perPage"
      illustration="abc_board"
      :from="from"
      :to="to"
      :total="total"
      :page="currentPage"
      @update:page="currentPage = $event"
      @view="handleViewDetail"
      @edit="$router.push(`/manajemen-data/guru-staff/edit?id=${$event}`)"
      @delete="deleteItem"
    />

    <DataSheet
      v-model:open="isDetailSheetOpen"
      :item="selectedItemForDetail"
      title-key="nama"
      description-key="nip_nuptk"
      description-prefix="NIP/NUPTK: "
      avatar-key="foto"
      badge-key="status"
      :sections="guruStaffSheetSections"
    />
  </div>
</template>
