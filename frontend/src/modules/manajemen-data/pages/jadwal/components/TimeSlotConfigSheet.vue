<script setup>
import { ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Switch } from '@/components/ui/switch'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription } from '@/components/ui/sheet'
import { Plus, Trash2, Clock } from 'lucide-vue-next'
import { getTimeSlots, saveTimeSlotsBulk } from '@/services/scheduleService'

const props = defineProps({
  open: Boolean
})

const emit = defineEmits(['update:open', 'saved'])

const slots = ref([])
const isLoading = ref(false)

async function loadTimeSlots() {
  isLoading.value = true
  try {
    const res = await getTimeSlots()
    slots.value = res.data.map(slot => ({
      id: slot.id,
      slot_number: slot.slot_number,
      start_time: slot.start_time.substring(0, 5),
      end_time: slot.end_time.substring(0, 5),
      is_break: !!slot.is_break,
      label: slot.label || ''
    }))
  } catch (err) {
    toast.error('Gagal memuat jam pelajaran')
  } finally {
    isLoading.value = false
  }
}

watch(() => props.open, (newVal) => {
  if (newVal) {
    loadTimeSlots()
  }
})

function addSlot() {
  const nextNum = slots.value.length > 0 ? Math.max(...slots.value.map(s => s.slot_number)) + 1 : 1
  slots.value.push({
    slot_number: nextNum,
    start_time: '07:30',
    end_time: '08:10',
    is_break: false,
    label: `Jam ${nextNum}`
  })
}

function removeSlot(index) {
  slots.value.splice(index, 1)
  // Re-number
  slots.value.forEach((slot, idx) => {
    slot.slot_number = idx + 1
    if (!slot.is_break) {
      slot.label = `Jam ${idx + 1}`
    }
  })
}

function toggleBreak(slot) {
  if (slot.is_break) {
    if (!slot.label || slot.label.startsWith('Jam ')) {
      slot.label = 'Istirahat'
    }
  } else {
    if (slot.label === 'Istirahat') {
      slot.label = `Jam ${slot.slot_number}`
    }
  }
}

async function handleSave() {
  // Validate time overlaps or empty times
  for (let slot of slots.value) {
    if (!slot.start_time || !slot.end_time) {
      toast.error('Gagal Menyimpan', { description: 'Jam mulai dan selesai harus diisi.' })
      return
    }
  }

  // Sort slots by slot_number
  slots.value.sort((a, b) => a.slot_number - b.slot_number)

  try {
    const payload = slots.value.map(slot => ({
      slot_number: slot.slot_number,
      start_time: slot.start_time + ':00',
      end_time: slot.end_time + ':00',
      is_break: slot.is_break,
      label: slot.label
    }))

    await saveTimeSlotsBulk(payload)
    toast.success('Berhasil Disimpan', { description: 'Jam pelajaran sekolah telah diperbarui.' })
    emit('update:open', false)
    emit('saved')
  } catch (err) {
    toast.error('Gagal menyimpan jam pelajaran')
  }
}
</script>

