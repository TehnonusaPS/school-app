<script setup>
import { ref } from 'vue'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import FormTextArea from '@/components/forms/FormTextArea.vue'
import { Check, X, AlertTriangle } from 'lucide-vue-next'

const props = defineProps({
  open: {
    type: Boolean,
    default: false
  },
  mode: {
    type: String,
    default: 'approve' // 'approve' | 'reject'
  },
  academicYearName: {
    type: String,
    default: ''
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:open', 'confirm', 'cancel'])

const rejectReason = ref('')
const errorMsg = ref('')

function handleConfirm() {
  if (props.mode === 'reject') {
    if (!rejectReason.value.trim()) {
      errorMsg.value = 'Alasan penolakan wajib diisi.'
      return
    }
  }
  errorMsg.value = ''
  emit('confirm', {
    mode: props.mode,
    reason: rejectReason.value.trim()
  })
}

function handleClose() {
  rejectReason.value = ''
  errorMsg.value = ''
  emit('update:open', false)
  emit('cancel')
}
</script>

<template>
  <Dialog :open="open" @update:open="handleClose">
    <DialogContent class="sm:max-w-md bg-card dark:bg-zinc-900 border border-border dark:border-zinc-800">
      <DialogHeader>
        <div class="flex items-center gap-3">
          <div
            class="h-10 w-10 rounded-full flex items-center justify-center shrink-0"
            :class="mode === 'approve' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-destructive/10 text-destructive'"
          >
            <Check v-if="mode === 'approve'" class="h-5 w-5" />
            <AlertTriangle v-else class="h-5 w-5" />
          </div>
          <div>
            <DialogTitle class="text-base font-semibold text-foreground dark:text-zinc-100">
              {{ mode === 'approve' ? 'Setujui Kalender Akademik' : 'Tolak Kalender Akademik' }}
            </DialogTitle>
            <DialogDescription class="text-xs text-muted-foreground dark:text-zinc-400 mt-0.5">
              Tahun Ajaran {{ academicYearName }}
            </DialogDescription>
          </div>
        </div>
      </DialogHeader>

      <div class="py-3">
        <p v-if="mode === 'approve'" class="text-sm text-foreground dark:text-zinc-300">
          Apakah Anda yakin ingin menyetujui Kalender Akademik ini? Setelah disetujui, kalender akan terkunci dan dipublikasikan ke seluruh guru, siswa, dan orang tua.
        </p>

        <div v-else class="space-y-3">
          <p class="text-sm text-foreground dark:text-zinc-300">
            Berikan alasan penolakan agar Admin Sekolah dapat melakukan perbaikan draft agenda kalender:
          </p>
          <FormTextArea
            v-model="rejectReason"
            placeholder="Contoh: Tanggal pelaksanaan Ujian Tengah Semester terlalu berdekatan dengan libur nasional..."
            :rows="3"
            :error="errorMsg"
          />
        </div>
      </div>

      <DialogFooter class="gap-2 sm:gap-0">
        <Button variant="outline" type="button" @click="handleClose" :disabled="isLoading">
          Batal
        </Button>
        <Button
          type="button"
          :variant="mode === 'approve' ? 'default' : 'destructive'"
          :disabled="isLoading"
          @click="handleConfirm"
          class="gap-1.5"
        >
          <Check v-if="mode === 'approve'" class="h-4 w-4" />
          <X v-else class="h-4 w-4" />
          {{ isLoading ? 'Memproses...' : (mode === 'approve' ? 'Ya, Setujui' : 'Tolak Kalender') }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
