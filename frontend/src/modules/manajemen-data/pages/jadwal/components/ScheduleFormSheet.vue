<script setup>
import { ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription } from '@/components/ui/sheet'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { AlertCircle, Calendar, Clock, Trash2 } from 'lucide-vue-next'
import { fetchAllSubjects } from '@/services/subjectService'
import { getTeachers } from '@/services/managementService'
import { createSchedule, updateSchedule, deleteSchedule, checkTeacherConflicts } from '@/services/scheduleService'

const props = defineProps({
  open: Boolean,
  isEditMode: Boolean,
  academicYearId: [String, Number],
  classroomId: [String, Number],
  selectedSlot: Object, // { time_slot_id, day_of_week, slot_label, day_name }
  editItem: Object // full Schedule object if editing
})

const emit = defineEmits(['update:open', 'saved', 'deleted'])

const subjects = ref([])
const teachers = ref([])
const formItem = ref({
  subject_id: '',
  teacher_id: ''
})
const conflictWarning = ref('')
const isCheckingConflict = ref(false)

async function loadFormOptions() {
  try {
    const [subRes, teachRes] = await Promise.all([
      fetchAllSubjects(),
      getTeachers()
    ])
    subjects.value = subRes.data.filter(s => s.is_active)
    teachers.value = teachRes.data.filter(t => 
      t.status?.toLowerCase() === 'aktif' || 
      t.status_aktif?.toLowerCase() === 'aktif' || 
      t.status_aktif === true
    )
  } catch (err) {
    console.error('Failed to load options', err)
  }
}

watch(() => props.open, (newVal) => {
  if (newVal) {
    loadFormOptions()
    if (props.isEditMode && props.editItem) {
      formItem.value = {
        subject_id: String(props.editItem.subject_id),
        teacher_id: String(props.editItem.teacher_id)
      }
    } else {
      formItem.value = {
        subject_id: '',
        teacher_id: ''
      }
    }
    conflictWarning.value = ''
  }
})

// Check teacher conflict when teacher or slot changes
async function checkConflict() {
  if (!formItem.value.teacher_id || !props.academicYearId || !props.selectedSlot) {
    conflictWarning.value = ''
    return
  }
  isCheckingConflict.value = true
  try {
    const res = await checkTeacherConflicts({
      academic_year_id: props.academicYearId,
      teacher_id: formItem.value.teacher_id,
      time_slot_id: props.selectedSlot.time_slot_id,
      day_of_week: props.selectedSlot.day_of_week,
      exclude_id: props.isEditMode ? props.editItem?.id : undefined
    })
    
    if (res.has_conflict) {
      const class_name = res.conflict?.classroom?.name || 'lain'
      const teacher_name = teachers.value.find(t => String(t.id) === String(formItem.value.teacher_id))?.nama || 'Guru'
      conflictWarning.value = `${teacher_name} sudah mengajar di Kelas ${class_name} pada hari dan jam yang sama.`
    } else {
      conflictWarning.value = ''
    }
  } catch (err) {
    console.error(err)
  } finally {
    isCheckingConflict.value = false
  }
}

watch(() => formItem.value.teacher_id, () => {
  checkConflict()
})

async function handleSave() {
  if (!formItem.value.subject_id || !formItem.value.teacher_id) {
    toast.error('Gagal Menyimpan', { description: 'Harap lengkapi mata pelajaran dan guru pengajar.' })
    return
  }

  if (conflictWarning.value) {
    toast.error('Gagal Menyimpan', { description: 'Ada konflik jadwal untuk guru terpilih.' })
    return
  }

  const payload = {
    academic_year_id: props.academicYearId,
    classroom_id: props.classroomId,
    subject_id: formItem.value.subject_id,
    teacher_id: formItem.value.teacher_id,
    time_slot_id: props.selectedSlot.time_slot_id,
    day_of_week: props.selectedSlot.day_of_week
  }

  try {
    if (props.isEditMode && props.editItem) {
      await updateSchedule(props.editItem.id, payload)
      toast.success('Berhasil Diperbarui', { description: 'Jadwal pelajaran telah diperbarui.' })
    } else {
      await createSchedule(payload)
      toast.success('Berhasil Ditambahkan', { description: 'Jadwal pelajaran telah ditambahkan.' })
    }
    emit('update:open', false)
    emit('saved')
  } catch (err) {
    const errorMsg = err.response?.data?.message || 'Gagal menyimpan jadwal pelajaran.'
    toast.error('Gagal Menyimpan', { description: errorMsg })
  }
}

