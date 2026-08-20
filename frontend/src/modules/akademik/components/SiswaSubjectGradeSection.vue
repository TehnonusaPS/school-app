<script setup>
import { BookOpen } from 'lucide-vue-next'
import GradeDetailCard from '@/components/grade-detail-card/GradeDetailCard.vue'
import ScoreCircleCard from '@/components/score-circle-card/ScoreCircleCard.vue'

defineProps({
  subjectName: {
    type: String,
    default: ''
  },
  formattedCategories: {
    type: Array,
    default: () => []
  },
  averageScoreData: {
    type: Object,
    required: true
  },
  attendanceData: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div class="grid gap-6 lg:grid-cols-3">
    <!-- Left: Detail Nilai dengan Progress Bar -->
    <GradeDetailCard
      :title="`Detail Nilai ${subjectName}`"
      :icon="BookOpen"
      :categories="formattedCategories"
    />

    <!-- Center: Rata-rata Nilai -->
    <ScoreCircleCard
      :title="averageScoreData.title"
      :score="averageScoreData.score"
      :label="averageScoreData.label"
      :description="averageScoreData.description"
      :percentage="averageScoreData.percentage"
      :variant="averageScoreData.variant"
    />

    <!-- Right: Persentase Kehadiran -->
    <ScoreCircleCard
      :title="attendanceData.title"
      :score="attendanceData.score"
      :label="attendanceData.label"
      :description="attendanceData.description"
      :percentage="attendanceData.percentage"
      :variant="attendanceData.variant"
    />
  </div>
</template>
