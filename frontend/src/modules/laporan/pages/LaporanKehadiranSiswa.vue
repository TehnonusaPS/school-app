<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
  ClipboardList,
  Download,
  AlertTriangle,
  CheckCircle2,
  Users,
  TrendingDown,
  Printer,
} from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'

// Common Components
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { glassSlide, glassFade } from '@/config/motion'
import { toast } from 'vue-sonner'

// Mock data untuk kehadiran siswa
const mockData = [
  { id: 1, nisn: '0051234567', nama: 'Ahmad Fadil', kelas: 'XI IPA 1', totalHari: 22, hadir: 20, terlambat: 1, izin: 1, sakit: 0, alpa: 0 },
  { id: 2, nisn: '0069876543', nama: 'Bunga Citra', kelas: 'XI IPA 1', totalHari: 22, hadir: 18, terlambat: 2, izin: 1, sakit: 1, alpa: 0 },
  { id: 3, nisn: '0054321987', nama: 'Cakra Khan', kelas: 'XI IPA 1', totalHari: 22, hadir: 14, terlambat: 1, izin: 2, sakit: 1, alpa: 4 },
  { id: 4, nisn: '0061122334', nama: 'Dian Sastro', kelas: 'XI IPA 1', totalHari: 22, hadir: 21, terlambat: 0, izin: 1, sakit: 0, alpa: 0 },
  { id: 5, nisn: '0055566778', nama: 'Elsa Novita', kelas: 'XI IPA 2', totalHari: 22, hadir: 22, terlambat: 0, izin: 0, sakit: 0, alpa: 0 },
  { id: 6, nisn: '0068899001', nama: 'Farhan Ramdan', kelas: 'XI IPA 2', totalHari: 22, hadir: 12, terlambat: 2, izin: 1, sakit: 2, alpa: 5 },
  { id: 7, nisn: '0052233445', nama: 'Gita Nirmala', kelas: 'XI IPA 2', totalHari: 22, hadir: 19, terlambat: 1, izin: 2, sakit: 0, alpa: 0 },
  { id: 8, nisn: '0067788990', nama: 'Hendra Saputra', kelas: 'XI IPA 1', totalHari: 22, hadir: 17, terlambat: 3, izin: 0, sakit: 2, alpa: 0 },
  { id: 9, nisn: '0051199887', nama: 'Indira Putri', kelas: 'XI IPS 1', totalHari: 22, hadir: 10, terlambat: 1, izin: 3, sakit: 2, alpa: 6 },
  { id: 10, nisn: '0063344556', nama: 'Joko Susilo', kelas: 'XI IPS 1', totalHari: 22, hadir: 20, terlambat: 0, izin: 0, sakit: 2, alpa: 0 },
]

const THRESHOLD = 75 // % kehadiran minimum

const isLoading = ref(true)
const siswaData = ref([])
const page = ref(1)
const perPage = ref(10)

// Filters State (DataTableCard format)
const filterValues = ref({
  search: '',
  semester: '1',
  kelas: 'all'
})

onMounted(() => {
  setTimeout(() => {
    siswaData.value = mockData
    isLoading.value = false
  }, 600)
})

watch(() => filterValues.value, () => {
  page.value = 1
}, { deep: true })

const kelasList = computed(() => {
  const set = new Set(siswaData.value.map(d => d.kelas))
  return Array.from(set).sort()
})

function getPercentage(siswa) {
  return Math.round(((siswa.hadir + siswa.terlambat) / siswa.totalHari) * 100)
}

const filteredData = computed(() => {
  return siswaData.value.filter(item => {
    const matchKelas = filterValues.value.kelas === 'all' || item.kelas === filterValues.value.kelas
    const matchSearch =
      !filterValues.value.search ||
      item.nama.toLowerCase().includes(filterValues.value.search.toLowerCase()) ||
      item.nisn.includes(filterValues.value.search) ||
      item.kelas.toLowerCase().includes(filterValues.value.search.toLowerCase())
    return matchKelas && matchSearch
  })
})

