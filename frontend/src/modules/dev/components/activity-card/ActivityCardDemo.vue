<script setup>
import ActivityCard from '@/components/activity-card/ActivityCard.vue'
import { BookOpen, Download, ClipboardList, CheckCircle, ArrowRight } from 'lucide-vue-next'
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
    <!-- Date Mode Demos -->
    <div class="space-y-3">
      <h4 class="text-sm font-bold text-muted-foreground mb-2">Date Mode (Leading berupa tanggal)</h4>
      
      <ActivityCard
        :date="18"
        month="Jun"
        title="Ujian Akhir Semester"
        description="Jadwal Matematika & Fisika"
        :trailing-icon="ClipboardList"
        variant="blue"
      />

      <ActivityCard
        :date="24"
        month="Jun"
        title="Rapat Yayasan & Wali Murid"
        description="Pembagian Raport Semester Genap"
        :trailing-icon="ArrowRight"
        variant="purple"
      />

      <ActivityCard
        :date="30"
        month="Jun"
        title="Libur Semester"
        description="Mulai Tanggal 30 Juni - 14 Juli"
        variant="amber"
      />
    </div>

    <!-- Icon Mode Demos -->
    <div class="space-y-3">
      <h4 class="text-sm font-bold text-muted-foreground mb-2">Icon Mode (Leading berupa kotak icon)</h4>

      <ActivityCard
        :leading-icon="BookOpen"
        title="Bab 3 : Geometri Dimensi Tiga"
        description="Materi dibagikan oleh Ibu Endang"
        :trailing-icon="Download"
        variant="green"
      />

      <ActivityCard
        :leading-icon="CheckCircle"
        title="SPP Mei Lunas"
        description="Diverifikasi otomatis oleh Tata Usaha"
        variant="green"
      />

      <ActivityCard
        :leading-icon="ClipboardList"
        title="Tugas Fisika Terkumpul"
        description="10 dari 32 Siswa Belum Mengumpulkan"
        :trailing-icon="ArrowRight"
        variant="blue"
      />
    </div>
  </div>
</template>
