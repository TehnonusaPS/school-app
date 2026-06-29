<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
  GraduationCap,
  Download,
  Trophy,
  TrendingUp,
  AlertCircle,
  BookOpen,
  Printer,
  Star,
} from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Card,
} from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs'
import { Separator } from '@/components/ui/separator'

// Common Components
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { glassSlide, glassFade } from '@/config/motion'
import { toast } from 'vue-sonner'

// Mock Data
const mataPelajaranList = ['Matematika', 'Fisika', 'Kimia', 'Biologi', 'Bahasa Indonesia', 'Bahasa Inggris', 'Sejarah', 'Ekonomi']

const mockNilaiSiswa = [
  { id: 1, nisn: '0051234567', nama: 'Ahmad Fadil', kelas: 'XI IPA 1', nilai: { Matematika: 88, Fisika: 82, Kimia: 90, Biologi: 85, 'Bahasa Indonesia': 88, 'Bahasa Inggris': 79 } },
  { id: 2, nisn: '0069876543', nama: 'Bunga Citra', kelas: 'XI IPA 1', nilai: { Matematika: 92, Fisika: 88, Kimia: 85, Biologi: 91, 'Bahasa Indonesia': 90, 'Bahasa Inggris': 87 } },
  { id: 3, nisn: '0054321987', nama: 'Cakra Khan', kelas: 'XI IPA 1', nilai: { Matematika: 72, Fisika: 68, Kimia: 74, Biologi: 70, 'Bahasa Indonesia': 75, 'Bahasa Inggris': 65 } },
  { id: 4, nisn: '0061122334', nama: 'Dian Sastro', kelas: 'XI IPA 1', nilai: { Matematika: 95, Fisika: 93, Kimia: 88, Biologi: 92, 'Bahasa Indonesia': 89, 'Bahasa Inggris': 91 } },
  { id: 5, nisn: '0055566778', nama: 'Elsa Novita', kelas: 'XI IPA 2', nilai: { Matematika: 80, Fisika: 75, Kimia: 82, Biologi: 78, 'Bahasa Indonesia': 82, 'Bahasa Inggris': 70 } },
  { id: 6, nisn: '0068899001', nama: 'Farhan Ramdan', kelas: 'XI IPA 2', nilai: { Matematika: 65, Fisika: 60, Kimia: 68, Biologi: 63, 'Bahasa Indonesia': 70, 'Bahasa Inggris': 55 } },
  { id: 7, nisn: '0052233445', nama: 'Gita Nirmala', kelas: 'XI IPA 2', nilai: { Matematika: 85, Fisika: 80, Kimia: 83, Biologi: 87, 'Bahasa Indonesia': 86, 'Bahasa Inggris': 82 } },
  { id: 8, nisn: '0067788990', nama: 'Hendra Saputra', kelas: 'XI IPA 1', nilai: { Matematika: 78, Fisika: 73, Kimia: 76, Biologi: 80, 'Bahasa Indonesia': 77, 'Bahasa Inggris': 72 } },
]

const KKM = 75 // Kriteria Ketuntasan Minimal

const isLoading = ref(true)
const nilaiData = ref([])
const page = ref(1)
const perPage = ref(10)

// Filters State (DataTableCard format)
const filterValues = ref({
  search: '',
  semester: '1',
  kelas: 'all',
  mapel: 'all'
})

onMounted(() => {
  setTimeout(() => {
    nilaiData.value = mockNilaiSiswa
    isLoading.value = false
  }, 700)
})

watch(() => filterValues.value, () => {
  page.value = 1
}, { deep: true })

function getRataRata(siswa) {
  const mapelList = filterValues.value.mapel === 'all'
    ? mataPelajaranList
    : [filterValues.value.mapel]
  const vals = mapelList.map(m => siswa.nilai[m] ?? 0).filter(v => v > 0)
  if (vals.length === 0) return 0
  return Math.round(vals.reduce((a, b) => a + b, 0) / vals.length)
}

function getPredikat(rata) {
  if (rata >= 90) return 'A'
  if (rata >= 80) return 'B'
  if (rata >= 70) return 'C'
  if (rata >= 60) return 'D'
  return 'E'
}

function getPredikatClass(rata) {
  if (rata >= 90) return 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 font-bold border-none'
  if (rata >= 80) return 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 font-bold border-none'
  if (rata >= 70) return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400 font-bold border-none'
  return 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 font-bold border-none'
}

function getNilaiClass(val) {
  if (val === undefined || val === null) return 'text-muted-foreground'
  if (val >= 90) return 'text-green-600 dark:text-green-400 font-semibold'
  if (val >= KKM) return 'text-foreground'
  return 'text-red-600 dark:text-red-400 font-semibold'
}

