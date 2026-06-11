<script setup>
import ScoreCircleCard from '@/components/score-circle-card/ScoreCircleCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
</script>

<template>
  <StatCardGrid cols="4">
    <!-- Blue Variant (Math) -->
    <ScoreCircleCard
      title="Rata-rata Nilai Matematika"
      score="82,5"
      label="Cukup Baik!"
      description="Anda berada di 10% peringkat teratas kelas."
      :percentage="82.5"
      variant="blue"
    />

    <!-- Green Variant (Attendance) -->
    <ScoreCircleCard
      title="Persentase Kehadiran"
      score="96%"
      label="Sangat Disiplin!"
      description="Hanya memiliki 2 hari absen tanpa keterangan semester ini."
      :percentage="96"
      variant="green"
    />

    <!-- Purple Variant (Fisika) -->
    <ScoreCircleCard
      title="Rata-rata Nilai Fisika"
      score="91,0"
      label="Sangat Baik!"
      description="Melampaui Kriteria Ketuntasan Minimal (KKM) yaitu 75."
      :percentage="91"
      variant="purple"
    />

    <!-- Amber Variant (English) -->
    <ScoreCircleCard
      title="Nilai Bahasa Inggris"
      score="68,0"
      label="Perlu Bimbingan"
      description="Harap konsultasi dengan guru mata pelajaran untuk remedial."
      :percentage="68"
      variant="amber"
    />
  </StatCardGrid>
</template>