<template>
  <Sheet :open="open" @update:open="emit('update:open', $event)">
    <SheetContent :show-close-button="false" class="sm:max-w-[550px] flex flex-col h-full gap-2">
      <SheetHeader class="border-b border-border pb-3 text-left">
        <SheetTitle class="text-base font-bold text-foreground">
          Konfigurasi Jam Pelajaran
        </SheetTitle>
        <SheetDescription class="text-xs text-muted-foreground">
          Atur jam masuk, durasi, dan waktu istirahat sekolah yang berlaku untuk pembuatan jadwal.
        </SheetDescription>
      </SheetHeader>

      <div class="flex-1 overflow-y-auto py-6 pr-1 space-y-4 no-scrollbar">
        <div v-if="isLoading" class="text-center text-sm py-8 text-muted-foreground font-semibold">
          Memuat jam pelajaran...
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="(slot, idx) in slots"
            :key="idx"
            class="group/card relative bg-card border border-border/80 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-primary/30 transition-all space-y-3.5"
          >
            <!-- Card Header: Number, Label input, Switch, Delete button -->
            <div class="flex items-center justify-between gap-3">
              <!-- Left side: Badge & Label Input -->
              <div class="flex items-center gap-2.5 flex-1 min-w-0">
                <div class="size-7 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-extrabold text-xs shrink-0 select-none">
                  {{ slot.slot_number }}
                </div>
                <Input
                  type="text"
                  v-model="slot.label"
                  placeholder="Keterangan (misal: Jam 1)"
                  class="h-8 text-xs font-bold bg-transparent border-none focus-visible:ring-1 focus-visible:ring-primary/20 px-2 rounded-lg"
                />
              </div>

              <!-- Right side: Break Switch & Delete -->
              <div class="flex items-center gap-4 shrink-0">
                <!-- Istirahat Toggle (Switch) -->
                <div class="flex items-center gap-2">
                  <span class="text-[10px] font-bold text-muted-foreground select-none uppercase tracking-wider">Istirahat</span>
                  <Switch
                    :checked="slot.is_break"
                    size="sm"
                    @update:checked="val => { slot.is_break = val; toggleBreak(slot); }"
                  />
                </div>

                <!-- Delete button -->
                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  class="h-8 w-8 text-muted-foreground hover:text-rose-500 hover:bg-rose-500/10 rounded-xl cursor-pointer"
                  @click="removeSlot(idx)"
                >
                  <Trash2 class="size-4" />
                </Button>
              </div>
            </div>

            <!-- Card Body: Time Range Pickers -->
            <div class="flex items-center gap-3 bg-muted/30 p-2.5 rounded-xl border border-border/40">
              <!-- Start Time -->
              <div class="flex-1 flex items-center gap-2 px-1">
                <Clock class="size-3.5 text-muted-foreground shrink-0" />
                <div class="flex-1 space-y-0.5 text-left">
                  <div class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider">Mulai</div>
                  <input
                    type="time"
                    v-model="slot.start_time"
                    class="w-full bg-transparent border-none p-0 text-xs font-bold text-foreground focus:outline-none focus:ring-0 cursor-pointer"
                  />
                </div>
              </div>

              <!-- Separator arrow -->
              <div class="text-muted-foreground/40 font-light select-none text-xs">
                &rarr;
              </div>

              <!-- End Time -->
              <div class="flex-1 flex items-center gap-2 px-1">
                <Clock class="size-3.5 text-muted-foreground shrink-0" />
                <div class="flex-1 space-y-0.5 text-left">
                  <div class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider">Selesai</div>
                  <input
                    type="time"
                    v-model="slot.end_time"
                    class="w-full bg-transparent border-none p-0 text-xs font-bold text-foreground focus:outline-none focus:ring-0 cursor-pointer"
                  />
                </div>
              </div>
            </div>
          </div>

          <Button
            type="button"
            variant="outline"
            class="w-full h-10 rounded-xl border-dashed border-2 flex items-center justify-center gap-1.5 font-bold text-xs cursor-pointer hover:bg-primary/5 hover:border-primary/50 transition-colors"
            @click="addSlot"
          >
            <Plus class="size-4" />
            Tambah Jam / Istirahat
          </Button>
        </div>
      </div>

      <div class="border-t border-border pt-4 flex items-center justify-end gap-2 shrink-0">
        <Button
          type="button"
          variant="ghost"
          class="text-xs font-bold rounded-xl cursor-pointer"
          @click="emit('update:open', false)"
        >
          Batal
        </Button>
        <Button
          type="button"
          class="text-xs font-bold rounded-xl cursor-pointer bg-primary text-primary-foreground hover:bg-primary/90 border-none shadow-none"
          @click="handleSave"
        >
          Simpan Jam Pelajaran
        </Button>
      </div>
    </SheetContent>
  </Sheet>
</template>
