<script setup>
import { ref, computed, watch } from 'vue'
import { BookOpen, Award, Smile, FileText, CheckCircle2 } from 'lucide-vue-next'
import { toast } from 'vue-sonner'

// Common Components
import PageHeader from '@/components/page-header/PageHeader.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import DataTableCard from '@/components/data-table/DataTableCard.vue'

// UI Components
import { Card } from '@/components/ui/card'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'

// Mock Data
import { siswaProfile, siswaRaportData } from '../data/mockSiswaRaport'

// --- Table Filters State ---
const filterValues = ref({
  kelas: '11-A IPA',
  semester: 'Semester Ganjil'
})

// Auto-reset 'all' selections to maintain valid report contexts
watch(() => filterValues.value.kelas, (newVal) => {
  if (newVal === 'all') {
    filterValues.value.kelas = '11-A IPA'
  }
})

watch(() => filterValues.value.semester, (newVal) => {
  if (newVal === 'all') {
    filterValues.value.semester = 'Semester Ganjil'
  }
})

// --- Computed Values for Current Selection ---
const currentKelas = computed(() => {
  return filterValues.value.kelas === 'all' ? '11-A IPA' : filterValues.value.kelas
})

const currentSemester = computed(() => {
  return filterValues.value.semester === 'all' ? 'Semester Ganjil' : filterValues.value.semester
})

const currentGrades = computed(() => {
  return siswaRaportData[currentKelas.value]?.[currentSemester.value] || []
})

// --- Dynamic Stats Calculations ---
const averageGrade = computed(() => {
  if (currentGrades.value.length === 0) return 0
  const total = currentGrades.value.reduce((acc, item) => acc + item.nilaiAkhir, 0)
  return Number((total / currentGrades.value.length).toFixed(1))
})

const averageTrend = computed(() => {
  const k = currentKelas.value
  const s = currentSemester.value
  
  if (k === '11-A IPA' && s === 'Semester Ganjil') {
    return { trend: '+2.4%', dir: 'up', sub: 'Naik dari 86.0 semester lalu' }
  } else if (k === '11-A IPA' && s === 'Semester Genap') {
    return { trend: '+2.1%', dir: 'up', sub: 'Naik dari 87.8 semester lalu' }
  } else if (k === '10-A IPA' && s === 'Semester Ganjil') {
    return { trend: '+1.5%', dir: 'up', sub: 'Naik dari 80.1 di SMP' }
  } else {
    return { trend: '+3.9%', dir: 'up', sub: 'Naik dari 81.6 semester lalu' }
  }
})

const kkmAchievement = computed(() => {
  const tuntas = currentGrades.value.filter(item => item.nilaiAkhir >= item.kkm).length
  const total = currentGrades.value.length
  return {
    value: `${tuntas} / ${total}`,
    sub: tuntas === total ? 'Seluruh mata pelajaran lulus KKM' : `${total - tuntas} mata pelajaran di bawah KKM`
  }
})

const characterAttitude = computed(() => {
  const k = currentKelas.value
  if (k === '11-A IPA') {
    return {
      grade: 'A',
      sub: 'Kemandirian & Gotong Royong',
      trend: 'Sangat Baik'
    }
  } else {
    return {
      grade: 'B',
      sub: 'Kedisiplinan & Tanggung Jawab',
      trend: 'Baik'
    }
  }
})

// --- Table Columns Setup ---
const tableColumns = [
  { key: 'mapel', label: 'Mata Pelajaran' },
  { key: 'kkm', label: 'KKM', type: 'number' },
  { key: 'tugas', label: 'Tugas', type: 'number' },
  { key: 'uts', label: 'UTS', type: 'number' },
  { key: 'uas', label: 'UAS', type: 'number' },
  { key: 'nilaiAkhir', label: 'Nilai Akhir', type: 'number' },
  { key: 'predikat', label: 'Predikat', badge: true }
]

const filtersConfig = computed(() => [
  {
    key: 'kelas',
    label: 'Kelas',
    type: 'select',
    placeholder: 'Pilih Kelas',
    options: [
      { value: '11-A IPA', label: '11-A IPA' },
      { value: '10-A IPA', label: '10-A IPA' }
    ]
  },
  {
    key: 'semester',
    label: 'Semester',
    type: 'select',
    placeholder: 'Pilih Semester',
    options: [
      { value: 'Semester Ganjil', label: 'Semester Ganjil' },
      { value: 'Semester Genap', label: 'Semester Genap' }
    ]
  }
])

