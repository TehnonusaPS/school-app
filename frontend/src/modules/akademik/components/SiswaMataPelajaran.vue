<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { FolderOpen, Book, BookOpen, Download, Play, FileText, ChevronLeft, ChevronRight, ClipboardList } from 'lucide-vue-next'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import AppCard from '@/components/app-card/AppCard.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import ActivityCard from '@/components/activity-card/ActivityCard.vue'
import GradeDetailCard from '@/components/grade-detail-card/GradeDetailCard.vue'
import ScoreCircleCard from '@/components/score-circle-card/ScoreCircleCard.vue'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'

import { useSiswaAkademik } from '../composables/useSiswaAkademik'
import { formatNumber } from '@/utils/formatNumber'
import { mockAktivitas } from '../data/mockSiswaAktivitas'

const {
  classrooms,
  subjects,
  overview,
  globalStats,
  isLoading,
  selectedClassroomId,
  selectedSubjectId,
  selectedMaterialId,
  fetchClassrooms,
  fetchSubjects,
  fetchOverview,
  fetchStats,
  handleDownload
} = useSiswaAkademik()

const materiPage = ref(1)
const aktivitasPage = ref(1)

onMounted(async () => {
  await fetchClassrooms()
})

// Watch classroom selection changes
watch(selectedClassroomId, async (newVal) => {
  if (newVal) {
    await fetchSubjects(newVal)
    await fetchStats(newVal)
    materiPage.value = 1
    aktivitasPage.value = 1
  }
})

// Watch subject selection changes
watch(selectedSubjectId, async (newVal) => {
  if (newVal && selectedClassroomId.value) {
    await fetchOverview(newVal, selectedClassroomId.value)
    materiPage.value = 1
  }
})

// Reset page on material select change
watch(selectedMaterialId, () => {
  materiPage.value = 1
})

const currentClassroomLabel = computed(() => {
  const current = classrooms.value.find(c => String(c.classroom_id) === String(selectedClassroomId.value))
  return current ? `${current.classroom_name} (${current.academic_year_name} ${current.semester_label})` : 'Pilih Kelas & Semester'
})

const currentSubjectLabel = computed(() => {
  const current = subjects.value.find(s => String(s.id) === String(selectedSubjectId.value))
  return current ? current.name : 'Pilih Mata Pelajaran'
})

const currentMaterial = computed(() => {
  if (!overview.value.materials || overview.value.materials.length === 0) return null
  if (!selectedMaterialId.value || selectedMaterialId.value === 'all') {
    return overview.value.materials[0]
  }
  return overview.value.materials.find(m => String(m.id) === String(selectedMaterialId.value)) || overview.value.materials[0]
})

// Auto-select first material when overview materials change
watch(() => overview.value.materials, (newMats) => {
  if (newMats && newMats.length > 0) {
    selectedMaterialId.value = String(newMats[0].id)
  } else {
    selectedMaterialId.value = ''
  }
})

const currentMateriItems = computed(() => {
  const items = []
  const mat = currentMaterial.value
  if (!mat) return items

  // 1. File download item
  items.push({
    id: mat.id,
    type: 'file',
    title: mat.title,
    file_name: mat.file_name,
    file_type: mat.file_type || 'pdf',
    file_size: mat.file_size || 0,
    created_at: mat.created_at
  })

  // 2. Assessments linked to this material
  if (overview.value.assessments) {
    overview.value.assessments.forEach(a => {
      if (String(a.material_id) === String(mat.id)) {
        items.push({
          id: a.id,
          type: 'assessment',
          category: a.category,
          title: a.title,
          score: a.score
        })
      }
    })
  }

  return items
})

const paginatedMateriItems = computed(() => {
  const start = (materiPage.value - 1) * 3
  return currentMateriItems.value.slice(start, start + 3)
})

const totalMateriPages = computed(() => {
  return Math.ceil(currentMateriItems.value.length / 3) || 1
})

const paginatedAktivitasItems = computed(() => {
  const start = (aktivitasPage.value - 1) * 3
  return mockAktivitas.slice(start, start + 3)
})

const totalAktivitasPages = computed(() => {
  return Math.ceil(mockAktivitas.length / 3) || 1
})

