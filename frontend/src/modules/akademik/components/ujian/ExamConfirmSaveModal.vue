<script setup>
import { AlertCircle, AlertTriangle } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { 
  Dialog, 
  DialogContent, 
  DialogHeader, 
  DialogTitle, 
  DialogDescription, 
  DialogFooter 
} from '@/components/ui/dialog'

defineProps({
  selectedEvent: { type: Object, default: null },
  eventDates: { type: Array, required: true },
  totalAssignedSubjects: { type: Number, default: 0 },
  emptyDates: { type: Array, required: true },
  saving: { type: Boolean, default: false },
  formatDateIndo: { type: Function, required: true }
})

const open = defineModel('open', { type: Boolean, default: false })
const emit = defineEmits(['execute'])
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent class="sm:max-w-md border-border">
      <DialogHeader>
        <DialogTitle class="flex items-center gap-2 text-foreground font-extrabold">
          <AlertCircle class="size-5 text-amber-500" />
          Konfirmasi Simpan Jadwal Ujian
        </DialogTitle>
        <DialogDescription class="text-xs text-muted-foreground">
          Apakah Anda yakin ingin menyimpan seluruh susunan jadwal ujian ke sistem?
        </DialogDescription>
      </DialogHeader>

      <!-- Summary & Warnings Body -->
      <div class="space-y-3 py-2 text-sm">
        <div class="bg-muted/50 p-3.5 rounded-xl border border-border/60 space-y-1.5 text-xs">
          <div class="flex justify-between">
            <span class="text-muted-foreground">Jenis Ujian:</span>
            <span class="font-bold text-foreground">{{ selectedEvent?.title }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-muted-foreground">Total Hari Pelaksanaan:</span>
            <span class="font-bold text-foreground">{{ eventDates.length }} Hari</span>
          </div>
          <div class="flex justify-between">
            <span class="text-muted-foreground">Total Mata Pelajaran Terisi:</span>
            <span class="font-bold text-emerald-600">{{ totalAssignedSubjects }} Mapel</span>
          </div>
        </div>

        <!-- Warning Box for Empty Dates -->
        <div v-if="emptyDates.length > 0" class="bg-rose-50 border border-rose-200 text-rose-900 rounded-xl p-3.5 space-y-2">
          <div class="flex items-center gap-2 font-bold text-xs">
            <AlertTriangle class="size-4 text-rose-600 shrink-0" />
            <span>Perhatian: Ada {{ emptyDates.length }} tanggal belum diisi mapelnya!</span>
          </div>
          <ul class="text-xs space-y-1 pl-5 list-disc text-rose-800">
            <li v-for="d in emptyDates" :key="d">
              {{ formatDateIndo(d) }}
            </li>
          </ul>
          <p class="text-[11px] text-rose-700 font-medium leading-relaxed pt-1">
            Jika tanggal tersebut memang hari tenang/libur atau tidak ada ujian, Anda tetap dapat melanjutkan menyimpan.
          </p>
        </div>
      </div>

      <DialogFooter class="flex flex-row justify-end gap-2 pt-2">
        <Button variant="outline" @click="open = false" class="rounded-xl">
          Batal
        </Button>
        <Button 
          variant="default" 
          :disabled="saving" 
          class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl" 
          @click="emit('execute')"
        >
          {{ saving ? 'Menyimpan...' : 'Lanjutkan Simpan' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