// --- Pagination State ---
const page = ref(1)
const perPage = ref(10)

const total = computed(() => currentGrades.value.length)
const from = computed(() => total.value === 0 ? 0 : (page.value - 1) * perPage.value + 1)
const to = computed(() => Math.min(page.value * perPage.value, total.value))

// --- PageHeader Actions ---
const pageHeaderActions = computed(() => [
  {
    label: 'Unduh Raport',
    icon: FileText,
    variant: 'default',
    click: handleDownloadReport
  }
])

// --- Helpers ---
const getInitials = (name) => {
  if (!name) return '?'
  const parts = name.trim().split(/\s+/)
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase()
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
}

const getSubjectStyle = (subject) => {
  const styles = {
    'Matematika': { color: 'text-blue-500 dark:text-blue-400', bg: 'bg-blue-500/10 dark:bg-blue-500/20' },
    'Fisika': { color: 'text-emerald-500 dark:text-emerald-400', bg: 'bg-emerald-500/10 dark:bg-emerald-500/20' },
    'Bahasa Indonesia': { color: 'text-amber-500 dark:text-amber-400', bg: 'bg-amber-500/10 dark:bg-amber-500/20' },
    'Kimia': { color: 'text-purple-500 dark:text-purple-400', bg: 'bg-purple-500/10 dark:bg-purple-500/20' },
    'Biologi': { color: 'text-pink-500 dark:text-pink-400', bg: 'bg-pink-500/10 dark:bg-pink-500/20' },
    'Bahasa Inggris': { color: 'text-indigo-500 dark:text-indigo-400', bg: 'bg-indigo-500/10 dark:bg-indigo-500/20' },
    'Pendidikan Agama': { color: 'text-cyan-500 dark:text-cyan-400', bg: 'bg-cyan-500/10 dark:bg-cyan-500/20' },
    'PJOK': { color: 'text-orange-500 dark:text-orange-400', bg: 'bg-orange-500/10 dark:bg-orange-500/20' }
  }
  return styles[subject] || { color: 'text-primary', bg: 'bg-primary/10' }
}

