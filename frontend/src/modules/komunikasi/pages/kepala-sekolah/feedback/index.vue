<script setup>
import { ref, computed } from 'vue'
import { toast } from 'vue-sonner'
import { useFeedbackStore } from '@/stores/feedbackStore'
import FeedbackTable from '../../../components/FeedbackTable.vue'
import FeedbackDetailModal from '../../../components/FeedbackDetailModal.vue'
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { glassFade } from '@/config/motion'
import { 
  MessageSquare, 
  BookOpen, 
  Building2, 
  HeartHandshake, 
  Wallet,
  ShieldCheck
} from 'lucide-vue-next'

const feedbackStore = useFeedbackStore()

const feedbacks = computed(() => feedbackStore.items)
const isDetailOpen = ref(false)
const selectedFeedback = ref(null)

// --- Computed Stats ---
const totalCount = computed(() => feedbacks.value.length)
const akademikCount = computed(() => feedbacks.value.filter(item => item.kategori === 'AKADEMIK').length)
const fasilitasCount = computed(() => feedbacks.value.filter(item => item.kategori === 'FASILITAS').length)
const pelayananCount = computed(() => feedbacks.value.filter(item => item.kategori === 'PELAYANAN').length)
const keuanganCount = computed(() => feedbacks.value.filter(item => item.kategori === 'KEUANGAN').length)

// --- Detail View Action ---
function openDetail(item) {
  selectedFeedback.value = item
  isDetailOpen.value = true
}

// --- Export Handlers ---
function handleExportExcel() {
  toast.success('Laporan Keluhan Anonim berhasil diekspor ke Excel!', {
    description: 'File spreadsheet sedang diunduh secara otomatis.'
  })
}

function handleExportPdf() {
  toast.success('Laporan Keluhan Anonim berhasil diekspor ke PDF!', {
    description: 'Dokumen cetak PDF sedang disiapkan untuk diunduh.'
  })
}
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 p-1"
  >
    <div class="space-y-6">
      <!-- Header Title -->
      <PageHeader
        title="Papan Evaluasi & Keluhan Anonim"
        description="Tampungan keluhan, masukan, atau saran berformat rahasia/anonim yang dikirimkan oleh wali murid sebagai bahan evaluasi sekolah."
      />
      
      <!-- Security Badge -->
      <div class="flex items-center gap-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 px-3.5 py-1.5 rounded-full text-xs font-semibold self-start shadow-xs w-max">
        <ShieldCheck class="size-4 shrink-0" />
        <span>Mode Keamanan & Anonimitas Aktif</span>
      </div>

      <!-- Summary Statistics Grid -->
      <StatCardGrid cols="5">
        <StatCard 
          label="Total Keluhan/Saran"
          :value="totalCount"
          sub="Masukan Masuk"
          :icon="MessageSquare"
          variant="primary"
        />
        <StatCard 
          label="Akademik"
          :value="akademikCount"
          sub="Kurikulum & Pembelajaran"
          :icon="BookOpen"
          variant="blue"
        />
        <StatCard 
          label="Fasilitas"
          :value="fasilitasCount"
          sub="Sarana & Prasarana"
          :icon="Building2"
          variant="emerald"
        />
        <StatCard 
          label="Pelayanan"
          :value="pelayananCount"
          sub="Administrasi & TU"
          :icon="HeartHandshake"
          variant="amber"
        />
        <StatCard 
          label="Keuangan"
          :value="keuanganCount"
          sub="SPP & Biaya Sekolah"
          :icon="Wallet"
          variant="violet"
        />
      </StatCardGrid>

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
  </div>
</template>
