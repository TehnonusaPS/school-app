<script setup>
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'

defineProps({
  classrooms: {
    type: Array,
    default: () => []
  },
  subjects: {
    type: Array,
    default: () => []
  },
  selectedClassroomId: {
    type: String,
    default: ''
  },
  selectedSubjectId: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:selectedClassroomId', 'update:selectedSubjectId'])
</script>

<template>
  <div class="flex flex-wrap items-center justify-between gap-4 glass-ui p-4 rounded-2xl border border-white/10 shadow-sm">
    <div class="flex items-center gap-3">
      <h2 class="text-lg font-bold text-foreground">Mata Pelajaran</h2>
    </div>

    <div class="flex flex-wrap gap-3">
      <!-- Classroom & Semester Selector -->
      <Select 
        :model-value="selectedClassroomId" 
        @update:model-value="val => emit('update:selectedClassroomId', val)"
      >
        <SelectTrigger class="w-[240px] bg-background/50 backdrop-blur-sm">
          <SelectValue placeholder="Pilih Kelas & Semester" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem 
            v-for="c in classrooms" 
            :key="c.classroom_id" 
            :value="String(c.classroom_id)"
          >
            Kelas {{ c.classroom_name }} ({{ c.academic_year_name }} - {{ c.semester_label }})
          </SelectItem>
        </SelectContent>
      </Select>

      <!-- Subject Selector -->
      <Select 
        :model-value="selectedSubjectId" 
        @update:model-value="val => emit('update:selectedSubjectId', val)"
      >
        <SelectTrigger class="w-[200px] bg-background/50 backdrop-blur-sm">
          <SelectValue placeholder="Pilih Mata Pelajaran" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem 
            v-for="sub in subjects" 
            :key="sub.id" 
            :value="String(sub.id)"
          >
            {{ sub.name }}
          </SelectItem>
        </SelectContent>
      </Select>
    </div>
  </div>
</template>
