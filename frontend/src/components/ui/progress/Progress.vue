<script setup lang="ts">
import type { ProgressRootProps } from 'reka-ui'
import type { HTMLAttributes } from 'vue'
import { ref, onMounted, watch } from 'vue'
import { reactiveOmit } from '@vueuse/core'
import {
  ProgressIndicator,
  ProgressRoot,
} from 'reka-ui'
import { cn } from '@/lib/utils'

const props = withDefaults(
  defineProps<ProgressRootProps & { class?: HTMLAttributes['class']; delay?: number }>(),
  {
    modelValue: 0,
    delay: 0,
  },
)

const delegatedProps = reactiveOmit(props, 'class')

const widthPercent = ref(0)

onMounted(() => {
  setTimeout(() => {
    requestAnimationFrame(() => {
      widthPercent.value = props.modelValue ?? 0
    })
  }, props.delay || 80)
})

watch(() => props.modelValue, (newVal) => {
  widthPercent.value = newVal ?? 0
})
</script>

<template>
  <ProgressRoot
    data-slot="progress"
    v-bind="delegatedProps"
    :class="
      cn(
        'bg-muted h-1 rounded-full relative flex w-full items-center overflow-x-hidden',
        props.class,
      )
    "
  >
    <ProgressIndicator
      data-slot="progress-indicator"
      class="bg-primary size-full flex-1 transition-transform duration-[1200ms] ease-out"
      :style="`transform: translateX(-${100 - widthPercent}%);`"
    />
  </ProgressRoot>
</template>