// Format Category List for GradeDetailCard: limit to 2 items + '...'
const formattedCategories = computed(() => {
  const stats = overview.value.stats || {}
  const cats = [
    {
      label: 'Tugas',
      weight: 40,
      score: stats.avg_tugas || 0,
      maxScore: 100,
      items: (stats.tugas_details || []).map(t => ({ label: t.label, value: t.value }))
    },
    {
      label: 'Ujian',
      weight: 60,
      score: stats.avg_ujian || 0,
      maxScore: 100,
      items: (stats.ujian_details || []).map(u => ({ label: u.label, value: u.value }))
    }
  ]

  return cats.map(cat => {
    let slicedItems = cat.items.slice(0, 2)
    if (cat.items.length > 2) {
      slicedItems.push({ label: '...', value: '' })
    }
    return {
      ...cat,
      items: slicedItems
    }
  })
})

const averageScoreData = computed(() => {
  const score = overview.value.stats?.avg_keseluruhan || 0
  let label = 'Cukup Baik !'
  if (score >= 90) label = 'Sangat Baik !'
  else if (score >= 75) label = 'Baik !'
  else if (score < 60) label = 'Perlu Ditingkatkan !'

  return {
    title: `Rata-rata Nilai ${currentSubjectLabel.value}`,
    score: String(score),
    label,
    description: `Nilai Akhir Semester (Tugas 40% & Ujian 60%)`,
    percentage: score,
    variant: 'purple'
  }
})

const attendanceData = {
  title: 'Persentase Kehadiran',
  score: '98%',
  label: 'Sangat Baik !',
  description: 'Persentase absensi kehadiran kelas semester ini',
  percentage: 98,
  variant: 'emerald'
}

const formatSize = (bytes) => {
  if (!bytes) return '0 KB'
  const kb = bytes / 1024
  if (kb < 1024) return `${kb.toFixed(1)} KB`
  return `${(kb / 1024).toFixed(1)} MB`
}
</script>

