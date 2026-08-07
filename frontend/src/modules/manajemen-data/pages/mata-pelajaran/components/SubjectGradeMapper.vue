<script setup>
import { ref, computed, onMounted } from 'vue'
import { Check, CheckCheck, GraduationCap, Loader2 } from 'lucide-vue-next'
import { getClassrooms } from '@/services/managementService'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  classrooms: {
    type: Array,
    default: null
  },
  level: {
    type: String,
    default: 'SD' // 'SD' | 'SMP' | 'ALL'
  },
  label: {
    type: String,
    default: 'Daftar Kelas Terdaftar di Sekolah'
  },
  error: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])

const fetchedClassrooms = ref([])
const isLoadingClassrooms = ref(false)

async function loadSchoolClassrooms() {
  if (props.classrooms && props.classrooms.length > 0) return
  isLoadingClassrooms.value = true
  try {
    const res = await getClassrooms()
    fetchedClassrooms.value = res.data || []
  } catch (err) {
    console.error('Gagal mengambil daftar kelas sekolah:', err)
  } finally {
    isLoadingClassrooms.value = false
  }
}

onMounted(() => {
  loadSchoolClassrooms()
})

const activeClassrooms = computed(() => {
  if (props.classrooms && props.classrooms.length > 0) {
    return props.classrooms
  }
  return fetchedClassrooms.value
})

const availableGrades = computed(() => {
  // Jika sekolah sudah mendaftarkan kelas di Daftar Kelas Sekolah, tampilkan kelas-kelas terdaftar tersebut
  if (activeClassrooms.value.length > 0) {
    return activeClassrooms.value.map(c => {
      const gradeNum = Number(c.grade || c.id)
      const formattedLabel = c.name.toLowerCase().startsWith('kelas')
        ? c.name
        : `Kelas ${c.name}`
      return {
        grade: gradeNum,
        id: c.id,
        label: formattedLabel
      }
    })
  }

  // Fallback ke daftar tingkat kelas bawaan jika belum ada kelas terdaftar di sekolah
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
    emit('update:modelValue', [...new Set(allGradeNums)].sort((a, b) => a - b))
  }
}
</script>

<template>
  <div class="space-y-3 text-left">
    <div class="flex items-center justify-between">
      <span class="text-xs font-semibold text-foreground flex items-center gap-1.5">
        <GraduationCap class="h-3.5 w-3.5 text-primary" />
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

    <!-- State Loading Kelas -->
    <div v-if="isLoadingClassrooms" class="flex items-center gap-2 py-3 text-xs text-muted-foreground">
      <Loader2 class="h-3.5 w-3.5 animate-spin text-primary" />
      <span>Memuat daftar kelas terdaftar di sekolah...</span>
    </div>

    <!-- Daftar Kelas Terdaftar (2 Columns Layout) -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
      <button
        v-for="item in availableGrades"
        :key="item.id || item.grade"
        type="button"
        @click="toggleGrade(item.grade)"
        class="px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all flex items-center justify-between border cursor-pointer select-none gap-2"
        :class="
          modelValue.includes(item.grade)
            ? 'bg-primary text-primary-foreground border-primary shadow-xs ring-2 ring-primary/20 font-semibold'
            : 'bg-muted/30 dark:bg-zinc-900/60 border-border/80 dark:border-zinc-800 text-muted-foreground hover:border-primary/50 hover:text-foreground dark:hover:border-zinc-700'
        "
      >
        <span class="font-semibold pr-1 text-left leading-tight select-none">{{ item.label }}</span>
        <span
          class="h-5 w-5 rounded-full flex items-center justify-center text-[10px] shrink-0 font-bold"
          :class="modelValue.includes(item.grade) ? 'bg-primary-foreground/20 text-primary-foreground' : 'bg-muted dark:bg-zinc-800 text-muted-foreground'"
        >
          <Check v-if="modelValue.includes(item.grade)" class="h-3 w-3 stroke-[3]" />
          <span v-else>{{ item.grade }}</span>
        </span>
      </button>
    </div>

    <p v-if="activeClassrooms.length > 0" class="text-[10px] text-muted-foreground">
      Daftar kelas di atas diambil langsung dari daftar kelas yang terdaftar di sekolah ini.
    </p>

    <p v-if="error" class="text-xs text-destructive font-medium pt-0.5">
      {{ error }}
    </p>
  </div>
</template>
