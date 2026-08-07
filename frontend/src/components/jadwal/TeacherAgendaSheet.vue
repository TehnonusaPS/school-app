<script setup>
import { computed } from 'vue'
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetDescription,
  SheetFooter,
  SheetClose
} from '@/components/ui/sheet'
import { ScrollArea } from '@/components/ui/scroll-area'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import FormInput from '@/components/forms/FormInput.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'
import FormDate from '@/components/forms/FormDate.vue'
import { FileText, Calendar, Sparkles, Loader2, CheckCircle2, CalendarDays } from 'lucide-vue-next'

const props = defineProps({
  open: { type: Boolean, default: false },
  mode: { type: String, default: 'add' },
  isSaving: { type: Boolean, default: false },
  isFromCalendarCell: { type: Boolean, default: false },
  form: { type: Object, default: () => ({ type: 'tugas', date: '' }) },
  formErrors: { type: Object, default: () => ({}) },
  classroomOptions: { type: Array, default: () => [] },
  subjectOptions: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:open', 'save'])

// Dynamic date label based on type
const dateLabel = computed(() => {
  const t = props.form ? props.form.type : 'tugas'
  switch (t) {
    case 'ujian_harian':
      return 'Tanggal Pelaksanaan Ujian'
    case 'kegiatan':
      return 'Tanggal Kegiatan'
    case 'tugas':
    default:
      return 'Tanggal Deadline'
  }
})

// Format readable Indonesian date for fixed display
const formattedFixedDate = computed(() => {
  if (!props.form || !props.form.date) return ''
  try {
    const d = new Date(props.form.date)
    if (isNaN(d.getTime())) return props.form.date
    return d.toLocaleDateString('id-ID', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    })
  } catch {
    return props.form.date
  }
})

const typeOptions = [
  {
    id: 'tugas',
    label: 'Tugas',
    subLabel: 'Pengumpulan',
    icon: FileText,
    colorClass: 'border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    activeClass: 'ring-2 ring-emerald-500 border-emerald-500 bg-emerald-500/15 shadow-sm'
  },
  {
    id: 'ujian_harian',
    label: 'Ujian Harian',
    subLabel: 'Kuis Kelas',
    icon: Calendar,
    colorClass: 'border-indigo-500/40 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
    activeClass: 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-500/15 shadow-sm'
  },
  {
    id: 'kegiatan',
    label: 'Kegiatan',
    subLabel: 'Proyek Kelas',
    icon: Sparkles,
    colorClass: 'border-amber-500/40 bg-amber-500/10 text-amber-600 dark:text-amber-400',
    activeClass: 'ring-2 ring-amber-500 border-amber-500 bg-amber-500/15 shadow-sm'
  }
]
</script>

<template>
  <Sheet :open="open" @update:open="emit('update:open', $event)">
    <SheetContent
      side="right"
      class="w-full sm:max-w-md flex flex-col h-full p-0 overflow-hidden border-l border-border/60 bg-card shadow-2xl"
    >
      <!-- Sleek Glassmorphism Header -->
      <SheetHeader class="px-6 py-5 border-b border-border/40 shrink-0 space-y-1.5 bg-muted/20">
        <div class="flex items-center gap-2">
          <Badge variant="outline" class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md border-primary/30 text-primary">
            {{ mode === 'add' ? 'Agenda Baru' : 'Edit Agenda' }}
          </Badge>
        </div>

        <SheetTitle class="text-base font-extrabold text-foreground pt-0.5">
          {{ mode === 'add' ? 'Tambah Agenda Kelas' : 'Edit Agenda Kelas' }}
        </SheetTitle>
        <SheetDescription class="text-xs text-muted-foreground leading-relaxed">
          Agenda akan langsung tampil di kalender siswa & orang tua.
        </SheetDescription>
      </SheetHeader>

      <!-- Scrollable Form Body -->
      <ScrollArea class="flex-1 min-h-0">
        <form v-if="form" @submit.prevent="emit('save')" class="px-6 py-6 space-y-5 pb-8">
          <!-- 1. Visual Type Selector Cards -->
          <div class="space-y-2">
            <label class="text-xs font-extrabold text-foreground tracking-wide block">Jenis Agenda</label>
            <div class="grid grid-cols-3 gap-2.5">
              <button
                v-for="t in typeOptions"
                :key="t.id"
                type="button"
                @click="form.type = t.id"
                class="p-3 rounded-xl border text-left transition-all flex flex-col justify-between h-22 cursor-pointer relative"
                :class="form.type === t.id ? t.activeClass : 'border-border/60 bg-card hover:bg-muted/40'"
              >
                <div class="flex items-center justify-between w-full">
                  <div class="p-1.5 rounded-lg shrink-0" :class="t.colorClass">
                    <component :is="t.icon" class="size-3.5" />
                  </div>
                  <CheckCircle2 v-if="form.type === t.id" class="size-3.5 text-primary shrink-0" />
                </div>
                <div>
                  <div class="text-xs font-extrabold leading-tight text-foreground">{{ t.label }}</div>
                  <div class="text-[10px] text-muted-foreground font-medium mt-0.5">{{ t.subLabel }}</div>
                </div>
              </button>
            </div>
          </div>

          <!-- 2. Date Section (Fixed Badge vs Editable Date Picker) -->
          <div class="space-y-2">
            <!-- If opened directly from calendar cell date: Fixed Badge -->
            <div v-if="isFromCalendarCell" class="p-3.5 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-between gap-3">
              <div class="space-y-0.5">
                <span class="text-[10px] font-extrabold uppercase text-primary tracking-wider block">
                  {{ dateLabel }}
                </span>
                <span class="text-xs font-extrabold text-foreground block">
                  {{ formattedFixedDate }}
                </span>
              </div>
              <div class="size-8 rounded-lg bg-primary/15 flex items-center justify-center text-primary shrink-0">
                <CalendarDays class="size-4" />
              </div>
            </div>

            <!-- If opened from top button: Show Date Picker -->
            <FormDate
              v-else
              v-model="form.date"
              :label="dateLabel"
              :error="formErrors.date"
            />
          </div>

          <!-- 3. Judul Agenda -->
          <FormInput
            v-model="form.title"
            label="Judul / Nama Agenda"
            placeholder="Contoh: Penyerahan Tugas PR Bab 3"
            :error="formErrors.title"
          />

          <!-- 4. Target Kelas & Mapel (Grid 2 kolom) -->
          <div class="grid grid-cols-2 gap-3">
            <FormSelect
              v-model="form.classroom_id"
              label="Target Kelas"
              :options="classroomOptions"
            />
            <FormSelect
              v-model="form.subject_id"
              label="Mata Pelajaran"
              :options="subjectOptions"
            />
          </div>

          <!-- 5. Keterangan / Catatan -->
          <FormTextArea
            v-model="form.description"
            label="Deskripsi / Catatan (Opsional)"
            placeholder="Instruksi tugas, materi kuis, atau catatan penting..."
            :rows="3"
          />
        </form>
      </ScrollArea>

      <!-- Fixed Footer Actions -->
      <SheetFooter class="px-6 py-4 border-t border-border/40 shrink-0 bg-muted/20 flex flex-row items-center justify-end gap-2.5">
        <SheetClose as-child>
          <Button
            type="button"
            variant="outline"
            size="sm"
            class="rounded-xl text-xs font-bold px-4 cursor-pointer"
            :disabled="isSaving"
          >
            Batal
          </Button>
        </SheetClose>
        <Button
          type="button"
          size="sm"
          class="rounded-xl text-xs font-extrabold px-5 bg-primary text-primary-foreground hover:bg-primary/90 shadow-2xs cursor-pointer"
          :disabled="isSaving"
          @click="emit('save')"
        >
          <Loader2 v-if="isSaving" class="size-3.5 animate-spin mr-1.5" />
          <span>{{ mode === 'add' ? 'Simpan Agenda' : 'Perbarui Agenda' }}</span>
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
