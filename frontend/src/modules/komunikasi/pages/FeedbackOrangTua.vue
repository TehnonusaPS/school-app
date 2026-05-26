<script setup>
import { ref } from 'vue'
import { toast } from 'vue-sonner'
import { mockFeedbacks } from '../data/mockFeedbacks'
import FeedbackTable from '../components/FeedbackTable.vue'
import FeedbackDetailModal from '../components/FeedbackDetailModal.vue'

// --- Reactive State ---
const feedbacks = ref([...mockFeedbacks])
const isDetailOpen = ref(false)
const selectedFeedback = ref(null)

// --- Detail View Action ---
function openDetail(item) {
  selectedFeedback.value = item
  isDetailOpen.value = true
}

// --- Export Handlers ---
function handleExportExcel() {
  toast.success('Laporan Feedback berhasil diekspor ke Excel!', {
    description: 'File spreadsheet sedang diunduh secara otomatis.'
  })
}

function handleExportPdf() {
  toast.success('Laporan Feedback berhasil diekspor ke PDF!', {
    description: 'Dokumen cetak PDF sedang disiapkan untuk diunduh.'
  })
}
</script>

<template>
  <div class="space-y-6 p-1">
    <!-- Header Title -->
    <div class="flex flex-col gap-1 mb-6">
      <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-foreground to-foreground/80 bg-clip-text text-left">
        Daftar Feedback Orang Tua
      </h1>
      <p class="text-sm text-muted-foreground leading-relaxed text-left">
        Daftar masukan dan saran dari orang tua siswa untuk peningkatan kualitas sekolah
      </p>
    </div>

    <!-- Feedback Table & Filtering Component -->
    <FeedbackTable 
      :items="feedbacks" 
      @view="openDetail" 
      @export-excel="handleExportExcel"
      @export-pdf="handleExportPdf"
    />

    <!-- Feedback Details Modal Dialog -->
    <FeedbackDetailModal 
      v-model:open="isDetailOpen" 
      :feedback="selectedFeedback" 
    />
  </div>
</template>