<template>
  <div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
      <StatCard 
        label="Nilai Rata-Rata (Seluruh Pelajaran)" 
        :value="formatNumber(globalStats.avg_all_subjects)"
        sub="Rata-rata raport seluruh pelajaran" 
        :icon="BookOpen" 
        illustration="apple" 
        variant="blue" 
      />

      <StatCard 
        label="Jumlah Tugas Terselesaikan" 
        :value="formatNumber(globalStats.total_tugas_completed)"
        sub="Total tugas terinput & dinilai" 
        :icon="ClipboardList" 
        illustration="open_book" 
        variant="violet" 
      />

      <StatCard 
        label="Nilai Ujian Rata-Rata" 
        :value="formatNumber(globalStats.avg_ujian_all)"
        sub="Rata-rata ujian harian, UTS & UAS" 
        :icon="BookOpen" 
        illustration="protractor" 
        variant="emerald" 
      />

      <StatCard 
        label="Nilai Tugas Rata-Rata" 
        :value="formatNumber(globalStats.avg_tugas_all)"
        sub="Rata-rata seluruh nilai tugas" 
        :icon="ClipboardList" 
        illustration="graded_paper" 
        variant="amber" 
      />
    </div>

    <!-- Header: Mata Pelajaran + Filters -->
    <div class="flex flex-wrap items-center justify-between gap-4 glass-ui p-4 rounded-2xl border border-white/10 shadow-sm">
      <div class="flex items-center gap-3">
        <h2 class="text-lg font-bold">Mata Pelajaran</h2>
      </div>
      <div class="flex flex-wrap gap-3">
        <Select v-model="selectedClassroomId">
          <SelectTrigger class="w-[240px] bg-background/50 backdrop-blur-sm">
            <SelectValue placeholder="Pilih Kelas & Semester" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem 
              v-for="c in classrooms" 
              :key="c.classroom_id" 
              :value="String(c.classroom_id)"
            >
              Kelas {{ c.classroom_name }} ({{ c.academic_year_name }} - {{ c.semester_label }})
            </SelectItem>
          </SelectContent>
        </Select>

        <Select v-model="selectedSubjectId">
          <SelectTrigger class="w-[200px] bg-background/50 backdrop-blur-sm">
            <SelectValue placeholder="Pilih Mata Pelajaran" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem 
              v-for="sub in subjects" 
              :key="sub.id" 
              :value="String(sub.id)"
            >
              {{ sub.name }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>

    <!-- Show main layout only if classroom and subject are selected -->
    <div v-if="selectedClassroomId && selectedSubjectId" class="space-y-6">
      
      <!-- UTS & UAS Score Cards -->
      <div class="grid gap-6 md:grid-cols-2">
        <Card class="glass-ui p-5 rounded-2xl border border-white/10 shadow-md relative overflow-hidden flex flex-col items-center text-center">
          <div class="absolute top-2 right-2">
            <Badge :variant="overview.uts !== null ? 'green' : 'gray'" class="font-bold text-[10px]">
              {{ overview.uts !== null ? 'Tuntas' : 'Menunggu' }}
            </Badge>
          </div>
          <span class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">📝 Nilai UTS</span>
          <div class="text-4xl font-extrabold text-foreground tracking-tight my-1">
            {{ overview.uts !== null ? formatNumber(overview.uts) : 'Belum Ada Nilai' }}
          </div>
          <span class="text-[10px] text-muted-foreground mt-1">{{ currentSubjectLabel }} &bull; Semester {{ currentClassroomLabel.includes('Ganjil') ? 'Ganjil' : 'Genap' }}</span>
        </Card>

        <Card class="glass-ui p-5 rounded-2xl border border-white/10 shadow-md relative overflow-hidden flex flex-col items-center text-center">
          <div class="absolute top-2 right-2">
            <Badge :variant="overview.uas !== null ? 'green' : 'gray'" class="font-bold text-[10px]">
              {{ overview.uas !== null ? 'Tuntas' : 'Menunggu' }}
            </Badge>
          </div>
          <span class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">📝 Nilai UAS</span>
          <div class="text-4xl font-extrabold text-foreground tracking-tight my-1">
            {{ overview.uas !== null ? formatNumber(overview.uas) : 'Belum Ada Nilai' }}
          </div>
          <span class="text-[10px] text-muted-foreground mt-1">{{ currentSubjectLabel }} &bull; Semester {{ currentClassroomLabel.includes('Ganjil') ? 'Ganjil' : 'Genap' }}</span>
        </Card>
      </div>

      <!-- Two Column Layout -->
      <div class="grid gap-6 lg:grid-cols-2">
        <!-- Left: Materi Pelajaran -->
        <AppCard header-class="pb-3" content-class="space-y-3">
          <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3 w-full">
              <div class="flex items-center gap-3">
                <div class="rounded-lg bg-muted p-2">
                  <FolderOpen class="size-5 text-muted-foreground" />
                </div>
                <div>
                  <div class="font-semibold text-foreground text-sm sm:text-base">{{ currentSubjectLabel }}</div>
                  <div class="text-xs text-muted-foreground">Unduh materi & nilai terkait</div>
                </div>
              </div>
              <Select v-slot="selectedMaterialId" v-model="selectedMaterialId">
                <SelectTrigger class="w-[200px] h-9 bg-background/40">
                  <SelectValue placeholder="Pilih Materi Pelajaran" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem 
                    v-for="m in overview.materials" 
                    :key="m.id" 
                    :value="String(m.id)"
                  >
                    {{ m.title }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </template>

          <!-- List Items (Materials & Scores) -->
          <div v-if="paginatedMateriItems.length > 0" class="space-y-2.5 min-h-[180px]">
            <div 
              v-for="item in paginatedMateriItems" 
              :key="item.id + '-' + item.type"
              class="flex items-center justify-between p-3 rounded-xl border border-border/40 bg-background/20 backdrop-blur-sm"
            >
              <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-muted/60 shrink-0">
                  <Play v-if="item.type === 'file'" class="size-4 text-blue-500" />
                  <FileText v-else class="size-4 text-amber-500" />
                </div>
                <div class="text-left min-w-0">
                  <div class="text-xs sm:text-sm font-semibold text-foreground truncate max-w-[240px] sm:max-w-[320px]">
                    {{ item.title }}
                  </div>
                  <div class="text-[10px] text-muted-foreground">
                    <span v-if="item.type === 'file'">
                      Berkas &bull; {{ formatSize(item.file_size) }}
                    </span>
                    <span v-else>
                      {{ item.category === 'tugas' ? 'Nilai Tugas' : 'Nilai Ujian' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Action: Download or Score Badge -->
              <div>
                <button
                  v-if="item.type === 'file'"
                  class="flex items-center gap-1 text-xs font-semibold text-blue-500 hover:text-blue-600 transition-colors bg-blue-500/10 px-2.5 py-1 rounded-lg"
                  @click="handleDownload(item.id, item.file_name)"
                >
                  <Download class="size-3" /> Unduh
                </button>
                <div v-else class="text-right">
                  <span class="text-sm font-bold text-foreground bg-amber-500/10 px-2.5 py-1 rounded-lg">
                    {{ item.score !== null ? formatNumber(item.score) : '-' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="flex flex-col items-center justify-center py-12 text-center text-muted-foreground border border-dashed rounded-xl min-h-[180px]">
            <FolderOpen class="size-8 mb-2 opacity-50" />
            <p class="text-sm font-semibold">Belum Ada Materi</p>
            <p class="text-xs">Guru belum mengunggah materi pelajaran.</p>
          </div>

          <!-- Pagination Left Card -->
          <div v-if="totalMateriPages > 1" class="flex items-center justify-between pt-3 border-t border-border/40">
            <span class="text-[10px] sm:text-xs text-muted-foreground">Halaman {{ materiPage }} dari {{ totalMateriPages }}</span>
            <div class="flex gap-1.5">
              <button
                v-if="materiPage > 1"
                class="p-1.5 rounded-lg border border-border/40 hover:bg-muted/40 transition-colors"
                @click="materiPage--"
              >
                <ChevronLeft class="size-4" />
              </button>
              <button
                v-if="materiPage < totalMateriPages"
                class="p-1.5 rounded-lg border border-border/40 hover:bg-muted/40 transition-colors"
                @click="materiPage++"
              >
                <ChevronRight class="size-4" />
              </button>
            </div>
          </div>
        </AppCard>

        <!-- Right: Aktivitas Mata Pelajaran (Dummy) -->
        <AppCard
          :title="`Aktivitas Pelajaran ${currentSubjectLabel}`"
          description="Detail aktivitas atau kegiatan mata pelajaran dalam satu semester"
          header-class="pb-3"
          content-class="space-y-3"
        >
          <div class="space-y-2.5 min-h-[180px]">
            <ActivityCard
              v-for="(item, i) in paginatedAktivitasItems"
              :key="i"
              :date="item.date"
              :month="item.month"
              :title="item.title"
              :description="item.description"
              :trailing-icon="item.icon"
              :variant="item.variant"
            />
          </div>

          <!-- Pagination Right Card -->
          <div v-if="totalAktivitasPages > 1" class="flex items-center justify-between pt-3 border-t border-border/40">
            <span class="text-[10px] sm:text-xs text-muted-foreground">Halaman {{ aktivitasPage }} dari {{ totalAktivitasPages }}</span>
            <div class="flex gap-1.5">
              <button
                v-if="aktivitasPage > 1"
                class="p-1.5 rounded-lg border border-border/40 hover:bg-muted/40 transition-colors"
                @click="aktivitasPage--"
              >
                <ChevronLeft class="size-4" />
              </button>
              <button
                v-if="aktivitasPage < totalAktivitasPages"
                class="p-1.5 rounded-lg border border-border/40 hover:bg-muted/40 transition-colors"
                @click="aktivitasPage++"
              >
                <ChevronRight class="size-4" />
              </button>
            </div>
          </div>
        </AppCard>
      </div>

      <!-- Grade Detail Section: Detail Nilai + Rata-rata + Kehadiran -->
      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left: Detail Nilai dengan Progress Bar (Real data, max 2 items + '...') -->
        <GradeDetailCard
          :title="`Detail Nilai ${currentSubjectLabel}`"
          :icon="BookOpen"
          :categories="formattedCategories"
        />

        <!-- Center: Rata-rata Nilai (Circle biru/purple) -->
        <ScoreCircleCard
          :title="averageScoreData.title"
          :score="averageScoreData.score"
          :label="averageScoreData.label"
          :description="averageScoreData.description"
          :percentage="averageScoreData.percentage"
          :variant="averageScoreData.variant"
        />

        <!-- Right: Persentase Kehadiran (Circle hijau - Dummy) -->
        <ScoreCircleCard
          :title="attendanceData.title"
          :score="attendanceData.score"
          :label="attendanceData.label"
          :description="attendanceData.description"
          :percentage="attendanceData.percentage"
          :variant="attendanceData.variant"
        />
      </div>
    </div>

    <!-- Empty State if filters are empty -->
    <div v-else class="flex flex-col items-center justify-center py-20 text-center text-muted-foreground border border-dashed rounded-2xl min-h-[300px] glass-ui">
      <BookOpen class="size-12 mb-3 opacity-40 text-muted-foreground" />
      <h3 class="text-lg font-bold">Pilih Kelas, Semester dan Mata Pelajaran</h3>
      <p class="text-sm max-w-md">Silakan gunakan filter di atas untuk melihat materi pelajaran, aktivitas harian, serta pencapaian nilai UTS & UAS Anda.</p>
    </div>
  </div>
</template>