async function handleDelete() {
  if (!props.editItem) return
  try {
    await deleteSchedule(props.editItem.id)
    toast.success('Berhasil Dihapus', { description: 'Jadwal pelajaran telah dihapus.' })
    emit('update:open', false)
    emit('deleted')
  } catch (err) {
    toast.error('Gagal menghapus jadwal pelajaran')
  }
}
</script>

<template>
  <Sheet :open="open" @update:open="emit('update:open', $event)">
    <SheetContent :show-close-button="false" class="sm:max-w-[450px] flex flex-col h-full gap-2">
      <SheetHeader class="border-b border-border pb-3 text-left">
        <SheetTitle class="text-base font-bold text-foreground">
          {{ isEditMode ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Pelajaran' }}
        </SheetTitle>
        <SheetDescription class="text-xs text-muted-foreground">
          Atur mata pelajaran dan guru untuk hari dan jam yang dipilih.
        </SheetDescription>
      </SheetHeader>

      <div class="flex-1 overflow-y-auto py-6 pr-1 space-y-5 no-scrollbar">
        <!-- Target Info -->
        <div class="p-3 bg-muted/40 rounded-xl flex flex-col gap-2 border text-left">
          <div class="flex items-center gap-2 text-xs font-semibold text-muted-foreground">
            <Calendar class="size-4 text-primary shrink-0" />
            <span>Hari:</span>
            <span class="text-foreground font-bold">{{ selectedSlot?.day_name }}</span>
          </div>
          <div class="flex items-center gap-2 text-xs font-semibold text-muted-foreground">
            <Clock class="size-4 text-primary shrink-0" />
            <span>Waktu/Jam:</span>
            <span class="text-foreground font-bold">{{ selectedSlot?.slot_label }}</span>
          </div>
        </div>

        <!-- Subject Select -->
        <div class="space-y-1.5 text-left">
          <label class="text-xs font-semibold text-muted-foreground">Mata Pelajaran <span class="text-rose-500">*</span></label>
          <Select v-model="formItem.subject_id">
            <SelectTrigger class="h-10 rounded-xl bg-background border-border">
              <SelectValue placeholder="Pilih Mata Pelajaran..." />
            </SelectTrigger>
            <SelectContent class="rounded-xl">
              <SelectItem
                v-for="sub in subjects"
                :key="sub.id"
                :value="String(sub.id)"
              >
                {{ sub.name }} ({{ sub.code }})
              </SelectItem>
            </SelectContent>
          </Select>
        </div>

        <!-- Teacher Select -->
        <div class="space-y-1.5 text-left">
          <label class="text-xs font-semibold text-muted-foreground">Guru Pengajar <span class="text-rose-500">*</span></label>
          <Select v-model="formItem.teacher_id">
            <SelectTrigger class="h-10 rounded-xl bg-background border-border">
              <SelectValue placeholder="Pilih Guru..." />
            </SelectTrigger>
            <SelectContent class="rounded-xl">
              <SelectItem
                v-for="t in teachers"
                :key="t.id"
                :value="String(t.id)"
              >
                {{ t.nama }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>

        <!-- Conflict warning -->
        <Alert v-if="conflictWarning" variant="destructive" class="rounded-xl border-rose-500 bg-rose-500/10 text-rose-600">
          <AlertCircle class="h-4 w-4 text-rose-500" />
          <AlertDescription class="text-[10px] font-bold text-left ml-2">
            {{ conflictWarning }}
          </AlertDescription>
        </Alert>
      </div>

      <div class="border-t border-border pt-4 flex items-center justify-between shrink-0">
        <!-- Delete Button (Only in edit mode) -->
        <div>
          <Button
            v-if="isEditMode"
            type="button"
            variant="ghost"
            class="text-xs font-bold rounded-xl text-rose-500 hover:text-rose-600 hover:bg-rose-500/10 cursor-pointer"
            @click="handleDelete"
          >
            <Trash2 class="size-4 mr-1" />
            Hapus
          </Button>
        </div>

        <div class="flex items-center gap-2">
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
            :disabled="isCheckingConflict"
          >
            Simpan Jadwal
          </Button>
        </div>
      </div>
    </SheetContent>
  </Sheet>
</template>
