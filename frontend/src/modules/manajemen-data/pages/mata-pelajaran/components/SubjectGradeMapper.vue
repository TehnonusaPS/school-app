<script setup>
import { computed } from 'vue'
import { Check, CheckCheck } from 'lucide-vue-next'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  level: {
    type: String,
    default: 'SD' // 'SD' | 'SMP' | 'ALL'
  },
  label: {
    type: String,
    default: 'Tingkat Kelas Pembelajar'
  },
  error: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])

const availableGrades = computed(() => {
  if (props.level === 'SD') {
    return [
      { grade: 1, label: 'Kelas 1' },
      { grade: 2, label: 'Kelas 2' },
      { grade: 3, label: 'Kelas 3' },
      { grade: 4, label: 'Kelas 4' },
      { grade: 5, label: 'Kelas 5' },
      { grade: 6, label: 'Kelas 6' }
    ]
  } else if (props.level === 'SMP') {
    return [
      { grade: 7, label: 'Kelas 7' },
      { grade: 8, label: 'Kelas 8' },
      { grade: 9, label: 'Kelas 9' }
    ]
  } else {
    return [
      { grade: 1, label: 'Kelas 1' },
      { grade: 2, label: 'Kelas 2' },
      { grade: 3, label: 'Kelas 3' },
      { grade: 4, label: 'Kelas 4' },
      { grade: 5, label: 'Kelas 5' },
      { grade: 6, label: 'Kelas 6' },
      { grade: 7, label: 'Kelas 7' },
      { grade: 8, label: 'Kelas 8' },
      { grade: 9, label: 'Kelas 9' }
    ]
  }
})

const isAllSelected = computed(() => {
  if (availableGrades.value.length === 0) return false
  return availableGrades.value.every(g => props.modelValue.includes(g.grade))
})

function toggleGrade(gradeNum) {
  const current = [...props.modelValue]
  const idx = current.indexOf(gradeNum)
  if (idx > -1) {
    current.splice(idx, 1)
  } else {
    current.push(gradeNum)
  }
  emit('update:modelValue', current.sort((a, b) => a - b))
}

function toggleSelectAll() {
  if (isAllSelected.value) {
    emit('update:modelValue', [])
  } else {
    const allGradeNums = availableGrades.value.map(g => g.grade)
    emit('update:modelValue', allGradeNums)
  }
}
</script>

<template>
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <span class="text-xs font-semibold text-foreground">
        {{ label }}
      </span>
      <button
        type="button"
        @click="toggleSelectAll"
        class="text-[11px] text-primary hover:text-primary/80 font-bold transition-all flex items-center gap-1 cursor-pointer"
      >
        <CheckCheck class="h-3.5 w-3.5" />
        {{ isAllSelected ? 'Batalkan Semua' : 'Pilih Semua Kelas' }}
      </button>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
      <button
        v-for="item in availableGrades"
        :key="item.grade"
        type="button"
        @click="toggleGrade(item.grade)"
        class="px-3 py-2 rounded-xl text-xs font-medium transition-all flex items-center justify-between border cursor-pointer select-none"
        :class="
          modelValue.includes(item.grade)
            ? 'bg-primary text-primary-foreground border-primary shadow-sm ring-2 ring-primary/20 font-semibold'
            : 'bg-muted/30 dark:bg-zinc-900/60 border-border/80 dark:border-zinc-800 text-muted-foreground hover:border-primary/50 hover:text-foreground dark:hover:border-zinc-700'
        "
      >
        <span>{{ item.label }}</span>
        <span
          class="h-4 w-4 rounded-full flex items-center justify-center text-[10px] shrink-0"
          :class="modelValue.includes(item.grade) ? 'bg-primary-foreground/20 text-primary-foreground' : 'bg-muted dark:bg-zinc-800 text-muted-foreground'"
        >
          <Check v-if="modelValue.includes(item.grade)" class="h-3 w-3 stroke-[3]" />
          <span v-else>{{ item.grade }}</span>
        </span>
      </button>
    </div>

    <p v-if="error" class="text-xs text-destructive font-medium pt-0.5">
      {{ error }}
    </p>
  </div>
</template>
