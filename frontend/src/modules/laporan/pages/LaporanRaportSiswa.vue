<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
  FileBarChart,
  Download,
  Printer,
  BookOpen,
  User,
  Calendar,
  GraduationCap,
  Eye
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

// Common Components
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'
import { glassSlide, glassFade } from '@/config/motion'

// UI Components
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { ScrollArea } from '@/components/ui/scroll-area'

const mataPelajaranList = [
  'Matematika',
  'Fisika',
  'Kimia',
  'Biologi',
  'Bahasa Indonesia',
  'Bahasa Inggris',
  'Sejarah',
  'PJOK'
]

const mockRaportData = [
  {
    id: 1,
    nisn: '0051234567',
    nama: 'Ahmad Fadil',
    kelas: 'XI IPA 1',
    waliKelas: 'Bu Sari Dewi, S.Pd',
    tp: '2025/2026',
    semester: '1',
    nilai: {
      Matematika: 88,
      Fisika: 82,
      Kimia: 90,
      Biologi: 85,
      'Bahasa Indonesia': 88,
      'Bahasa Inggris': 79,
      Sejarah: 85,
      PJOK: 90
    },
    kehadiran: { hadir: 95, terlambat: 4, izin: 2, sakit: 1, alpa: 0 },
    catatan:
      'Ahmad menunjukkan kemajuan yang baik di bidang sains. Perlu meningkatkan kemampuan bahasa Inggris.'
  },
  {
    id: 2,
    nisn: '0069876543',
    nama: 'Bunga Citra',
    kelas: 'XI IPA 1',
    waliKelas: 'Bu Sari Dewi, S.Pd',
    tp: '2025/2026',
    semester: '1',
    nilai: {
      Matematika: 92,
      Fisika: 88,
      Kimia: 85,
      Biologi: 91,
      'Bahasa Indonesia': 90,
      'Bahasa Inggris': 87,
      Sejarah: 88,
      PJOK: 85
    },
    kehadiran: { hadir: 100, terlambat: 1, izin: 1, sakit: 0, alpa: 0 },
    catatan:
      'Siswa berprestasi dengan nilai konsisten di semua mata pelajaran. Direkomendasikan untuk program pengembangan bakat.'
  },
  {
    id: 3,
    nisn: '0054321987',
    nama: 'Cakra Khan',
    kelas: 'XI IPA 1',
    waliKelas: 'Bu Sari Dewi, S.Pd',
    tp: '2025/2026',
    semester: '1',
    nilai: {
      Matematika: 72,
      Fisika: 68,
      Kimia: 74,
      Biologi: 70,
      'Bahasa Indonesia': 75,
      'Bahasa Inggris': 65,
      Sejarah: 73,
      PJOK: 80
    },
    kehadiran: { hadir: 78, terlambat: 8, izin: 5, sakit: 4, alpa: 7 },
    catatan:
      'Perlu peningkatan di banyak aspek terutama kedisiplinan. Disarankan untuk mengikuti program remedial.'
  },
  {
    id: 4,
    nisn: '0061122334',
    nama: 'Dian Sastro',
    kelas: 'XI IPA 1',
    waliKelas: 'Bu Sari Dewi, S.Pd',
    tp: '2025/2026',
    semester: '1',
    nilai: {
      Matematika: 95,
      Fisika: 93,
      Kimia: 88,
      Biologi: 92,
      'Bahasa Indonesia': 89,
      'Bahasa Inggris': 91,
      Sejarah: 90,
      PJOK: 88
    },
    kehadiran: { hadir: 100, terlambat: 0, izin: 2, sakit: 0, alpa: 0 },
    catatan:
      'Siswa terbaik di kelas dengan dedikasi tinggi. Kandidat kuat untuk beasiswa berprestasi.'
  },
  {
    id: 5,
    nisn: '0055566778',
    nama: 'Elsa Novita',
    kelas: 'XI IPA 2',
    waliKelas: 'Pak Rahmat, M.Pd',
    tp: '2025/2026',
    semester: '1',
    nilai: {
      Matematika: 80,
      Fisika: 75,
      Kimia: 82,
      Biologi: 78,
      'Bahasa Indonesia': 82,
      'Bahasa Inggris': 70,
      Sejarah: 79,
      PJOK: 85
    },
    kehadiran: { hadir: 100, terlambat: 0, izin: 0, sakit: 2, alpa: 0 },
    catatan: 'Siswa yang rajin dan disiplin. Nilai konsisten di atas KKM.'
  },
  {
    id: 6,
    nisn: '0068899001',
    nama: 'Farhan Ramdan',
    kelas: 'XI IPA 2',
    waliKelas: 'Pak Rahmat, M.Pd',
    tp: '2025/2026',
    semester: '1',
    nilai: {
      Matematika: 65,
      Fisika: 60,
      Kimia: 68,
      Biologi: 63,
      'Bahasa Indonesia': 70,
      'Bahasa Inggris': 55,
      Sejarah: 67,
      PJOK: 78
    },
    kehadiran: { hadir: 68, terlambat: 10, izin: 5, sakit: 8, alpa: 11 },
    catatan:
      'Perlu perhatian khusus. Kehadiran dan nilai jauh di bawah standar. Disarankan konsultasi dengan orang tua.'
  }
]