const paginatedData = computed(() => {
  const start = (page.value - 1) * perPage.value
  return filteredData.value.slice(start, start + perPage.value)
})

const total = computed(() => filteredData.value.length)
const from = computed(() => total.value === 0 ? 0 : (page.value - 1) * perPage.value + 1)
const to = computed(() => Math.min(page.value * perPage.value, total.value))

// Stats
const siswaDiBawahThreshold = computed(() => filteredData.value.filter(s => getPercentage(s) < THRESHOLD))
const siswaHadirPerfect = computed(() => filteredData.value.filter(s => getPercentage(s) === 100))
const avgKehadiran = computed(() => {
  if (filteredData.value.length === 0) return 0
  const totalPct = filteredData.value.reduce((sum, s) => sum + getPercentage(s), 0)
  return Math.round(totalPct / filteredData.value.length)
})

function getProgressColor(pct) {
  if (pct >= 90) return 'bg-green-500'
  if (pct >= 75) return 'bg-yellow-500'
  return 'bg-red-500'
}

function handlePrint() {
  window.print()
}

// Configs for DataTableCard
const tableColumns = [
  { key: 'nama', label: 'Nama Siswa' },
  { key: 'kelas', label: 'Kelas' },
  { key: 'hadir', label: 'Hadir', type: 'number' },
  { key: 'terlambat', label: 'Terlambat', type: 'number' },
  { key: 'izin', label: 'Izin', type: 'number' },
  { key: 'sakit', label: 'Sakit', type: 'number' },
  { key: 'alpa', label: 'Alpa', type: 'number' },
  { key: 'persen', label: '% Kehadiran' }
]

const filtersConfig = computed(() => [
  {
    key: 'search',
    type: 'search',
    placeholder: 'Cari Nama / NISN / Kelas...'
  },
  {
    key: 'semester',
    label: 'Semester',
    type: 'select',
    placeholder: 'Pilih Semester',
    options: [
      { value: '1', label: 'Semester 1' },
      { value: '2', label: 'Semester 2' }
    ]
  },
  {
    key: 'kelas',
    label: 'Kelas',
    type: 'select',
    placeholder: 'Semua Kelas',
    options: kelasList.value.map(k => ({ value: k, label: k }))
  }
])

