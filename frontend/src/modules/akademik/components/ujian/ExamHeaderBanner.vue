<script setup>
import { Sparkles, Printer } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

defineProps({
  selectedAcademicYear: { type: Object, default: null },
  selectedEvent: { type: Object, default: null },
  formatDateIndo: { type: Function, required: true }
})

const emit = defineEmits(['print'])
</script>

<template>
  <div>
    <!-- PRINT ONLY HEADER (Visible only on print preview) -->
    <div class="hidden print:block text-center space-y-2 mb-6 pb-4 border-b">
      <h1 class="text-xl font-bold uppercase tracking-wider text-black">JADWAL UJIAN DETAIL SEKOLAH</h1>
      <p v-if="selectedAcademicYear" class="text-sm font-semibold text-gray-700">
        Tahun Ajaran {{ selectedAcademicYear.name }}
      </p>
      <p v-if="selectedEvent" class="text-sm font-bold text-gray-900">
        {{ selectedEvent.title }} ({{ formatDateIndo(selectedEvent.start_date) }} - {{ formatDateIndo(selectedEvent.end_date) }})
      </p>
    </div>

    <!-- Header Banner (Screen Only) -->
    <div class="print:hidden relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 text-white p-6 md:p-8 shadow-xl border border-white/10">
      <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-indigo-200">
            <Sparkles class="size-3.5 text-amber-300" />
            <span>Manajemen Ujian Terpusat</span>
          </div>
          <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
            Jadwal Ujian Detail (UTS / UAS)
          </h1>
          <p class="text-sm text-indigo-100/80 max-w-2xl leading-relaxed">
            Susun dan pantau jadwal pelaksanaan mata pelajaran per tanggal dan sesi ujian sekolah untuk setiap angkatan secara akurat.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <Button 
            @click="emit('print')"
            variant="outline"
            class="bg-white/10 hover:bg-white/20 backdrop-blur-md border-white/20 text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all"
          >
            <Printer class="size-4" />
            <span>Cetak Jadwal</span>
          </Button>
        </div>
      </div>
      
      <!-- Background Accents -->
      <div class="absolute -right-12 -bottom-12 size-64 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none" />
      <div class="absolute right-1/3 -top-12 size-48 rounded-full bg-purple-500/20 blur-2xl pointer-events-none" />
    </div>
  </div>
</template>
