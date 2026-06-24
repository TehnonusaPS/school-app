<script setup>
import { ref } from 'vue'
import { FolderOpen, Book } from 'lucide-vue-next'
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
import { siswaStatsData } from '../data/mockSiswaStatCards'
import { mockAktivitas } from '../data/mockSiswaAktivitas'
import { mockMateriList } from '../data/mockSiswaMateri'
import {
  gradeDetailIcon,
  gradeDetailCategories,
  averageScoreData,
  attendanceData
} from '../data/mockSiswaNilai'
import { formatNumber, formatDelta } from '@/utils/formatNumber'

const selectedMapel = ref('matematika')
const selectedSemester = ref('semester-1')
const selectedMateri = ref('')
</script>

<template>
  <div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-5">
      <StatCard label="Nilai Asal-asalan" :value="formatNumber(siswaStatsData.nilaiAsal.total)"
        sub="naik dari semester sebelumnya" :trend="`${formatDelta(siswaStatsData.nilaiAsal.growthPercent)}%`"
        :trendDirection="siswaStatsData.nilaiAsal.trendDirection" :icon="Book" illustration="apple" variant="blue" />

      <StatCard label="Jumlah Tugas Terselesaikan" :value="formatNumber(siswaStatsData.tugas.total)"
        sub="turun dari semester sebelumnya" :trend="`${formatDelta(siswaStatsData.tugas.growthPercent)}%`"
        :trendDirection="siswaStatsData.tugas.trendDirection" :icon="Book" illustration="open_book" variant="violet" />

      <StatCard label="Nilai Ujian Matematika" :value="formatNumber(siswaStatsData.nilaiMtk.total)"
        sub="naik dari semester sebelumnya" :trend="`${formatDelta(siswaStatsData.nilaiMtk.growthPercent)}%`"
        :trendDirection="siswaStatsData.nilaiMtk.trendDirection" :icon="Book" illustration="protractor" variant="emerald" />

      <StatCard label="Jumlah Ujian Terselesaikan" :value="formatNumber(siswaStatsData.ujian.total)"
        sub="turun dari semester sebelumnya" :trend="`${formatDelta(siswaStatsData.ujian.growthPercent)}%`"
        :trendDirection="siswaStatsData.ujian.trendDirection" :icon="Book" illustration="graded_paper" variant="amber" />

      <StatCard label="Jumlah Kuis Terselesaikan" :value="formatNumber(siswaStatsData.kuis.total)"
        sub="turun dari semester sebelumnya" :trend="`${formatDelta(siswaStatsData.kuis.growthPercent)}%`"
        :trendDirection="siswaStatsData.kuis.trendDirection" :icon="Book" illustration="star" variant="primary" />
    </div>

    <!-- Header: Mata Pelajaran + Filters -->
    <div class="flex flex-wrap items-center gap-4">
      <h2 class="text-lg font-semibold">Mata Pelajaran</h2>
      <div class="flex gap-3">
        <Select v-model="selectedMapel">
          <SelectTrigger class="w-[180px]">
            <SelectValue placeholder="Pilih Mata Pelajaran" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="matematika">Matematika</SelectItem>
            <SelectItem value="bahasa-indonesia">Bahasa Indonesia</SelectItem>
            <SelectItem value="ipa">IPA</SelectItem>
          </SelectContent>
        </Select>

        <Select v-model="selectedSemester">
          <SelectTrigger class="w-[160px]">
            <SelectValue placeholder="Pilih Semester" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="semester-1">Semester 1</SelectItem>
            <SelectItem value="semester-2">Semester 2</SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid gap-6 lg:grid-cols-2">
      <!-- Left: Materi Pelajaran -->
      <AppCard header-class="pb-3" content-class="space-y-3">
        <template #header>
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="rounded-lg bg-muted p-2">
                <FolderOpen class="size-5 text-muted-foreground" />
              </div>
              <div>
                <div class="font-semibold">Matematika</div>
                <div class="text-xs text-muted-foreground">Lihat Detail Pelajaran</div>
              </div>
            </div>
            <Select v-model="selectedMateri">
              <SelectTrigger class="w-[200px]">
                <SelectValue placeholder="Pilih Materi Pelajaran" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="bab-1">Bab 1 - Aljabar</SelectItem>
                <SelectItem value="bab-2">Bab 2 - Geometri</SelectItem>
                <SelectItem value="bab-3">Bab 3 - Statistika</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </template>

        <ActivityCard
          v-for="(materi, i) in mockMateriList"
          :key="i"
          :leading-icon="materi.leadingIcon"
          :title="materi.title"
          :description="materi.description"
          :trailing-icon="materi.trailingIcon"
          :variant="materi.variant"
        />
      </AppCard>

      <!-- Right: Aktivitas Mata Pelajaran -->
      <AppCard
        title="Aktivitas Mata Pelajaran Matematika"
        description="Detail aktivitas atau kegiatan mata pelajaran dalam satu semester"
        header-class="pb-3"
        content-class="space-y-3"
      >
        <ActivityCard
          v-for="(item, i) in mockAktivitas"
          :key="i"
          :date="item.date"
          :month="item.month"
          :title="item.title"
          :description="item.description"
          :trailing-icon="item.icon"
          :variant="item.variant"
        />
      </AppCard>
    </div>

    <!-- Grade Detail Section: Detail Nilai + Rata-rata + Kehadiran -->
    <div class="grid gap-6 lg:grid-cols-3">
      <!-- Left: Detail Nilai dengan Progress Bar -->
      <GradeDetailCard
        title="Detail Nilai Mata Pelajaran Matematika"
        :icon="gradeDetailIcon"
        :categories="gradeDetailCategories"
      />

      <!-- Center: Rata-rata Nilai (Circle biru) -->
      <ScoreCircleCard
        :title="averageScoreData.title"
        :score="averageScoreData.score"
        :label="averageScoreData.label"
        :description="averageScoreData.description"
        :percentage="averageScoreData.percentage"
        :variant="averageScoreData.variant"
      />

      <!-- Right: Persentase Kehadiran (Circle hijau) -->
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
</template>