const pageHeaderActions = computed(() => [
  {
    label: 'Cetak',
    icon: Printer,
    variant: 'outline',
    click: handlePrint
  },
  {
    label: 'Ekspor',
    icon: Download,
    variant: 'default',
    click: () => {
      toast.success('Ekspor Berhasil', {
        description: 'Laporan kehadiran siswa telah diekspor.'
      })
    }
  }
])
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 max-w-[1400px] mx-auto pb-8 text-left"
  >
    <!-- Header -->
    <PageHeader
      title="Kehadiran Siswa"
      description="Rekapitulasi persentase kehadiran dan daftar siswa yang perlu perhatian khusus."
      :actions="pageHeaderActions"
    />

    <!-- Stats Cards -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
    >
      <StatCardGrid cols="4">
        <StatCard
          label="Total Siswa"
          :value="isLoading ? 0 : total"
          sub="Terdaftar semester ini"
          :icon="Users"
          variant="default"
        />
        <StatCard
          label="Rata-rata Kehadiran"
          :value="isLoading ? 0 : avgKehadiran"
          sub="Kehadiran seluruh kelas"
          :icon="ClipboardList"
          variant="primary"
        />
        <StatCard
          label="Hadir Penuh"
          :value="isLoading ? 0 : siswaHadirPerfect.length"
          sub="Siswa dengan 100% hadir"
          :icon="CheckCircle2"
          variant="emerald"
        />
        <StatCard
          label="Perlu Perhatian"
          :value="isLoading ? 0 : siswaDiBawahThreshold.length"
          :sub="`Di bawah ${THRESHOLD}% kehadiran`"
          :icon="TrendingDown"
          variant="amber"
        />
      </StatCardGrid>
    </div>

    <!-- Alert Siswa Bermasalah -->
    <Alert v-if="!isLoading && siswaDiBawahThreshold.length > 0" variant="destructive" class="border-red-200 bg-red-50 dark:bg-red-950/20 dark:border-red-900/30 rounded-2xl">
      <AlertTriangle class="size-4" />
      <AlertTitle class="text-red-700 dark:text-red-400 font-bold">
        {{ siswaDiBawahThreshold.length }} Siswa di Bawah Batas Minimum Kehadiran ({{ THRESHOLD }}%)
      </AlertTitle>
      <AlertDescription class="text-red-600 dark:text-red-500 text-sm mt-2">
        <div class="flex flex-wrap gap-1.5 mt-1">
          <Badge
            v-for="s in siswaDiBawahThreshold"
            :key="s.id"
            variant="outline"
            class="border-red-300 text-red-700 dark:border-red-800 dark:text-red-400 text-xs bg-white/50 dark:bg-black/20"
          >
            {{ s.nama }} ({{ getPercentage(s) }}%)
          </Badge>
        </div>
      </AlertDescription>
    </Alert>

    <!-- Table -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 200 } }"
    >
      <DataTableCard
        :columns="tableColumns"
        :items="paginatedData"
        :filters="filtersConfig"
        v-model:filterValues="filterValues"
        :from="from"
        :to="to"
        :total="total"
        :page="page"
        :per-page="perPage"
        @update:page="page = $event"
        @update:perPage="perPage = $event"
        illustration="textbook"
      >
        <!-- Overrides Cells -->
        <template #cell-nama="{ item }">
          <div>
            <div class="font-semibold text-sm text-foreground">{{ item.nama }}</div>
            <div class="text-xs text-muted-foreground font-mono mt-0.5">{{ item.nisn }}</div>
          </div>
        </template>

        <template #cell-kelas="{ item }">
          <Badge variant="outline" class="text-xs font-normal border-white/10 dark:border-white/5 bg-background/50">
            {{ item.kelas }}
          </Badge>
        </template>

        <template #cell-hadir="{ value }">
          <span class="text-green-600 dark:text-green-400 font-semibold tabular-nums">{{ value }}</span>
        </template>

        <template #cell-terlambat="{ value }">
          <span class="text-yellow-600 dark:text-yellow-400 font-semibold tabular-nums">{{ value }}</span>
        </template>

        <template #cell-izin="{ value }">
          <span class="text-indigo-600 dark:text-indigo-400 font-semibold tabular-nums">{{ value }}</span>
        </template>

        <template #cell-sakit="{ value }">
          <span class="text-blue-600 dark:text-blue-400 font-semibold tabular-nums">{{ value }}</span>
        </template>

        <template #cell-alpa="{ value }">
          <span class="text-red-600 dark:text-red-400 font-semibold tabular-nums">{{ value }}</span>
        </template>

        <template #cell-persen="{ item }">
          <div class="flex items-center gap-2 min-w-[100px]">
            <div class="flex-1 h-1.5 rounded-full bg-muted overflow-hidden">
              <div
                :class="['h-full rounded-full transition-all', getProgressColor(getPercentage(item))]"
                :style="{ width: `${getPercentage(item)}%` }"
              />
            </div>
            <Badge
              variant="outline"
              :class="[
                'text-xs font-bold border-none px-2.5 py-0.5 rounded-full shrink-0',
                getPercentage(item) >= 90 ? 'bg-green-500/10 text-green-500' :
                getPercentage(item) >= THRESHOLD ? 'bg-yellow-500/10 text-yellow-500' :
                'bg-red-500/10 text-red-500'
              ]"
            >
              {{ getPercentage(item) }}%
            </Badge>
          </div>
        </template>
      </DataTableCard>
    </div>
  </div>
</template>