const KKM = 75
const isLoading = ref(true)
const raportData = ref([])
const selectedRaport = ref(null)
const isDetailOpen = ref(false)

// State filter & pagination
const page = ref(1)
const perPage = ref(10)
const filterValues = ref({
  search: '',
  kelas: 'all',
  semester: '1'
})

onMounted(() => {
  setTimeout(() => {
    raportData.value = mockRaportData
    isLoading.value = false
  }, 600)
})

watch(
  () => filterValues.value,
  () => {
    page.value = 1
  },
  { deep: true }
)

const classList = computed(() => {
  const set = new Set(raportData.value.map(d => d.kelas))
  return Array.from(set).sort()
})

function getRataRata(siswa) {
  const vals = mataPelajaranList.map(m => siswa.nilai[m] ?? 0).filter(v => v > 0)
  if (!vals.length) return 0
  return (vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(1)
}

function getPredikat(rata) {
  const r = parseFloat(rata)
  if (r >= 90) return { label: 'A', desc: 'Sangat Baik' }
  if (r >= 80) return { label: 'B', desc: 'Baik' }
  if (r >= 70) return { label: 'C', desc: 'Cukup' }
  if (r >= 60) return { label: 'D', desc: 'Kurang' }
  return { label: 'E', desc: 'Sangat Kurang' }
}

function getPredikatClass(rata) {
  const r = parseFloat(rata)
  if (r >= 90)
    return 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 border-none font-bold'
  if (r >= 80)
    return 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 border-none font-bold'
  if (r >= 70)
    return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400 border-none font-bold'
  return 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 border-none font-bold'
}

function getNilaiClass(val) {
  if (!val) return 'text-muted-foreground'
  if (val >= 90) return 'text-green-600 dark:text-green-400'
  if (val >= KKM) return 'text-foreground'
  return 'text-red-600 dark:text-red-400 font-bold'
}

function getKehadiranPct(siswa) {
  const total = Object.values(siswa.kehadiran).reduce((a, b) => a + b, 0)
  if (!total) return 0
  return Math.round(((siswa.kehadiran.hadir + siswa.kehadiran.terlambat) / total) * 100)
}

const filteredData = computed(() => {
  return raportData.value.filter(item => {
    const matchKelas = filterValues.value.kelas === 'all' || item.kelas === filterValues.value.kelas
    const matchSemester =
      filterValues.value.semester === 'all' || item.semester === filterValues.value.semester
    const matchSearch =
      !filterValues.value.search ||
      item.nama.toLowerCase().includes(filterValues.value.search.toLowerCase()) ||
      item.nisn.includes(filterValues.value.search)
    return matchKelas && matchSearch && matchSemester
  })
})

const paginatedData = computed(() => {
  const start = (page.value - 1) * perPage.value
  return filteredData.value.slice(start, start + perPage.value)
})

const total = computed(() => filteredData.value.length)
const from = computed(() => (total.value === 0 ? 0 : (page.value - 1) * perPage.value + 1))
const to = computed(() => Math.min(page.value * perPage.value, total.value))

// Stats calculations
const totalSiswa = computed(() => filteredData.value.length)
const naikKelasCount = computed(
  () => filteredData.value.filter(s => parseFloat(getRataRata(s)) >= KKM).length
)
const rataRataKelas = computed(() => {
  const data = filteredData.value
  if (!data.length) return '-'
  const sum = data.reduce((acc, s) => acc + parseFloat(getRataRata(s)), 0)
  return (sum / data.length).toFixed(1)
})

function getFase(kelas) {
  if (!kelas) return '-'
  if (kelas.includes('XI') || kelas.includes('11') || kelas.includes('XII') || kelas.includes('12'))
    return 'F'
  if (kelas.includes('X') || kelas.includes('10')) return 'E'
  if (
    kelas.includes('VII') ||
    kelas.includes('7') ||
    kelas.includes('VIII') ||
    kelas.includes('8') ||
    kelas.includes('IX') ||
    kelas.includes('9')
  )
    return 'D'
  if (kelas.includes('V') || kelas.includes('5') || kelas.includes('VI') || kelas.includes('6'))
    return 'C'
  if (kelas.includes('III') || kelas.includes('3') || kelas.includes('IV') || kelas.includes('4'))
    return 'B'
  if (kelas.includes('I') || kelas.includes('1') || kelas.includes('II') || kelas.includes('2'))
    return 'A'
  return '-'
}

function getCapaianKompetensi(mapel, nilai) {
  if (nilai === undefined || nilai === null) return '-'
  if (nilai >= 85) {
    return `Menunjukkan penguasaan yang sangat baik dalam menganalisis konsep dasar ${mapel} serta terampil mengaplikasikannya dalam penyelesaian tugas.`
  } else if (nilai >= KKM) {
    return `Menunjukkan penguasaan yang baik dalam memahami materi pokok ${mapel} dan mampu menyelesaikan sebagian besar tugas dengan mandiri.`
  } else {
    return `Perlu bimbingan dan latihan lebih intensif dalam meningkatkan pemahaman materi dasar ${mapel} untuk mencapai kriteria ketuntasan.`
  }
}

function openDetail(item) {
  selectedRaport.value = item
  isDetailOpen.value = true
}

function printSingleRaport() {
  const printableArea = document.getElementById('printable-area-raport')
  if (!printableArea) return

  const iframe = document.createElement('iframe')
  iframe.style.position = 'absolute'
  iframe.style.width = '0'
  iframe.style.height = '0'
  iframe.style.border = 'none'
  document.body.appendChild(iframe)

  const styles = Array.from(document.styleSheets)
    .map(styleSheet => {
      try {
        return Array.from(styleSheet.cssRules)
          .map(rule => rule.cssText)
          .join('')
      } catch (e) {
        return ''
      }
    })
    .join('\n')

  const doc = iframe.contentWindow.document
  doc.open()
  doc.write(`
    <html>
      <head>
        <title>Print Raport Siswa</title>
        <style>
          ${styles}
          body { 
            background-color: white !important; 
            margin: 0; 
            padding: 0;
            color: black !important;
            font-family: serif;
          }
          ::-webkit-scrollbar { display: none; }
        </style>
      </head>
      <body>
        <div style="padding: 1cm; max-width: 210mm; margin: auto;">
          ${printableArea.innerHTML}
        </div>
      </body>
    </html>
  `)
  doc.close()

  setTimeout(() => {
    iframe.contentWindow.focus()
    iframe.contentWindow.print()
    setTimeout(() => {
      document.body.removeChild(iframe)
    }, 1000)
  }, 500)
}

function handlePrintAll() {
  window.print()
}

// Configs for DataTableCard
const tableColumns = [
  { key: 'nama', label: 'Nama Siswa' },
  { key: 'kelas', label: 'Kelas' },
  { key: 'waliKelas', label: 'Wali Kelas' },
  { key: 'rataRata', label: 'Rata-rata', type: 'number' },
  { key: 'kehadiran', label: 'Kehadiran', type: 'number' },
  { key: 'predikat', label: 'Predikat', badge: true },
  { key: 'status', label: 'Status', badge: true },
  { key: 'actions', label: 'Aksi' }
]

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
    options: classList.value.map(k => ({ value: k, label: k }))
  }
])

