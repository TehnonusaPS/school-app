<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
  FileText,
  Users,
  UserCheck,
  UserX,
  Clock,
  TrendingUp,
  Printer,
  RefreshCw,
  Download,
} from 'lucide-vue-next'
import { getRecapData } from '@/services/api/absensi'
import { Badge } from '@/components/ui/badge'
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs'
import { toast } from 'vue-sonner'

// Common Components
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { glassSlide, glassFade } from '@/config/motion'

// Helper to get local date strings for start and end of current month
const getStartAndEndOfMonth = () => {
  const d = new Date()
  const start = new Date(d.getFullYear(), d.getMonth(), 1)
  const end = new Date(d.getFullYear(), d.getMonth() + 1, 0)
  
  const formatDate = (date) => {
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    return `${year}-${month}-${day}`
  }
  
  return {
    start: formatDate(start),
    end: formatDate(end)
  }
}

const initialDates = getStartAndEndOfMonth()

const isLoading = ref(true)
const isRefreshing = ref(false)
const recapData = ref([])

// Shared Filter State
const filterValues = ref({
  search: '',
  startDate: initialDates.start,
  endDate: initialDates.end,
  kelas: 'all',
  status: 'all'
})

// Tab 1 isolated page states
const pageDetail = ref(1)
const perPageDetail = ref(10)

// Tab 2 isolated page states
const pageSummary = ref(1)
const perPageSummary = ref(10)

async function loadData() {
  isLoading.value = true
  try {
    const data = await getRecapData(filterValues.value.startDate, filterValues.value.endDate)
    recapData.value = data
  } catch (err) {
    console.error(err)
    toast.error('Gagal mengambil data absensi')
  } finally {
    isLoading.value = false
  }
}

async function handleRefresh() {
  isRefreshing.value = true
  await loadData()
  isRefreshing.value = false
  toast.success('Data absensi diperbarui')
}

onMounted(() => {
  loadData()
})

// Reload data from API when date range changes
watch(
  () => [filterValues.value.startDate, filterValues.value.endDate],
  () => {
    pageDetail.value = 1
    pageSummary.value = 1
    loadData()
  }
)

// Reset pages on search/kelas/status change
watch(
  () => [filterValues.value.search, filterValues.value.kelas, filterValues.value.status],
  () => {
    pageDetail.value = 1
    pageSummary.value = 1
  }
)

const kelasList = computed(() => {
  const set = new Set(recapData.value.map(d => d.kelas))
  return Array.from(set).sort()
})

const filteredDetailData = computed(() => {
  return recapData.value.filter(item => {
    const matchStatus = filterValues.value.status === 'all' || item.status === filterValues.value.status
    const matchKelas = filterValues.value.kelas === 'all' || item.kelas === filterValues.value.kelas
    const matchSearch =
      !filterValues.value.search ||
      item.nama.toLowerCase().includes(filterValues.value.search.toLowerCase()) ||
      item.kelas.toLowerCase().includes(filterValues.value.search.toLowerCase())
    return matchStatus && matchKelas && matchSearch
  })
})

const paginatedDetailData = computed(() => {
  const start = (pageDetail.value - 1) * perPageDetail.value
  return filteredDetailData.value.slice(start, start + perPageDetail.value)
})

const totalDetail = computed(() => filteredDetailData.value.length)
const fromDetail = computed(() => totalDetail.value === 0 ? 0 : (pageDetail.value - 1) * perPageDetail.value + 1)
const toDetail = computed(() => Math.min(pageDetail.value * perPageDetail.value, totalDetail.value))

// Ringkasan per siswa
const ringkasanSiswa = computed(() => {
  const map = {}
  recapData.value.forEach(item => {
    if (!map[item.nama]) {
      map[item.nama] = {
        nama: item.nama,
        kelas: item.kelas,
        hadir: 0,
        terlambat: 0,
        izin: 0,
        sakit: 0,
        alpa: 0,
        total: 0
      }
    }
    map[item.nama].total++
    if (item.status === 'hadir') map[item.nama].hadir++
    else if (item.status === 'terlambat') map[item.nama].terlambat++
    else if (item.status === 'izin') map[item.nama].izin++
    else if (item.status === 'sakit') map[item.nama].sakit++
    else if (item.status === 'alpa') map[item.nama].alpa++
  })

  // Filter client-side
  return Object.values(map).filter(siswa => {
    const matchKelas = filterValues.value.kelas === 'all' || siswa.kelas === filterValues.value.kelas
    const matchSearch =
      !filterValues.value.search ||
      siswa.nama.toLowerCase().includes(filterValues.value.search.toLowerCase())
    return matchKelas && matchSearch
  })
})

const paginatedSummaryData = computed(() => {
  const start = (pageSummary.value - 1) * perPageSummary.value
  return ringkasanSiswa.value.slice(start, start + perPageSummary.value)
})

const totalSummary = computed(() => ringkasanSiswa.value.length)
const fromSummary = computed(() => totalSummary.value === 0 ? 0 : (pageSummary.value - 1) * perPageSummary.value + 1)
const toSummary = computed(() => Math.min(pageSummary.value * perPageSummary.value, totalSummary.value))

