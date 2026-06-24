<script>
import { defineComponent, h, ref } from 'vue'
import { RotateCcw } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

export const ShowcaseItem = defineComponent({
  name: 'ShowcaseItem',
  props: {
    label: {
      type: String,
      required: true
    }
  },
  setup(props, { slots }) {
    const animationKey = ref(0)
    const replayAnimation = () => {
      animationKey.value++
    }

    return () =>
      h('div', { class: 'py-6 first:pt-0 last:pb-0 space-y-3' }, [
        h('div', { class: 'flex items-center justify-between' }, [
          h(
            'div',
            {
              class: 'text-xs font-semibold text-muted-foreground uppercase tracking-wider'
            },
            props.label
          ),
          h(
            Button,
            {
              variant: 'outline',
              size: 'xs',
              class: 'text-[10px] text-muted-foreground hover:text-foreground flex items-center gap-1 border-border/50 bg-background/50',
              onClick: replayAnimation
            },
            () => [
              h(RotateCcw, { class: 'h-2.5 w-2.5' }),
              'Play Animasi'
            ]
          )
        ]),
        h(
          'div',
          {
            key: animationKey.value,
            class: 'w-full'
          },
          slots.default ? slots.default() : null
        )
      ])
  }
})
</script>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <section class="space-y-4 showcase-section">
    <!-- Section Header -->
    <div class="border-b pb-2">
      <h2 class="text-2xl font-semibold text-primary showcase-section-title">
        {{ title }}
      </h2>
      <p
        v-if="description"
        class="text-sm text-muted-foreground mt-1"
      >
        {{ description }}
      </p>
    </div>

    <!-- Section list wrapper with divider line between each item -->
    <div class="divide-y divide-border mt-6">
      <slot />
    </div>
  </section>
</template>

<style>
body {
  counter-reset: showcase-section-counter;
}
</style>

<style scoped>
.showcase-section {
  counter-increment: showcase-section-counter;
}

.showcase-section-title::before {
  content: counter(showcase-section-counter) ". ";
}
</style>
