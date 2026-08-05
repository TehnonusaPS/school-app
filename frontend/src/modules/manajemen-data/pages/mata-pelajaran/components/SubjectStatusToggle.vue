<script setup>
import { ref } from 'vue'
import { toggleSubjectStatus } from '@/services/subjectService'
import { toast } from 'vue-sonner'
import { Power, Check, X } from 'lucide-vue-next'

const props = defineProps({
  subjectId: {
    type: [Number, String],
    required: true
  },
  isActive: {
    type: Boolean,
    default: true
  },
  subjectName: {
    type: String,
    default: 'Mata Pelajaran'
  }
})

const emit = defineEmits(['statusChanged'])

const isUpdating = ref(false)
const currentStatus = ref(props.isActive)

async function handleToggle() {
  isUpdating.value = true
  const previous = currentStatus.value
  currentStatus.value = !currentStatus.value

  try {
    const res = await toggleSubjectStatus(props.subjectId)
    currentStatus.value = res.data.is_active
    toast.success(
      currentStatus.value
        ? `${props.subjectName} berhasil diaktifkan`
        : `${props.subjectName} berhasil dinonaktifkan`
    )
    emit('statusChanged', currentStatus.value)
  } catch (err) {
    currentStatus.value = previous
    toast.error('Gagal memperbarui status mata pelajaran')
  } finally {
    isUpdating.value = false
  }
}
</script>

<template>
  <button
    type="button"
    :disabled="isUpdating"
    @click="handleToggle"
    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 disabled:opacity-50"
    :class="currentStatus ? 'bg-emerald-500 dark:bg-emerald-600' : 'bg-zinc-300 dark:bg-zinc-700'"
  >
    <span
      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white dark:bg-zinc-100 shadow-md ring-0 transition duration-200 ease-in-out flex items-center justify-center text-[10px]"
      :class="currentStatus ? 'translate-x-5 text-emerald-600' : 'translate-x-0 text-zinc-400'"
    >
      <Check v-if="currentStatus" class="h-3 w-3 stroke-[3]" />
      <X v-else class="h-3 w-3 stroke-[3]" />
    </span>
  </button>
</template>