// Stats
const totalHadir = computed(() => recapData.value.filter(d => d.status === 'hadir').length)
const totalTerlambat = computed(() => recapData.value.filter(d => d.status === 'terlambat').length)
const totalIzinSakit = computed(() => recapData.value.filter(d => ['izin', 'sakit'].includes(d.status)).length)
const totalAlpa = computed(() => recapData.value.filter(d => d.status === 'alpa').length)
const persentaseKehadiran = computed(() => {
  const total = recapData.value.length
  if (total === 0) return 0
  return Math.round(((totalHadir.value + totalTerlambat.value) / total) * 100)
})

function getStatusLabel(status) {
  const labels = {
    hadir: 'Hadir',
    terlambat: 'Terlambat',
    izin: 'Izin',
    sakit: 'Sakit',
    alpa: 'Alpa',
  }
  return labels[status] || 'Belum Absen'
}

function getStatusClass(status) {
  return {
    hadir: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 border-none font-medium',
    terlambat: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400 border-none font-medium',
    sakit: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 border-none font-medium',
    izin: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400 border-none font-medium',
    alpa: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 border-none font-medium',
  }[status] || 'bg-muted text-muted-foreground border-none'
}

function handlePrint() {
  window.print()
}

// Configs for Header and DataTableCard
const pageHeaderActions = computed(() => [
  {
    label: 'Perbarui',
    icon: RefreshCw,
    variant: 'outline',
    click: handleRefresh,
    disabled: isLoading.value || isRefreshing.value
  },
  {
    label: 'Cetak',
    icon: Printer,
    variant: 'outline',
    click: handlePrint
  },
  {
    label: 'Ekspor Excel',
    icon: Download,
    variant: 'default',
    click: () => {
      toast.success('Laporan berhasil diekspor ke Excel!')
    }
  }
])

const detailColumns = [
  { key: 'tanggal', label: 'Tanggal' },
  { key: 'nama', label: 'Nama Siswa' },
  { key: 'kelas', label: 'Kelas' },
  { key: 'jamMasuk', label: 'Jam Masuk' },
  { key: 'jamKeluar', label: 'Jam Keluar' },
  { key: 'status', label: 'Status' }
]

const summaryColumns = [
  { key: 'nama', label: 'Nama Siswa' },
  { key: 'kelas', label: 'Kelas' },
  { key: 'hadir', label: 'Hadir', type: 'number' },
  { key: 'terlambat', label: 'Terlambat', type: 'number' },
  { key: 'izin', label: 'Izin', type: 'number' },
  { key: 'sakit', label: 'Sakit', type: 'number' },
  { key: 'alpa', label: 'Alpa', type: 'number' },
  { key: 'persen', label: '% Kehadiran' }
]

const detailFiltersConfig = computed(() => [
  {
    key: 'search',
    type: 'search',
    placeholder: 'Cari nama siswa...'
  },
  {
    key: 'startDate',
    label: 'Dari',
    type: 'date'
  },
  {
    key: 'endDate',
    label: 'Sampai',
    type: 'date'
  },
  {
    key: 'kelas',
    label: 'Kelas',
    type: 'select',
    placeholder: 'Semua Kelas',
    options: kelasList.value.map(k => ({ value: k, label: k }))
  },
  {
    key: 'status',
    label: 'Status',
    type: 'select',
    placeholder: 'Semua Status',
    options: [
      { value: 'hadir', label: 'Hadir' },
      { value: 'terlambat', label: 'Terlambat' },
      { value: 'izin', label: 'Izin' },
      { value: 'sakit', label: 'Sakit' },
      { value: 'alpa', label: 'Alpa' }
    ]
  }
])

const summaryFiltersConfig = computed(() => [
  {
    key: 'search',
    type: 'search',
    placeholder: 'Cari nama siswa...'
  },
  {
    key: 'startDate',
    label: 'Dari',
    type: 'date'
  },
  {
    key: 'endDate',
    label: 'Sampai',
    type: 'date'
  },
  {
    key: 'kelas',
    label: 'Kelas',
    type: 'select',
    placeholder: 'Semua Kelas',
    options: kelasList.value.map(k => ({ value: k, label: k }))
  }
])
</script>

