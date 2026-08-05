<script setup>
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'
import FormInput from '@/components/forms/FormInput.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'
import FormDate from '@/components/forms/FormDate.vue'
import { eventTypes } from '../../data/calendarConstants'

const props = defineProps({
  open: { type: Boolean, default: false },
  mode: { type: String, default: 'add' },
  form: { type: Object, required: true },
  formErrors: { type: Object, default: () => ({}) },
  classroomOptions: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:open', 'save', 'close'])
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-md bg-card dark:bg-zinc-900 border border-border text-left">
      <DialogHeader>
        <DialogTitle class="text-base font-bold text-foreground">
          {{ mode === 'add' ? 'Tambah Agenda Akademik' : 'Edit Agenda Akademik' }}
        </DialogTitle>
        <DialogDescription class="text-xs text-muted-foreground">
          Lengkapi rincian kegiatan, tanggal, dan tipe agenda akademik sekolah.
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="emit('save')" class="space-y-4 py-2">
        <!-- Judul Agenda -->
        <FormInput
          v-model="form.title"
          label="Judul / Nama Agenda"
          placeholder="Contoh: Penilaian Tengah Semester (PTS) Ganjil"
          :error="formErrors.title"
        />

        <!-- Tipe Agenda (Dropdown Select) -->
        <FormSelect
          v-model="form.type"
          label="Jenis Agenda"
          :options="eventTypes"
          :error="formErrors.type"
        />

        <!-- Tanggal Mulai & Selesai -->
        <div class="grid grid-cols-2 gap-4">
          <FormDate
            v-model="form.startDate"
            label="Tanggal Mulai"
            :error="formErrors.startDate"
          />
          <FormDate
            v-model="form.endDate"
            label="Tanggal Selesai"
            :error="formErrors.endDate"
          />
        </div>

        <!-- Target Kelas (Dropdown Select) -->
        <FormSelect
          v-model="form.classroom_id"
          label="Berlaku Untuk Kelas"
          :options="classroomOptions"
        />

        <!-- Keterangan -->
        <FormTextArea
          v-model="form.description"
          label="Keterangan / Catatan Tambahan"
          placeholder="Tuliskan detail atau instruksi khusus untuk agenda ini..."
          :rows="3"
        />
      </form>

      <DialogFooter class="gap-2 sm:gap-0">
        <Button variant="outline" size="sm" @click="emit('update:open', false)">
          Batal
        </Button>
        <Button size="sm" class="bg-primary text-primary-foreground font-bold" @click="emit('save')">
          {{ mode === 'add' ? 'Tambahkan ke Agenda' : 'Simpan Perubahan Agenda' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
