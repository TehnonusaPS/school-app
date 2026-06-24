<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
  Search,
  Download
} from 'lucide-vue-next'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { glassFade } from '@/config/motion'
import { getRecapData } from '@/services/api/absensi'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { usePagination } from '@/composables/usePagination'
import { Badge } from '@/components/ui/badge'
import { today, getLocalTimeZone, startOfMonth, endOfMonth } from '@internationalized/date'

const filterValues = ref({
  search: '',
  status: 'semua',
  startDate: '',
  endDate: ''
})

const filters = computed(() => [
  { type: 'search', key: 'search', placeholder: 'Cari siswa/kelas...' },
  { type: 'date', key: 'startDate', label: 'Dari:' },
  { type: 'date', key: 'endDate', label: 'Sampai:' },
  {
    type: 'select',
    key: 'status',
    label: 'Status:',
    placeholder: 'Semua Status',
    options: [
      { label: 'Hadir', value: 'hadir' },
      { label: 'Terlambat', value: 'terlambat' },
      { label: 'Izin', value: 'izin' },
      { label: 'Sakit', value: 'sakit' },
      { label: 'Tanpa Keterangan', value: 'alpa' }
    ]
  }
])

const columns = computed(() => [
  { key: 'tanggal', label: 'Tanggal' },
  { key: 'nama', label: 'Nama Siswa & Kelas' },
  { key: 'jamMasuk', label: 'Jam Masuk', type: 'code' },
  { key: 'jamKeluar', label: 'Jam Keluar', type: 'code' },
  { key: 'status', label: 'Status' }
])

const itemsPerPage = ref(10)
const recapData = ref([])
const isLoading = ref(true)

function formatDateStr(dateObj) {
  if (!dateObj) return ''
  if (dateObj.year) {
    return `${dateObj.year}-${String(dateObj.month).padStart(2, '0')}-${String(dateObj.day).padStart(2, '0')}`
  }
  if (dateObj instanceof Date) {
    return dateObj.toISOString().split('T')[0]
  }
  return String(dateObj)
}

async function loadData() {
  isLoading.value = true
  try {
    const startStr = filterValues.value.startDate
    const endStr = filterValues.value.endDate
    const data = await getRecapData(startStr, endStr)
    recapData.value = data
  } catch (err) {
    console.error(err)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  try {
    const tz = getLocalTimeZone()
    const now = today(tz)
    filterValues.value.startDate = formatDateStr(startOfMonth(now))
    filterValues.value.endDate = formatDateStr(endOfMonth(now))
  } catch (e) {
    filterValues.value.startDate = formatDateStr(new Date())
    filterValues.value.endDate = formatDateStr(new Date())
  }
  loadData()
})

const filteredData = computed(() => {
  return recapData.value.filter(item => {
    const matchStatus = filterValues.value.status === 'semua' || item.status === filterValues.value.status
    const matchSearch =
      !filterValues.value.search ||
      item.nama.toLowerCase().includes(filterValues.value.search.toLowerCase()) ||
      item.kelas.toLowerCase().includes(filterValues.value.search.toLowerCase())
    return matchStatus && matchSearch
  })
})

const { currentPage, total, from, to, paginatedItems: paginatedData } = usePagination(filteredData, itemsPerPage)

watch(filteredData, () => {
  currentPage.value = 1
})

function getStatusLabel(status) {
  if (status === 'hadir') return 'Hadir'
  if (status === 'terlambat') return 'Terlambat'
  if (status === 'izin') return 'Izin'
  if (status === 'sakit') return 'Sakit'
  if (status === 'alpa') return 'Tanpa Keterangan'
  return 'Belum Absen'
}

const tableActions = computed(() => [
  {
    label: 'Filter',
    icon: Search,
    variant: 'default',
    click: () => loadData()
  },
  {
    label: 'Download',
    icon: Download,
    variant: 'outline',
    click: () => {}
  }
])
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6"
  >
    <PageHeader
      title="Rekap Data Absensi"
      description="Pantau riwayat kehadiran siswa secara fleksibel berdasarkan rentang waktu."
    />

    <!-- Table using DataTableCard -->
    <div>
      <DataTableCard
        :columns="columns"
        :items="paginatedData"
        :filters="filters"
        :actions="tableActions"
        v-model:filterValues="filterValues"
        v-model:perPage="itemsPerPage"
        illustration="school_bell"
        :from="from"
        :to="to"
        :total="total"
        :page="currentPage"
        @update:page="currentPage = $event"
      >
        <template #cell-tanggal="{ value }">
          <div class="font-medium text-sm text-left">{{ value }}</div>
        </template>

        <template #cell-nama="{ item }">
          <div class="font-semibold text-sm text-left">
            {{ item.nama }} 
            <span class="text-muted-foreground font-normal">({{ item.kelas }})</span>
          </div>
        </template>

        <template #cell-status="{ value }">
          <Badge
            :variant="value === 'hadir' ? 'default' : value === 'terlambat' ? 'destructive' : 'secondary'"
            :class="{
              'bg-green-100 text-green-700 hover:bg-green-100 dark:bg-green-900/40 dark:text-green-400': value === 'hadir',
              'bg-yellow-100 text-yellow-700 hover:bg-yellow-100 dark:bg-yellow-900/40 dark:text-yellow-400': value === 'terlambat',
              'bg-blue-100 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-400': value === 'sakit',
              'bg-indigo-100 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-400': value === 'izin',
              'bg-red-100 text-red-700 hover:bg-red-100 dark:bg-red-900/40 dark:text-red-400': value === 'alpa',
            }"
            class="mx-auto"
          >
            {{ getStatusLabel(value) }}
          </Badge>
        </template>
      </DataTableCard>
    </div>
  </div>
</template>
