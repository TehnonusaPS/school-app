<script setup>
import { computed } from 'vue'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle
} from '@/components/ui/dialog'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Calendar, GraduationCap, Lock, EyeOff, ShieldCheck } from 'lucide-vue-next'

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  feedback: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['update:open'])

const isOpen = computed({
  get: () => props.open,
  set: (val) => emit('update:open', val)
})

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const datePart = dateStr.includes('T') ? dateStr.split('T')[0] : dateStr.split(' ')[0]
  const [year, month, day] = datePart.split('-')
  return `${day}/${month}/${year}`
}

const categoryBadgeClass = (kategori) => {
  switch (kategori) {
    case 'AKADEMIK':
      return 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 shadow-xs'
    case 'FASILITAS':
      return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 shadow-xs'
    case 'PELAYANAN':
      return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 shadow-xs'
    case 'KEUANGAN':
      return 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 hover:bg-purple-500/20 shadow-xs'
    default:
      return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 hover:bg-slate-500/20 shadow-xs'
  }
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent class="sm:max-w-[550px] rounded-2xl border-border bg-card shadow-2xl max-h-[90vh] flex flex-col p-6 animate-fade-in">
      <DialogHeader class="shrink-0 text-left" v-if="feedback">
        <div class="flex flex-wrap items-center gap-2">
          <Badge :class="categoryBadgeClass(feedback.kategori)" class="rounded-full px-2.5 py-0.5 font-bold uppercase tracking-wider text-[9px]">
            {{ feedback.kategori }}
          </Badge>
          <span class="text-xs text-muted-foreground">•</span>
          <span class="text-xs text-foreground/80 font-bold bg-muted px-2 py-0.5 rounded border border-border/40 flex items-center gap-1.5">
            <EyeOff class="size-3 text-amber-500" />
            Siswa: Kelas {{ feedback.kelas }} (Identitas Dirahasiakan)
          </span>
        </div>
        <DialogTitle class="text-xl font-extrabold text-foreground mt-4 leading-snug text-left select-text">
          {{ feedback.judul }}
        </DialogTitle>
        <DialogDescription class="text-[11px] text-muted-foreground flex items-center gap-1.5 mt-2 font-mono font-medium">
          <Calendar class="size-3.5" />
          Diterima pada {{ formatDate(feedback.tanggal) }}
          <span class="text-muted-foreground">•</span>
          <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
            <Lock class="size-3" />
            Enkripsi Anonim Aktif
          </span>
        </DialogDescription>
      </DialogHeader>
      
      <div class="flex-1 py-4 border-t border-b border-border/60 my-3 overflow-y-auto pr-1" v-if="feedback">
        <div class="space-y-4">
          <!-- Anonymous Disclaimer Info Box -->
          <div class="flex gap-2.5 bg-emerald-500/5 text-emerald-700 dark:text-emerald-400 p-3 rounded-xl border border-emerald-500/15 text-xs">
            <ShieldCheck class="size-4 shrink-0 mt-0.5 text-emerald-500" />
            <div>
              <p class="font-bold">Informasi Penting Evaluasi:</p>
              <p class="text-muted-foreground mt-0.5 leading-relaxed">
                Detail nama wali murid dan nama siswa disamarkan oleh sistem untuk menjaga kerahasiaan pengirim sebagai bentuk keterbukaan masukan.
              </p>
            </div>
          </div>

          <div>
            <p class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider mb-1">Isi Masukan & Saran:</p>
            <p class="text-sm text-foreground/90 leading-relaxed whitespace-pre-wrap select-text p-4 bg-muted/30 border border-border/40 rounded-xl">
              {{ feedback.pesan }}
            </p>
          </div>
          
          <div class="grid grid-cols-2 gap-3 bg-muted/40 border border-border/40 p-3 rounded-xl text-xs">
            <div>
              <p class="font-bold text-muted-foreground flex items-center gap-1">
                <Lock class="size-3 text-emerald-500" />
                Pengirim:
              </p>
              <p class="text-foreground/80 mt-0.5 font-semibold">Wali Murid (Anonim - #{{ feedback.id }})</p>
            </div>
            <div>
              <p class="font-bold text-muted-foreground flex items-center gap-1">
                <EyeOff class="size-3 text-amber-500" />
                Siswa Asuhan:
              </p>
              <p class="text-foreground/80 mt-0.5 font-semibold">Kelas {{ feedback.kelas }} (Dirahasiakan)</p>
            </div>
          </div>
        </div>
      </div>
      
      <DialogFooter class="shrink-0 flex justify-end gap-2 pt-2">
        <Button type="button" variant="outline" @click="isOpen = false" class="rounded-xl h-10 px-4 font-semibold cursor-pointer">
          Tutup
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.25s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

