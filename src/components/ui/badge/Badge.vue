<script setup lang="ts">
import type { PrimitiveProps } from 'reka-ui'
import type { HTMLAttributes } from 'vue'
import type { BadgeVariants } from '.'
import { reactiveOmit } from '@vueuse/core'
import { Primitive } from 'reka-ui'
import { cn } from '@/lib/utils'
import { badgeVariants } from '.'

const props = defineProps<
  PrimitiveProps & {
    variant?: BadgeVariants['variant']
    class?: HTMLAttributes['class']
    showDot?: boolean
    pulse?: boolean
  }
>()

const delegatedProps = reactiveOmit(props, ['class', 'showDot', 'pulse'])
</script>

<template>
  <Primitive
    data-slot='badge'
    :data-variant="variant"
    :class="cn(badgeVariants({ variant }), props.class)"
    v-bind="delegatedProps"
  >
    <span v-if="showDot" class="relative flex h-1.5 w-1.5 shrink-0">
      <span
        v-if="pulse"
        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-current opacity-75"
      />
      <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-current opacity-80" />
    </span>
    <slot />
  </Primitive>
</template>
