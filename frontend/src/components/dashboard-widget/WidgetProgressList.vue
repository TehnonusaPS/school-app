<script setup>
import { Progress } from '@/components/ui/progress'
import WidgetCard from './WidgetCard.vue'
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'

const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  icon: { type: [Object, Function], required: false },
  items: { type: Array, required: true },
  cardClass: { type: String, default: '' },
  contentClass: { type: String, default: 'space-y-4' },
  delay: { type: Number, default: 0 }
})

const auth = useAuthStore()
const computedDelay = computed(() => (auth.isJustLoggedIn ? 1400 : 0) + props.delay)
</script>

<template>
  <WidgetCard
    :title="title"
    :description="description"
    :icon="icon"
    :cardClass="cardClass"
    :contentClass="contentClass"
    :delay="delay"
  >
    <div v-for="(item, index) in items" :key="item.label || item.id" class="space-y-1.5">
      <div class="flex items-center justify-between text-sm">
        <span class="font-medium">{{ item.label }}</span>
        <span class="text-muted-foreground text-xs">
          <slot name="value" :item="item">
            {{ item.value }} &bull; {{ item.progress }}%
          </slot>
        </span>
      </div>
      <Progress :model-value="item.progress" :delay="computedDelay + 250 + (index * 60)" class="h-2" :class="item.progressClass" />
      <slot name="item-footer" :item="item" />
    </div>
    
    <slot />
    
    <template v-if="$slots.footer" #footer>
      <slot name="footer" />
    </template>
  </WidgetCard>
</template>
