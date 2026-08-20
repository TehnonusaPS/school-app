<script setup>
import { Save } from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'

defineProps({
  canManageExams: { type: Boolean, default: false },
  selectedEvent: { type: Object, default: null },
  totalAssignedSubjects: { type: Number, default: 0 },
  saving: { type: Boolean, default: false }
})

const emit = defineEmits(['confirm-save'])
</script>

<template>
  <div 
    v-if="canManageExams && selectedEvent" 
    class="print:hidden fixed bottom-6 left-4 right-4 md:left-72 md:right-8 z-40 bg-slate-900/95 backdrop-blur-md border border-slate-700/80 rounded-2xl p-4 shadow-2xl flex flex-wrap items-center justify-between gap-4 text-white animate-in slide-in-from-bottom-4 duration-300"
  >
    <div class="flex items-center gap-3">
      <div class="size-10 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center font-bold">
        <Save class="size-5" />
      </div>
      <div>
        <div class="flex items-center gap-2">
          <h4 class="font-bold text-sm text-white">Status Jadwal Ujian</h4>
          <Badge variant="outline" class="bg-amber-400/10 text-amber-300 border-amber-400/30 text-[10px] px-2 py-0.5">
            Draft Mode
          </Badge>
        </div>
        <p class="text-xs text-slate-300">
          Event: <span class="font-bold text-white">{{ selectedEvent?.title }}</span> | {{ totalAssignedSubjects }} Mapel Terisi
        </p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <Button 
        @click="emit('confirm-save')" 
        :disabled="saving"
        class="bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold px-6 py-2.5 rounded-xl shadow-md transition-all active:scale-95"
      >
        <Save class="size-4 mr-2" />
        <span>{{ saving ? 'Menyimpan...' : 'Simpan Jadwal Ujian' }}</span>
      </Button>
    </div>
  </div>
</template>
