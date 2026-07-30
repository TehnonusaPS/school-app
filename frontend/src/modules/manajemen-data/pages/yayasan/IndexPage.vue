<script setup>
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { columns, filters, actions } from './data/yayasan.js'
import StatCard from '@/components/stat-card/StatCard.vue'
import { ref, computed, watch, onMounted } from 'vue'
import { toast } from 'vue-sonner'
import DataSheet from '@/components/data-sheet/DataSheet.vue'
import { yayasanSheetSections } from './data/dataSheetDetail.js'
import { getFoundations, deleteFoundation } from '@/services/managementService'
import { Building2, ShieldCheck, ShieldAlert, ShieldX } from 'lucide-vue-next'

const stats = ref([
  {
    label: 'TOTAL YAYASAN',
    value: '0',
    trend: 'Terdaftar',
    trendDirection: 'up',
    icon: Building2,
    illustration: 'globe',
    variant: 'primary'
  },
  {
    label: 'YAYASAN AKTIF',
    value: '0',
    trend: 'Status aktif',
    trendDirection: 'up',
    icon: ShieldCheck,
    illustration: 'school_bell',
    variant: 'emerald'
  },
  {
    label: 'YAYASAN SEDANG TRIAL',
    value: '0',
    trend: 'Status trial',
    trendDirection: 'down',
    icon: ShieldAlert,
    illustration: 'pencil',
    variant: 'amber'
  },
  {
    label: 'YAYASAN TIDAK AKTIF',
    value: '0',
    trend: 'Status nonaktif',
    trendDirection: 'up',
    icon: ShieldX,
    illustration: 'ruler',
    variant: 'violet'
  }
])

const perPage = ref(5)
const currentPage = ref(1)
const total = ref(0)
const from = ref(1)
const to = ref(1)
const tableItems = ref([])
const isLoading = ref(false)

const filterValues = ref({
  search: '',
  status: 'all'
})

// Debounced search query
const searchQuery = ref('')
let debounceTimeout = null
watch(() => filterValues.value.search, (newVal) => {
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    searchQuery.value = newVal
  }, 300)
})

const fetchFoundations = async () => {
  isLoading.value = true
  try {
    const params = {
      page: currentPage.value,
      per_page: perPage.value,
      search: searchQuery.value,
    }
    if (filterValues.value.status !== 'all') {
      params.status = filterValues.value.status.toLowerCase()
    }
    const res = await getFoundations(params)
    const baseUrl = (import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api').replace(/\/api$/, '')
    const getLogoUrl = (path) => {
      if (!path) return null
      if (path.startsWith('http')) return path
      return `${baseUrl}/storage/${path}`
    }

    tableItems.value = res.data.data.map(item => ({
      id: item.id,
      nama: item.name,
      kode: item.code,
      tanggal_berdiri: item.established_date ? item.established_date.split('T')[0] : '-',
      alamat: item.address,
      email: item.email,
      no_hp: item.phone,
      website: item.website,
      no_akta: item.deed_number,
      tanggal_akta: item.deed_date ? item.deed_date.split('T')[0] : '-',
      no_sk: item.decree_number,
      tanggal_sk: item.decree_date ? item.decree_date.split('T')[0] : '-',
      logo: getLogoUrl(item.logo),
      foto: getLogoUrl(item.logo),
      status: item.status.charAt(0).toUpperCase() + item.status.slice(1),
      emailLogin: item.users && item.users[0] ? item.users[0].email : '-',
      noHpLogin: item.users && item.users[0] ? item.users[0].phone : '-',
      // Mapped fields
      jmlSekolah: item.schools_count || 0,
      jmlPengguna: item.users_count || 0,
      ...item
    }))
    total.value = res.data.total
    from.value = res.data.from || 1
    to.value = res.data.to || 1

    if (res.stats) {
      stats.value[0].value = String(res.stats.total)
      stats.value[1].value = String(res.stats.active)
      stats.value[2].value = String(res.stats.trial)
      stats.value[3].value = String(res.stats.inactive)
    }
  } catch (error) {
    toast.error('Gagal mengambil data yayasan')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchFoundations()
})

// Watch search and status filter to reset page to 1
watch([searchQuery, () => filterValues.value.status], () => {
  if (currentPage.value !== 1) {
    currentPage.value = 1
  } else {
    fetchFoundations()
  }
})

// Watch pagination values to fetch data
watch([currentPage, perPage], () => {
  fetchFoundations()
})

const deleteItem = async (id, item) => {
  try {
    await deleteFoundation(id)
    toast.success('Berhasil dihapus', {
      description: `${item.nama} telah dihapus dari sistem.`
    })
    fetchFoundations()
  } catch (err) {
    toast.error('Gagal menghapus yayasan')
  }
}

const isDetailSheetOpen = ref(false)
const selectedItemForDetail = ref(null)

const handleViewDetail = id => {
  const item = tableItems.value.find(x => x.id === id)
  if (item) {
    selectedItemForDetail.value = item
    isDetailSheetOpen.value = true
  }
}

</script>

<template>
  <div class="space-y-6 w-full mx-auto px-0">
    <PageHeader
      title="Data Yayasan"
      description="Kelola informasi dan profil yayasan secara lengkap dan terstruktur di sini"
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
      :illustration="stat.illustration"
      />
     </div>

    <DataTableCard
      :columns="columns"
      :items="tableItems"
      :filters="filters"
      :actions="actions"
      v-model:filterValues="filterValues"
      v-model:perPage="perPage"
      illustration="globe"
      :from="from"
      :to="to"
      :total="total"
      :page="currentPage"
      @update:page="currentPage = $event"
      @view="handleViewDetail"
      @edit="$router.push(`/manajemen-data/yayasan/edit?id=${$event}`)"
      @delete="deleteItem"
    />
  </div>

  <!-- Detail Sheet -->
  <DataSheet
    v-model:open="isDetailSheetOpen"
    :item="selectedItemForDetail"
    title-key="nama"
    description-key="no_akta"
    description-prefix="No. Akta: "
    avatar-key="foto"
    badge-key="status"
    :sections="yayasanSheetSections"
  />
</template>