const pageHeaderActions = computed(() => [
  {
    label: 'Cetak Semua',
    icon: Printer,
    variant: 'outline',
    click: handlePrintAll
  },
  {
    label: 'Ekspor PDF',
    icon: Download,
    variant: 'default',
    click: () => {
      toast.success('Ekspor PDF Berhasil', {
        description: 'Laporan rekapitulasi raport siswa telah diekspor ke PDF.'
      })
    }
  }
])

const rowActions = [
  {
    label: 'Detail',
    icon: Eye,
    click: item => openDetail(item)
  }
]
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
      title="Raport Siswa"
      description="Rekapitulasi nilai akhir semester dan pencetakan raport siswa perwalian."
      :actions="pageHeaderActions"
    />

    <!-- Stats -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{
        ...glassSlide.visible,
        transition: { ...glassSlide.visible.transition, delay: 100 }
      }"
    >
      <StatCardGrid cols="4">
        <StatCard
          label="Total Siswa"
          :value="isLoading ? 0 : totalSiswa"
          sub="Siswa terdaftar di kelas"
          :icon="User"
          variant="default"
        />
        <StatCard
          label="Naik Kelas"
          :value="isLoading ? 0 : naikKelasCount"
          sub="Di atas rata-rata KKM"
          :icon="GraduationCap"
          variant="emerald"
        />
        <StatCard
          label="Rata-rata Kelas"
          :value="isLoading ? '-' : rataRataKelas"
          sub="Nilai rata-rata kelas"
          :icon="BookOpen"
          variant="violet"
        />
        <StatCard
          label="Tahun Pelajaran"
          value="2025/2026"
          :sub="
            filterValues.semester === 'all' ? 'Semua Semester' : `Semester ${filterValues.semester}`
          "
          :icon="Calendar"
          variant="amber"
        />
      </StatCardGrid>
    </div>

    <!-- Table -->
    <div
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{
        ...glassSlide.visible,
        transition: { ...glassSlide.visible.transition, delay: 200 }
      }"
    >
      <DataTableCard
        :columns="tableColumns"
        :items="paginatedData"
        :filters="filtersConfig"
        v-model:filterValues="filterValues"
        :row-actions="rowActions"
        :from="from"
        :to="to"
        :total="total"
        :page="page"
        :per-page="perPage"
        @update:page="page = $event"
        @update:perPage="perPage = $event"
        illustration="textbook"
      >
        <!-- Overrides cells -->
        <template #cell-nama="{ item }">
          <div>
            <div class="font-semibold text-sm text-foreground">{{ item.nama }}</div>
            <div class="text-xs text-muted-foreground font-mono mt-0.5">{{ item.nisn }}</div>
          </div>
        </template>

        <template #cell-kelas="{ item }">
          <Badge
            variant="outline"
            class="text-xs font-normal border-white/10 dark:border-white/5 bg-background/50"
          >
            {{ item.kelas }}
          </Badge>
        </template>

        <template #cell-rataRata="{ item }">
          <span
            class="font-bold text-sm"
            :class="getNilaiClass(parseFloat(getRataRata(item)))"
          >
            {{ getRataRata(item) }}
          </span>
        </template>

        <template #cell-kehadiran="{ item }">
          <Badge
            variant="outline"
            :class="[
              'text-xs font-bold border-none px-2.5 py-0.5 rounded-full',
              getKehadiranPct(item) >= 90
                ? 'bg-green-500/10 text-green-500'
                : getKehadiranPct(item) >= 75
                  ? 'bg-yellow-500/10 text-yellow-500'
                  : 'bg-red-500/10 text-red-500'
            ]"
          >
            {{ getKehadiranPct(item) }}%
          </Badge>
        </template>

        <template #cell-predikat="{ item }">
          <Badge :class="getPredikatClass(getRataRata(item))">
            {{ getPredikat(getRataRata(item)).label }} — {{ getPredikat(getRataRata(item)).desc }}
          </Badge>
        </template>

        <template #cell-status="{ item }">
          <Badge
            :class="[
              'text-xs font-bold border-none rounded-full px-2.5 py-0.5',
              parseFloat(getRataRata(item)) >= KKM
                ? 'bg-green-500/10 text-green-500'
                : 'bg-red-500/10 text-red-500'
            ]"
          >
            {{ parseFloat(getRataRata(item)) >= KKM ? 'Naik Kelas' : 'Perlu Remedial' }}
          </Badge>
        </template>
      </DataTableCard>
    </div>
  </div>

  <!-- Detail Raport Dialog -->
  <Dialog v-model:open="isDetailOpen">
    <DialogContent
      class="sm:max-w-4xl p-0 overflow-hidden bg-slate-100 dark:bg-slate-900 border-none rounded-xl"
    >
      <DialogHeader
        class="px-6 py-4 border-b border-border/40 bg-white dark:bg-slate-950 flex flex-row items-center justify-between sticky top-0 z-10 print:hidden"
      >
        <DialogTitle class="text-xl font-bold flex items-center gap-2">
          <FileBarChart class="size-5 text-primary" />
          Preview Laporan Hasil Belajar (Raport)
        </DialogTitle>
        <div class="flex gap-2">
          <Button
            @click="printSingleRaport"
            class="gap-2 rounded-full px-6 shadow-sm bg-emerald-600 hover:bg-emerald-700 text-white"
          >
            <Printer class="size-4" />
            Cetak Raport
          </Button>
        </div>
      </DialogHeader>

      <ScrollArea
        class="h-[80vh] bg-slate-100/50 dark:bg-slate-900/50 p-6 sm:p-10 print:h-auto print:p-0 print:bg-transparent"
      >
        <div v-if="selectedRaport">
          <!-- Paper Sheet -->
          <div
            id="printable-area-raport"
            class="bg-white text-black max-w-[210mm] min-h-[297mm] mx-auto p-12 shadow-md print:shadow-none print:m-0 print:p-8 rounded-sm font-sans"
          >
            <!-- TITLE -->
            <div class="text-center mb-8">
              <h1 class="text-lg font-bold uppercase tracking-wide">
                LAPORAN HASIL BELAJAR (RAPORT)
              </h1>
            </div>

            <!-- STUDENT & SCHOOL HEADER INFO -->
            <table class="w-full text-xs mb-6 table-fixed border-collapse">
              <colgroup>
                <col class="w-[140px]" />
                <col class="w-[15px]" />
                <col class="w-[38%]" />
                <col class="w-[120px]" />
                <col class="w-[15px]" />
                <col class="w-[20%]" />
              </colgroup>
              <tbody>
                <tr class="align-top">
                  <td class="py-1 font-semibold text-left whitespace-nowrap">Nama Peserta Didik</td>
                  <td class="py-1 text-center">:</td>
                  <td class="py-1 text-left font-bold">{{ selectedRaport.nama }}</td>
                  <td class="py-1 pl-6 font-semibold text-left whitespace-nowrap">Kelas</td>
                  <td class="py-1 text-center">:</td>
                  <td class="py-1 text-left font-medium">{{ selectedRaport.kelas }}</td>
                </tr>
                <tr class="align-top">
                  <td class="py-1 font-semibold text-left whitespace-nowrap">NISN</td>
                  <td class="py-1 text-center">:</td>
                  <td class="py-1 text-left font-mono">{{ selectedRaport.nisn }}</td>
                  <td class="py-1 pl-6 font-semibold text-left whitespace-nowrap">Fase</td>
                  <td class="py-1 text-center">:</td>
                  <td class="py-1 text-left font-medium">{{ getFase(selectedRaport.kelas) }}</td>
                </tr>
                <tr class="align-top">
                  <td class="py-1 font-semibold text-left whitespace-nowrap">Sekolah</td>
                  <td class="py-1 text-center">:</td>
                  <td class="py-1 text-left font-medium">SDN TEHNONUSA PRIMA I</td>
                  <td class="py-1 pl-6 font-semibold text-left whitespace-nowrap">Semester</td>
                  <td class="py-1 text-center">:</td>
                  <td class="py-1 text-left font-medium">{{ selectedRaport.semester }}</td>
                </tr>
                <tr class="align-top">
                  <td class="py-1 font-semibold text-left whitespace-nowrap">Alamat</td>
                  <td class="py-1 text-center">:</td>
                  <td class="py-1 text-left leading-relaxed break-words">
                    Jl. Pendidikan No. 1, Kec. Ilmu, Kota Pengetahuan
                  </td>
                  <td class="py-1 pl-6 font-semibold text-left whitespace-nowrap">
                    Tahun Pelajaran
                  </td>
                  <td class="py-1 text-center">:</td>
                  <td class="py-1 text-left font-medium">{{ selectedRaport.tp }}</td>
                </tr>
              </tbody>
            </table>

            <!-- TABLE 1: HASIL BELAJAR -->
            <div class="mb-6">
              <table class="w-full border-collapse border border-black text-[11px] leading-normal">
                <thead>
                  <tr class="bg-gray-100 text-center font-bold">
                    <th class="border border-black py-2 px-1 w-[40px]">No.</th>
                    <th class="border border-black py-2 px-2 text-left w-[240px]">
                      Mata Pelajaran
                    </th>
                    <th class="border border-black py-2 px-1 w-[80px]">Nilai Akhir</th>
                    <th class="border border-black py-2 px-2 text-left">Capaian Kompetensi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(mapel, index) in mataPelajaranList"
                    :key="mapel"
                  >
                    <td class="border border-black py-2 px-1 text-center">{{ index + 1 }}</td>
                    <td class="border border-black py-2 px-2 text-left">{{ mapel }}</td>
                    <td class="border border-black py-2 px-1 text-center font-bold">
                      {{ selectedRaport.nilai[mapel] ?? '-' }}
                    </td>
                    <td class="border border-black py-2 px-2 text-left text-[10px] leading-relaxed">
                      {{ getCapaianKompetensi(mapel, selectedRaport.nilai[mapel]) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- TABLE 2: EKSTRAKURIKULER -->
            <div class="mb-6">
              <table class="w-full border-collapse border border-black text-[11px]">
                <thead>
                  <tr class="bg-gray-100 text-center font-bold">
                    <th class="border border-black py-2 px-1 w-[40px]">No.</th>
                    <th class="border border-black py-2 px-2 text-left w-[240px]">
                      Ekstrakurikuler
                    </th>
                    <th class="border border-black py-2 px-2 text-left">Keterangan</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="border border-black py-2 px-1 text-center">1</td>
                    <td class="border border-black py-2 px-2 text-left font-medium">Pramuka</td>
                    <td class="border border-black py-2 px-2 text-left text-[10px] leading-relaxed">
                      Aktif berpartisipasi dalam setiap kegiatan Pramuka and menunjukkan sikap
                      disiplin, mandiri, serta kerja sama yang sangat baik dalam regu.
                    </td>
                  </tr>
                  <tr>
                    <td class="border border-black py-2 px-1 text-center">2</td>
                    <td class="border border-black py-2 px-2 text-left font-medium">
                      Silat (Bela Diri)
                    </td>
                    <td class="border border-black py-2 px-2 text-left text-[10px] leading-relaxed">
                      Menunjukkan penguasaan teknik dasar bela diri pencak silat yang baik dan
                      konsisten selama mengikuti latihan rutin mingguan.
                    </td>
                  </tr>
                  <tr>
                    <td class="border border-black py-2 px-1 text-center">dst.</td>
                    <td class="border border-black py-2 px-2 text-left text-muted-foreground">-</td>
                    <td class="border border-black py-2 px-2 text-left text-muted-foreground">-</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- ATTENDANCE & DATE PLACE (SIDE BY SIDE) -->
            <div class="grid grid-cols-2 gap-8 items-start mb-8">
              <!-- Ketidakhadiran Table -->
              <div>
                <table class="w-[260px] border-collapse border border-black text-[11px]">
                  <thead>
                    <tr class="bg-gray-100 font-bold">
                      <th
                        colspan="2"
                        class="border border-black py-1.5 px-3 text-center uppercase tracking-wide"
                      >
                        Ketidakhadiran
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="border border-black py-1 px-3 w-[140px] text-left">Sakit</td>
                      <td class="border border-black py-1 px-3 text-center font-bold">
                        {{ selectedRaport.kehadiran.sakit }} hari
                      </td>
                    </tr>
                    <tr>
                      <td class="border border-black py-1 px-3 text-left">Izin</td>
                      <td class="border border-black py-1 px-3 text-center font-bold">
                        {{ selectedRaport.kehadiran.izin }} hari
                      </td>
                    </tr>
                    <tr>
                      <td class="border border-black py-1 px-3 text-left text-red-600 font-medium">
                        Tanpa Keterangan (Alpa)
                      </td>
                      <td class="border border-black py-1 px-3 text-center font-bold text-red-600">
                        {{ selectedRaport.kehadiran.alpa }} hari
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Date info -->
              <div class="text-right text-xs pr-4 pt-1 font-medium">
                <p>
                  Jakarta,
                  {{
                    new Date().toLocaleDateString('id-ID', {
                      day: 'numeric',
                      month: 'long',
                      year: 'numeric'
                    })
                  }}
                </p>
              </div>
            </div>

            <!-- SIGNATURES SECTION -->
            <div>
              <!-- Signature: Parents & Teacher -->
              <div class="grid grid-cols-2 text-center text-xs mb-10">
                <div>
                  <p class="mb-20">Orang Tua/Wali Murid</p>
                  <div class="border-b border-black w-44 mx-auto"></div>
                </div>
                <div>
                  <p class="mb-20">Wali Kelas</p>
                  <p class="font-bold underline">{{ selectedRaport.waliKelas.split(',')[0] }}</p>
                  <p class="text-[10px] text-muted-foreground">NIP. -</p>
                </div>
              </div>

              <!-- Signature: Headmaster -->
              <div class="text-center text-xs">
                <p>Mengetahui,</p>
                <p class="mb-20">Kepala Sekolah</p>
                <p class="font-bold underline">Dr. H. Ahmad Dahlan, M.Pd.</p>
                <p class="text-[10px] text-muted-foreground">NIP. 19700101 199512 1 001</p>
              </div>
            </div>
          </div>
        </div>
      </ScrollArea>
    </DialogContent>
  </Dialog>
</template>
