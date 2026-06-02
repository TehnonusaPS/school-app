<script setup>
import { computed } from 'vue'
import { ChevronLeft, Save } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { pageHeaderSlide } from '@/config/motion'

const props = defineProps({
  variant: {
    type: String,
    default: 'default'
  },
  title: String,
  description: String,
  loading: {
    type: Boolean,
    default: false
  },
  saveText: {
    type: String,
    default: 'Simpan'
  },
  cancelText: {
    type: String,
    default: 'Batal'
  }
})

const emit = defineEmits(['back', 'cancel', 'save'])

const showBackButton = computed(() =>
  ['back', 'form'].includes(props.variant)
)

const showFormActions = computed(() =>
  props.variant === 'form'
)
</script>

<template>
  <div
    v-motion
    :initial="pageHeaderSlide.initial"
    :visible="pageHeaderSlide.visible"
    class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
  >
    <div class="flex items-center gap-4">
      <!-- Default Back Button -->
      <Button
        v-if="showBackButton"
        variant="secondary"
        size="icon"
        class="rounded-full shadow-sm"
        @click="emit('back')"
      >
        <ChevronLeft class="h-5 w-5" />
      </Button>

      <!-- Optional Custom Left Slot -->
      <slot name="left" />
      <div>
        <h1 class="text-3xl font-bold tracking-tight mb-2"> {{ title }}</h1>
        <p v-if="description" class="text-muted-foreground"> {{ description }}</p>
      </div>
    </div>

    <!-- Default Form Actions -->
    <div v-if="showFormActions" class="flex gap-3">
      <Button
        variant="secondary"
        :disabled="loading"
        @click="emit('cancel')"
      >
        {{ cancelText }}
      </Button>

      <Button
        class="shadow-md"
        :disabled="loading"
        @click="emit('save')"
      >
        <Save v-if="!loading" class="mr-2 h-4 w-4"/>
        <span v-else class="mr-2 h-4 w-4 animate-spin border-2 border-current border-t-transparent rounded-full"/>
        {{ loading ? 'Menyimpan...' : saveText }}
      </Button>
    </div>

    <!-- Optional Custom Actions -->
    <div v-else-if="$slots.actions" class="flex gap-3">
      <slot name="actions" />
    </div>
  </div>
</template>