<template>
  <div class="space-y-6 animate-in fade-in duration-300">
    <!-- Page Header -->
    <PageHeader
      title="Laporan Absensi"
      description="Rekap dan analisis kehadiran siswa berdasarkan rentang waktu yang dipilih."
      :actions="pageHeaderActions"
    />

    <!-- Stats Cards -->
    <StatCardGrid
      v-motion
      :initial="glassFade.initial"
      :visible-once="glassFade.visible"
      :cols="5"
    >
      <StatCard
        title="Kehadiran"
        :value="isLoading ? '' : `${persentaseKehadiran}%`"
        description="Rata-rata kehadiran"
        :icon="TrendingUp"
        :loading="isLoading"
      />
      <StatCard
        title="Hadir"
        :value="isLoading ? '' : totalHadir"
        description="Tepat waktu"
        :icon="UserCheck"
        iconClass="text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-950/40"
        valueClass="text-green-600 dark:text-green-400"
        :loading="isLoading"
      />
      <StatCard
        title="Terlambat"
        :value="isLoading ? '' : totalTerlambat"
        description="Tidak tepat waktu"
        :icon="Clock"
        iconClass="text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-950/40"
        valueClass="text-yellow-600 dark:text-yellow-400"
        :loading="isLoading"
      />
      <StatCard
        title="Izin / Sakit"
        :value="isLoading ? '' : totalIzinSakit"
        description="Ada keterangan"
        :icon="Users"
        iconClass="text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40"
        valueClass="text-blue-600 dark:text-blue-400"
        :loading="isLoading"
      />
      <StatCard
        title="Alpa"
        :value="isLoading ? '' : totalAlpa"
        description="Tanpa keterangan"
        :icon="UserX"
        iconClass="text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40"
        valueClass="text-red-600 dark:text-red-400"
        :loading="isLoading"
      />
    </StatCardGrid>

    <Tabs default-value="detail">
      <TabsList class="w-full justify-start">
        <TabsTrigger value="detail">Riwayat Detail</TabsTrigger>
        <TabsTrigger value="ringkasan">Ringkasan per Siswa</TabsTrigger>
      </TabsList>

      <!-- Detail Tab -->
      <TabsContent value="detail" class="mt-0">
        <div
          v-motion
          :initial="glassSlide.initial"
          :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }"
        >
          <DataTableCard
            :columns="detailColumns"
            :items="paginatedDetailData"
            :filters="detailFiltersConfig"
            v-model:filterValues="filterValues"
            :from="fromDetail"
            :to="toDetail"
            :total="totalDetail"
            :page="pageDetail"
            :per-page="perPageDetail"
            @update:page="pageDetail = $event"
            @update:perPage="perPageDetail = $event"
            illustration="textbook"
          >
            <!-- Overrides Cells -->
            <template #cell-nama="{ item }">
              <span class="font-medium text-sm text-foreground">{{ item.nama }}</span>
            </template>

            <template #cell-kelas="{ item }">
              <Badge variant="outline" class="text-xs font-normal border-white/10 dark:border-white/5 bg-background/50">
                {{ item.kelas }}
              </Badge>
            </template>

            <template #cell-jamMasuk="{ item }">
              <span class="font-mono text-xs text-muted-foreground">{{ item.jamMasuk || '-' }}</span>
            </template>

            <template #cell-jamKeluar="{ item }">
              <span class="font-mono text-xs text-muted-foreground">{{ item.jamKeluar || '-' }}</span>
            </template>

            <template #cell-status="{ item }">
              <Badge :class="getStatusClass(item.status)">
                {{ getStatusLabel(item.status) }}
              </Badge>
            </template>
          </DataTableCard>
        </div>
      </TabsContent>

      <!-- Ringkasan Tab -->
      <TabsContent value="ringkasan" class="mt-0">
        <div
          v-motion
          :initial="glassSlide.initial"
          :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }"
        >
          <DataTableCard
            :columns="summaryColumns"
            :items="paginatedSummaryData"
            :filters="summaryFiltersConfig"
            v-model:filterValues="filterValues"
            :from="fromSummary"
            :to="toSummary"
            :total="totalSummary"
            :page="pageSummary"
            :per-page="perPageSummary"
            @update:page="pageSummary = $event"
            @update:perPage="perPageSummary = $event"
            illustration="textbook"
          >
            <!-- Overrides Cells -->
            <template #cell-nama="{ item }">
              <span class="font-medium text-sm text-foreground">{{ item.nama }}</span>
            </template>

            <template #cell-kelas="{ item }">
              <Badge variant="outline" class="text-xs font-normal border-white/10 dark:border-white/5 bg-background/50">
                {{ item.kelas }}
              </Badge>
            </template>

            <template #cell-hadir="{ item }">
              <span class="text-green-600 dark:text-green-400 font-semibold">{{ item.hadir }}</span>
            </template>

            <template #cell-terlambat="{ item }">
              <span class="text-yellow-600 dark:text-yellow-400 font-semibold">{{ item.terlambat }}</span>
            </template>

            <template #cell-izin="{ item }">
              <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ item.izin }}</span>
            </template>

            <template #cell-sakit="{ item }">
              <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ item.sakit }}</span>
            </template>

            <template #cell-alpa="{ item }">
              <span class="text-red-600 dark:text-red-400 font-semibold">{{ item.alpa }}</span>
            </template>

            <template #cell-persen="{ item }">
              <Badge
                :class="[
                  (((item.hadir + item.terlambat) / item.total) * 100) >= 80
                    ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 font-bold border-none'
                    : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 font-bold border-none'
                ]"
              >
                {{ Math.round(((item.hadir + item.terlambat) / item.total) * 100) }}%
              </Badge>
            </template>
          </DataTableCard>
        </div>
      </TabsContent>
    </Tabs>
  </div>
</template>
