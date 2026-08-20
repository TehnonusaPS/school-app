<script setup>
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Trash2 } from 'lucide-vue-next'

const props = defineProps({
  open: Boolean,
  item: Object
})

const emit = defineEmits(['update:open', 'confirmed'])

function handleConfirm() {
  emit('confirmed', props.item)
  emit('update:open', false)
}
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-[400px] rounded-2xl p-6 text-left">
      <DialogHeader>
        <DialogTitle class="text-sm font-bold text-foreground flex items-center gap-2">
          <div class="p-2 rounded-xl bg-rose-500/10 text-rose-500 shrink-0">
            <Trash2 class="size-4 animate-bounce" />
          </div>
          Hapus Tahun Ajaran
        </DialogTitle>
        <DialogDescription class="text-xs text-muted-foreground leading-relaxed mt-2 text-left">
          Apakah Anda yakin ingin menghapus tahun ajaran <strong class="text-foreground">"{{ item?.tahun }}"</strong>? Seluruh periode semester ganjil & genap terkait akan terhapus. Tindakan ini tidak dapat dibatalkan.
        </DialogDescription>
      </DialogHeader>

      <DialogFooter class="flex flex-row items-center justify-end gap-2 pt-4 border-t border-border/60 mt-4">
        <Button
          type="button"
          variant="ghost"
          class="text-xs font-bold rounded-xl cursor-pointer h-9 px-4"
          @click="emit('update:open', false)"
        >
          Batal
        </Button>
        <Button
          type="button"
          class="text-xs font-bold rounded-xl cursor-pointer bg-rose-500 text-white hover:bg-rose-600 border-none shadow-none h-9 px-5"
          @click="handleConfirm"
        >
          Ya, Hapus
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