const handleDownloadReport = () => {
  const fileName = `Raport_${siswaProfile.name.replace(/\s+/g, '_')}_${currentKelas.value.replace(/\s+/g, '_')}_${currentSemester.value.replace(/\s+/g, '_')}.pdf`
  
  // PDF format structure
  const mockContent = `%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R >>
endobj
4 0 obj
<< /Length 350 >>
stream
BT
/F1 14 Tf
50 800 Td
(BUKU NILAI RAPORT SISWA) Tj
0 -25 Td
(Siswa: ${siswaProfile.name}) Tj
0 -20 Td
(NISN: ${siswaProfile.nisn}) Tj
0 -20 Td
(Kelas: ${currentKelas.value} - ${currentSemester.value}) Tj
0 -40 Td
(Rangkuman Nilai:) Tj
0 -20 Td
(- Rata-rata Nilai: ${averageGrade.value}) Tj
0 -20 Td
(- Ketercapaian KKM: ${kkmAchievement.value.value}) Tj
0 -20 Td
(- Kehadiran: ${siswaProfile.kehadiran}) Tj
0 -20 Td
(- Sikap & Karakter: ${characterAttitude.value.grade} [${characterAttitude.value.trend}]) Tj
ET
endstream
endobj
xref
0 5
0000000000 65535 f
0000000009 00000 n
0000000062 00000 n
0000000121 00000 n
0000000213 00000 n
trailer
<< /Size 5 /Root 1 0 R >>
startxref
500
%%EOF`

  const blob = new Blob([mockContent], { type: 'application/pdf' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = fileName
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)

  toast.success(`Raport ${currentKelas.value} - ${currentSemester.value} berhasil diunduh!`, {
    description: `File tersimpan sebagai ${fileName}`
  })
}
</script>

<template>
  <div class="space-y-6 max-w-[1400px] mx-auto pb-8">
    <!-- Header -->
    <PageHeader
      title="Buku Nilai Siswa"
      :description="`Kelola pencapaian akademik dan unduh raport semester ${currentSemester === 'Semester Ganjil' ? 'ganjil' : 'genap'} ${currentKelas === '11-A IPA' ? '2023/2024' : '2022/2023'}.`"
      :actions="pageHeaderActions"
    />

    <!-- Student Profile Card -->
    <Card class="glass-ui relative overflow-hidden p-6 rounded-2xl border border-white/10 dark:border-white/5 shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-4.5">
          <Avatar class="h-16 w-16 rounded-2xl border border-white/20 dark:border-white/10 shadow-inner shrink-0">
            <AvatarImage :src="siswaProfile.avatar" :alt="siswaProfile.name" />
            <AvatarFallback class="rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-xl">
              {{ getInitials(siswaProfile.name) }}
            </AvatarFallback>
          </Avatar>
          <div class="space-y-1.5">
            <div class="flex items-center gap-2">
              <h2 class="text-xl font-bold tracking-tight text-foreground">{{ siswaProfile.name }}</h2>
              <span class="inline-flex items-center justify-center size-5 bg-emerald-500 text-white text-[10px] rounded-full font-bold">✓</span>
            </div>
            <p class="text-sm font-medium text-muted-foreground">
              Kelas {{ currentKelas }} &bull; {{ currentSemester }}
            </p>
            <div class="flex flex-wrap gap-2 mt-1">
              <Badge variant="blue" class="font-semibold text-[10px] uppercase tracking-wider bg-blue-500/10 text-blue-500 hover:bg-blue-500/20 border-blue-500/20">
                Peringkat #{{ currentKelas === '11-A IPA' ? '3' : '5' }}
              </Badge>
              <Badge variant="green" class="font-semibold text-[10px] uppercase tracking-wider bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500/20 border-emerald-500/20">
                Kehadiran {{ siswaProfile.kehadiran }}
              </Badge>
            </div>
          </div>
        </div>
      </div>
    </Card>

    <!-- Stats Cards Grid -->
    <StatCardGrid cols="3">
      <!-- Card 1: Rata-rata Nilai -->
      <StatCard
        label="Rata-rata Nilai"
        :value="averageGrade"
        :sub="averageTrend.sub"
        :trend="averageTrend.trend"
        :trendDirection="averageTrend.dir"
        :icon="Award"
        variant="violet"
      />

      <!-- Card 2: Ketercapaian KKM -->
      <StatCard
        label="Mata Pelajaran Tuntas"
        :value="kkmAchievement.value"
        :sub="kkmAchievement.sub"
        :icon="CheckCircle2"
        variant="emerald"
      />

      <!-- Card 3: Karakter & Sikap -->
      <StatCard
        label="Karakter & Sikap"
        :value="characterAttitude.grade"
        :sub="characterAttitude.sub"
        :trend="characterAttitude.trend"
        trendDirection="neutral"
        :icon="Smile"
        variant="amber"
      />
    </StatCardGrid>

    <!-- Details Table -->
    <div class="text-left">
      <DataTableCard
        :columns="tableColumns"
        :items="currentGrades"
        :filters="filtersConfig"
        v-model:filterValues="filterValues"
        :from="from"
        :to="to"
        :total="total"
        :page="page"
        :per-page="perPage"
        @update:page="page = $event"
        @update:perPage="perPage = $event"
      >
        <!-- Custom cell override for mapel -->
        <template #cell-mapel="{ value }">
          <div class="flex items-center gap-3 text-left">
            <div 
              :class="[
                'rounded-xl p-2 shrink-0 border border-white/5 shadow-sm',
                getSubjectStyle(value).bg,
                getSubjectStyle(value).color
              ]"
            >
              <BookOpen class="size-4" />
            </div>
            <span class="font-semibold text-foreground text-sm">{{ value }}</span>
          </div>
        </template>

        <!-- Custom cell override for nilaiAkhir -->
        <template #cell-nilaiAkhir="{ value, item }">
          <span 
            :class="[
              'font-bold text-sm',
              value < item.kkm ? 'text-red-500 font-extrabold' : 'text-foreground'
            ]"
          >
            {{ value }}
          </span>
        </template>

        <!-- Custom cell override for KKM, Tugas, UTS, UAS -->
        <template #cell-kkm="{ value }">
          <span class="font-medium text-muted-foreground text-sm tabular-nums">{{ value }}</span>
        </template>
        <template #cell-tugas="{ value }">
          <span class="font-medium text-muted-foreground text-sm tabular-nums">{{ value }}</span>
        </template>
        <template #cell-uts="{ value }">
          <span class="font-medium text-muted-foreground text-sm tabular-nums">{{ value }}</span>
        </template>
        <template #cell-uas="{ value }">
          <span class="font-medium text-muted-foreground text-sm tabular-nums">{{ value }}</span>
        </template>
      </DataTableCard>
    </div>
  </div>
</template>
