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
import { Button } from '@/components/ui/button'

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  announcement: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['update:open', 'confirm'])

const isOpen = computed({
  get: () => props.open,
  set: (val) => emit('update:open', val)
})

function onConfirm() {
  emit('confirm')
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent class="sm:max-w-[420px] rounded-2xl border-border bg-card shadow-2xl  p-6">
      <DialogHeader>
        <DialogTitle class="text-base font-extrabold text-foreground leading-none">Hapus Pengumuman?</DialogTitle>
        <DialogDescription class="text-xs text-muted-foreground mt-2 leading-relaxed">
          Apakah Anda yakin ingin menghapus pengumuman ini? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
        </DialogDescription>
      </DialogHeader>
      
      <div class="py-3 px-4 bg-destructive/5 border border-destructive/10 rounded-xl my-3" v-if="announcement">
        <p class="text-sm font-bold text-destructive leading-snug">
          {{ announcement.judul }}
        </p>
        <p class="text-[10px] text-muted-foreground mt-1 font-semibold uppercase tracking-wider">
          Sasaran: {{ announcement.sekolah }}
        </p>
      </div>
      
      <DialogFooter class="flex justify-end gap-2 border-t border-border/60 pt-4 mt-4">
        <Button 
          type="button" 
          variant="outline" 
          @click="isOpen = false" 
          class="rounded-xl h-10 px-4 text-xs font-semibold"
        >
          Batal
        </Button>
        <Button 
          type="button" 
          variant="destructive" 
          @click="onConfirm" 
          class="rounded-xl h-10 px-4 text-xs font-bold bg-destructive hover:bg-destructive/90 text-destructive-foreground active:scale-[0.98]"
        >
          Ya, Hapus
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
