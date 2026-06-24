<script setup>
import { ref, computed, watch } from 'vue'
import { toast } from 'vue-sonner'

// UI Components
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import FormSheet from '@/components/data-sheet/FormSheet.vue'

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  mode: {
    type: String,
    default: 'add' // 'add', 'edit', 'view'
  },
  activeTab: {
    type: String,
    required: true // 'materi', 'tugas', 'ujian'
  },
  activeTabLabel: {
    type: String,
    required: true
  },
  initialForm: {
    type: Object,
    required: true
  },
  studentsMap: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update:open', 'save'])

const isOpen = computed({
  get: () => props.open,
  set: (val) => emit('update:open', val)
})

const form = ref({
  kelas: '',
  title: '',
  fileName: '',
  grades: {}
})

// Sync with initialForm when opened or when initialForm changes
watch(
  () => props.open,
  (newOpen) => {
    if (newOpen) {
      form.value = JSON.parse(JSON.stringify(props.initialForm))
    }
  },
  { immediate: true }
)

// Watch class selection to initialize default student grades if in add mode
watch(() => form.value.kelas, (newKelas) => {
  if (props.mode === 'add' && newKelas) {
    const students = props.studentsMap[newKelas] || []
    const newGrades = {}
    students.forEach(st => {
      newGrades[st.id] = ''
    })
    form.value.grades = newGrades
  }
})

const schemaSections = computed(() => {
  const sections = []
  
  if (props.activeTab === 'materi') {
    sections.push({
      id: 'materi-info',
      title: 'Materi Pelajaran',
      fields: [
        {
          label: 'Pilih Kelas',
          key: 'kelas',
          select: true,
          options: [
            { value: 'Kelas 1', label: 'Kelas 1' },
            { value: 'Kelas 2', label: 'Kelas 2' },
            { value: 'Kelas 3', label: 'Kelas 3' }
          ]
        },
        {
          label: 'File Materi Pelajaran',
          key: 'fileName',
          file: true,
          accept: '.pdf,.doc,.docx,.ppt,.pptx,image/*'
        }
      ]
    })
  } else {
    sections.push({
      id: 'tugas-ujian-info',
      title: props.activeTabLabel,
      fields: [
        {
          label: 'Pilih Kelas',
          key: 'kelas',
          select: true,
          options: [
            { value: 'Kelas 1', label: 'Kelas 1' },
            { value: 'Kelas 2', label: 'Kelas 2' },
            { value: 'Kelas 3', label: 'Kelas 3' }
          ]
        },
        {
          label: 'Judul ' + props.activeTabLabel,
          key: 'title',
          placeholder: 'Masukkan Judul ' + props.activeTabLabel
        },
        {
          label: 'Daftar Siswa & Nilai',
          key: 'grades'
        }
      ]
    })
  }
  
  return sections
})

const currentFormStudents = computed(() => {
  if (!form.value.kelas) return []
  return props.studentsMap[form.value.kelas] || []
})

const getInitials = (name) => {
  if (!name) return '?'
  const parts = String(name).trim().split(/\s+/)
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase()
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
}

// --- Save Action ---
const handleSave = () => {
  if (!form.value.kelas) {
    toast.error('Silakan pilih kelas terlebih dahulu')
    return
  }

  if (props.activeTab === 'materi' && !form.value.fileName) {
    toast.error('Silakan unggah file materi terlebih dahulu')
    return
  }

  if (props.activeTab !== 'materi' && !form.value.title.trim()) {
    toast.error(`Nama ${props.activeTabLabel} tidak boleh kosong`)
    return
  }

  // Convert grade values to numbers
  const finalGrades = {}
  Object.keys(form.value.grades).forEach(key => {
    finalGrades[key] = form.value.grades[key] === '' ? 0 : Number(form.value.grades[key])
  })

  emit('save', {
    kelas: form.value.kelas,
    title: props.activeTab === 'materi' ? form.value.fileName : form.value.title,
    fileName: form.value.fileName,
    grades: finalGrades
  })
}
</script>

<template>
  <FormSheet
    v-model:open="isOpen"
    :item="form"
    :avatar-key="false"
    :title="mode === 'add' ? 'Tambah ' + activeTabLabel + ' Baru' : mode === 'edit' ? 'Edit ' + activeTabLabel : 'Detail ' + activeTabLabel"
    :description="mode === 'view' ? 'Melihat rincian informasi dan berkas' : 'Isi form berikut untuk mengelola data pelajaran dan berkas'"
    :sections="schemaSections"
    :disabled="mode === 'view'"
    @change="(val) => {
      form = { ...form, ...val }
    }"
  >
    <!-- Override custom field for grades -->
    <template #field-grades="{ form: localForm }">
      <!-- List Siswa dan Input Nilai -->
      <div v-if="localForm.kelas" class="space-y-4 pt-2 border-t border-border/60">
        <div class="flex items-center justify-between">
          <label class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Daftar Siswa & Nilai</label>
          <Badge variant="secondary" class="text-[10px] font-semibold">
            {{ currentFormStudents.length }} Siswa
          </Badge>
        </div>
        
        <div class="space-y-2.5 max-h-[350px] overflow-y-auto pr-1">
          <div
            v-for="student in currentFormStudents"
            :key="student.id"
            class="flex items-center justify-between p-3 rounded-xl border border-border/40 bg-background/40 backdrop-blur-sm"
          >
            <div class="flex items-center gap-3">
              <Avatar class="size-8 border border-border/50">
                <AvatarFallback class="text-xs font-bold bg-muted/60 text-muted-foreground">
                  {{ getInitials(student.name) }}
                </AvatarFallback>
              </Avatar>
              <span class="text-sm font-semibold text-foreground truncate max-w-[220px]">
                {{ student.name }}
              </span>
            </div>

            <div class="flex items-center gap-2">
              <span class="text-xs font-medium text-muted-foreground">Nilai:</span>
              <Input
                type="number"
                min="0"
                max="100"
                v-model.number="localForm.grades[student.id]"
                :disabled="mode === 'view'"
                class="w-16 h-8 text-center text-sm font-bold bg-background/60"
                placeholder="0"
              />
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-center py-8 text-sm text-muted-foreground italic border border-dashed rounded-xl">
        Pilih kelas terlebih dahulu untuk melihat daftar siswa.
      </div>
    </template>

    <!-- Override actions slot to use custom buttons -->
    <template #actions>
      <Button
        variant="outline"
        size="sm"
        class="h-9 px-4 rounded-xl"
        @click="isOpen = false"
      >
        {{ mode === 'view' ? 'Tutup' : 'Batal' }}
      </Button>
      <Button
        v-if="mode !== 'view'"
        size="sm"
        class="h-9 px-5 rounded-xl bg-primary text-primary-foreground font-semibold"
        @click="handleSave"
      >
        Simpan
      </Button>
    </template>
  </FormSheet>
</template>
