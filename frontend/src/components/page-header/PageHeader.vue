<script setup>
import { useRouter } from 'vue-router'
import { ChevronLeft } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { pageHeaderSlide } from '@/config/motion'

defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  back: {
    type: Boolean,
    default: false
  },
  actions: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['back'])
const router = useRouter()

const handleBack = () => {
  emit('back')
  if (router) {
    router.back()
  } else {
    window.history.back()
  }
}
</script>

<template>
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between w-full">
    <div
      v-motion
      :initial="pageHeaderSlide.initial"
      :visible-once="pageHeaderSlide.visible"
      class="flex items-start gap-3 sm:gap-4 w-full lg:w-auto"
    >
      <Button
        v-if="back"
        variant="secondary"
        size="icon"
        class="rounded-full shadow-sm shrink-0 h-8 w-8 sm:h-9 sm:w-9 mt-0.5 sm:mt-1 flex items-center justify-center"
        @click="handleBack"
      >
        <ChevronLeft class="h-4.5 w-4.5 sm:h-5 sm:w-5" />
      </Button>

      <div class="space-y-1 min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-foreground leading-tight">
          {{ title }}
        </h1>
        <p v-if="description" class="text-muted-foreground text-xs sm:text-sm leading-relaxed">
          {{ description }}
        </p>
      </div>
    </div>

    <!-- Actions buttons on the right side -->
    <div v-if="actions && actions.length" class="grid grid-cols-[repeat(3,max-content)] sm:grid-cols-[repeat(4,max-content)] lg:flex lg:flex-row-reverse gap-3 items-center justify-start w-full lg:w-auto shrink-0 [direction:rtl] lg:[direction:ltr]">
      <Button
        v-for="(action, index) in actions"
        v-motion
        :initial="{ opacity: 0, x: 15 }"
        :visible-once="{
          opacity: 1,
          x: 0,
          transition: { type: 'spring', stiffness: 120, damping: 18, mass: 0.8, delay: 100 + index * 100 }
        }"
        :key="index"
        :variant="action.variant || 'default'"
        size="sm"
        :disabled="action.disabled || action.loading"
        class="[direction:ltr] w-auto justify-center shrink-0"
        @click="action.click"
      >
        <span v-if="action.loading" class="mr-2 h-4 w-4 animate-spin border-2 border-current border-t-transparent rounded-full" />
        <component v-else-if="action.icon" :is="action.icon" class="mr-2 h-4 w-4" />
        {{ action.label }}
      </Button>
    </div>
  </div>
</template>