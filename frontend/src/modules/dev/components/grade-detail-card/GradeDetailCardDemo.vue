<script setup>
import GradeDetailCard from '@/components/grade-detail-card/GradeDetailCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { BookOpen, Award } from 'lucide-vue-next'

const matematikaGradeDetails = [
  {
    label: 'Tugas Harian',
    weight: 20,
    score: 85,
    maxScore: 100,
    items: [
      { label: 'Tugas 1: Aljabar Linier', value: '88/100' },
      { label: 'Tugas 2: Matriks', value: '82/100' }
    ]
  },
  {
    label: 'Kuis & Praktikum',
    weight: 30,
    score: 90,
    maxScore: 100,
    items: [
      { label: 'Kuis 1: Persamaan Kuadrat', value: '95/100' },
      { label: 'Kuis 2: Geometri Ruang', value: '85/100' }
    ]
  },
  {
    label: 'Ujian Tengah Semester (UTS)',
    weight: 25,
    score: 78,
    maxScore: 100,
    items: [
      { label: 'Ujian Tertulis UTS', value: '78/100' }
    ]
  },
  {
    label: 'Ujian Akhir Semester (UAS)',
    weight: 25,
    score: 88,
    maxScore: 100,
    items: [
      { label: 'Ujian Tertulis UAS', value: '88/100' }
    ]
  }
]

const fisikaGradeDetails = [
  {
    label: 'Tugas & Laporan Praktikum',
    weight: 30,
    score: 95,
    maxScore: 100,
    items: [
      { label: 'Laporan 1: Hukum Newton', value: '98/100' },
      { label: 'Laporan 2: Optik Cermin', value: '92/100' }
    ]
  },
  {
    label: 'Ujian Tengah Semester',
    weight: 35,
    score: 82,
    maxScore: 100,
    items: [
      { label: 'UTS Fisika Dasar', value: '82/100' }
    ]
  },
  {
    label: 'Ujian Akhir Semester',
    weight: 35,
    score: 80,
    maxScore: 100,
    items: [
      { label: 'UAS Teori & Praktek', value: '80/100' }
    ]
  }
]
</script>

<template>
  <StatCardGrid cols="2">
    <!-- Matematika Grade Details -->
    <GradeDetailCard
      title="Detail Nilai - Matematika Wajib"
      :icon="BookOpen"
      :categories="matematikaGradeDetails"
    />

    <!-- Fisika Grade Details -->
    <GradeDetailCard
      title="Detail Nilai - Fisika Peminatan"
      :icon="Award"
      :categories="fisikaGradeDetails"
    />
  </StatCardGrid>
</template>