const kelasList = computed(() => {
  const set = new Set(nilaiData.value.map(d => d.kelas))
  return Array.from(set).sort()
})

const activeMapelList = computed(() =>
  filterValues.value.mapel === 'all' ? mataPelajaranList : [filterValues.value.mapel]
)

const filteredData = computed(() => {
  return nilaiData.value.filter(item => {
    const matchKelas = filterValues.value.kelas === 'all' || item.kelas === filterValues.value.kelas
    const matchSearch =
      !filterValues.value.search ||
      item.nama.toLowerCase().includes(filterValues.value.search.toLowerCase()) ||
      item.nisn.includes(filterValues.value.search)
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
const avgNilai = computed(() => {
  if (filteredData.value.length === 0) return 0
  return Math.round(filteredData.value.reduce((sum, s) => sum + getRataRata(s), 0) / filteredData.value.length)
})

const siswaLulus = computed(() => filteredData.value.filter(s => getRataRata(s) >= KKM).length)
const siswaTidakLulus = computed(() => filteredData.value.filter(s => getRataRata(s) < KKM).length)

// Peringkat
const peringkat = computed(() => {
  return [...filteredData.value]
    .map(s => ({ ...s, rata: getRataRata(s) }))
    .sort((a, b) => b.rata - a.rata)
    .slice(0, 3)
})

function handlePrint() {
  window.print()
}

// Configs for DataTableCard
const tableColumns = computed(() => {
  const cols = [
    { key: 'nama', label: 'Nama Siswa' },
    { key: 'kelas', label: 'Kelas' }
  ]
  activeMapelList.value.forEach(mapel => {
    cols.push({ key: mapel, label: mapel, type: 'number' })
  })
  cols.push({ key: 'rata', label: 'Rata²', type: 'number' })
  cols.push({ key: 'predikat', label: 'Predikat', badge: true })
  return cols
})

const filtersConfig = computed(() => [
  {
    key: 'search',
    type: 'search',
    placeholder: 'Cari Nama / NISN...'
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
  },
  {
    key: 'mapel',
    label: 'Mata Pelajaran',
    type: 'select',
    placeholder: 'Semua Mapel',
    options: mataPelajaranList.map(m => ({ value: m, label: m }))
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
    label: 'Ekspor Excel',
    icon: Download,
    variant: 'default',
    click: () => {
      toast.success('Ekspor Excel Berhasil', {
        description: 'Laporan nilai siswa telah diekspor ke Excel.'
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
      title="Laporan Nilai Siswa"
      description="Rekapitulasi nilai akademis per mata pelajaran beserta rata-rata dan predikat ketuntasan."
      :actions="pageHeaderActions"
    />

    <!-- Stats -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
    >
      <StatCardGrid cols="4">
        <StatCard
          label="Rata-rata Kelas"
          :value="isLoading ? 0 : avgNilai"
          sub="Nilai rata-rata semua mapel"
          :icon="TrendingUp"
          variant="violet"
        />
        <StatCard
          label="Total Siswa"
          :value="isLoading ? 0 : total"
          sub="Terdaftar semester ini"
          :icon="BookOpen"
          variant="default"
        />
        <StatCard
          label="Tuntas KKM"
          :value="isLoading ? 0 : siswaLulus"
          :sub="`Di atas KKM (${KKM})`"
          :icon="Trophy"
          variant="emerald"
        />
        <StatCard
          label="Belum Tuntas"
          :value="isLoading ? 0 : siswaTidakLulus"
          sub="Memerlukan remedial"
          :icon="AlertCircle"
          variant="amber"
        />
      </StatCardGrid>
    </div>

    <Tabs default-value="nilai">
      <TabsList class="w-full justify-start">
        <TabsTrigger value="nilai">Tabel Nilai</TabsTrigger>
        <TabsTrigger value="peringkat">Peringkat Kelas</TabsTrigger>
      </TabsList>

      <!-- Nilai Tab -->
      <TabsContent value="nilai" class="mt-0 space-y-4">
        <!-- Info KKM -->
        <div class="flex items-center gap-2 text-xs text-muted-foreground bg-muted/40 px-3 py-2 rounded-lg border">
          <AlertCircle class="size-3.5 shrink-0" />
          <span>KKM: <strong>{{ KKM }}</strong> — Nilai di bawah KKM ditampilkan dalam warna merah.</span>
        </div>

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

            <!-- Dynamic mapel overrides -->
            <template v-for="mapel in mataPelajaranList" #[`cell-${mapel}`]="{ item }">
              <span :class="getNilaiClass(item.nilai[mapel])">
                {{ item.nilai[mapel] ?? '-' }}
              </span>
            </template>

            <template #cell-rata="{ item }">
              <span class="font-bold text-sm" :class="getNilaiClass(getRataRata(item))">
                {{ getRataRata(item) }}
              </span>
            </template>

            <template #cell-predikat="{ item }">
              <Badge :class="getPredikatClass(getRataRata(item))">
                {{ getPredikat(getRataRata(item)) }}
              </Badge>
            </template>
          </DataTableCard>
        </div>
      </TabsContent>

      <!-- Peringkat Tab -->
      <TabsContent value="peringkat" class="mt-0">
        <div class="grid gap-4 grid-cols-1 md:grid-cols-3 mb-6">
          <Card
            v-for="(siswa, idx) in peringkat"
            :key="siswa.id"
            :class="[
              'p-6 text-center hover:shadow-md transition-shadow relative overflow-hidden rounded-2xl border',
              idx === 0 ? 'border-yellow-300 dark:border-yellow-700 bg-yellow-50/20 dark:bg-yellow-950/10' : '',
              idx === 1 ? 'border-slate-300 dark:border-slate-600' : '',
              idx === 2 ? 'border-amber-300 dark:border-amber-700 bg-amber-50/20 dark:bg-amber-950/10' : '',
            ]"
          >
            <Skeleton v-if="isLoading" class="h-24 w-full" />
            <template v-else>
              <div class="flex justify-center mb-3">
                <div :class="[
                  'w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold',
                  idx === 0 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400' :
                  idx === 1 ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' :
                  'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400'
                ]">
                  {{ idx + 1 }}
                </div>
              </div>
              <div class="flex items-center justify-center gap-1 mb-1">
                <Star v-if="idx === 0" class="size-4 text-yellow-500 fill-yellow-400 animate-pulse" />
                <h3 class="font-bold text-base">{{ siswa.nama }}</h3>
              </div>
              <p class="text-sm text-muted-foreground mb-3">{{ siswa.kelas }}</p>
              <div class="text-3xl font-bold tracking-tight" :class="[
                idx === 0 ? 'text-yellow-600 dark:text-yellow-400' :
                idx === 1 ? 'text-slate-600 dark:text-slate-300' :
                'text-amber-600 dark:text-amber-400'
              ]">
                {{ siswa.rata }}
              </div>
              <p class="text-xs text-muted-foreground mt-1">Rata-rata Nilai</p>
              <Badge :class="['mt-3', getPredikatClass(siswa.rata)]">
                Predikat {{ getPredikat(siswa.rata) }}
              </Badge>
            </template>
          </Card>
        </div>

        <Separator class="my-6" />

        <Card class="overflow-hidden border border-border/40 rounded-2xl">
          <Table>
            <TableHeader class="bg-muted/50">
              <TableRow>
                <TableHead class="font-semibold w-[65px] text-center">No</TableHead>
                <TableHead class="w-12 font-semibold text-center">Peringkat</TableHead>
                <TableHead class="font-semibold">Nama Siswa</TableHead>
                <TableHead class="font-semibold">Kelas</TableHead>
                <TableHead class="font-semibold text-center">Rata-rata</TableHead>
                <TableHead class="font-semibold text-center">Predikat</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="(siswa, idx) in [...filteredData].map(s => ({ ...s, rata: getRataRata(s) })).sort((a, b) => b.rata - a.rata)"
                :key="siswa.id"
                class="hover:bg-muted/30 transition-colors"
              >
                <TableCell class="text-center text-xs text-muted-foreground">{{ idx + 1 }}</TableCell>
                <TableCell class="text-center font-bold text-sm">{{ idx + 1 }}</TableCell>
                <TableCell>
                  <div class="font-semibold text-sm">{{ siswa.nama }}</div>
                  <div class="text-xs text-muted-foreground font-mono mt-0.5">{{ siswa.nisn }}</div>
                </TableCell>
                <TableCell>
                  <Badge variant="outline" class="text-xs font-normal border-white/10 dark:border-white/5 bg-background/50">{{ siswa.kelas }}</Badge>
                </TableCell>
                <TableCell class="text-center font-bold" :class="getNilaiClass(siswa.rata)">{{ siswa.rata }}</TableCell>
                <TableCell class="text-center">
                  <Badge :class="getPredikatClass(siswa.rata)">{{ getPredikat(siswa.rata) }}</Badge>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </Card>
      </TabsContent>
    </Tabs>
  </div>
</template>
