<script setup>
import { ScrollArea } from '@/components/ui/scroll-area'
import WidgetCard from './WidgetCard.vue'
import { glassFade } from '@/config/motion'

const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  icon: { type: [Object, Function], required: false },
  footerText: { type: String, default: '' },
  items: { type: Array, required: true },
  cardClass: { type: String, default: '' },
  listClass: { type: String, default: 'h-[360px] px-6' },
  emptyText: { type: String, default: 'Tidak ada data.' },
  delay: { type: Number, default: 0 },
  illustration: { type: String, default: '' }
})

import { useAuthStore } from '@/stores/authStore'
const auth = useAuthStore()
const getDelay = (index) => (auth.isJustLoggedIn ? 1400 : 0) + props.delay + (index * 100)
</script>

<template>
  <WidgetCard
    :title="title"
    :description="description"
    :icon="icon"
    :footerText="footerText"
    contentClass="p-0"
    :cardClass="cardClass"
    :delay="delay"
    :illustration="illustration"
  >
    <template #header-action>
      <slot name="header-action" />
    </template>
    
    <ScrollArea :class="listClass">
      <div v-if="items && items.length > 0" class="space-y-1 py-1">
        <div
          v-for="(item, index) in items"
          :key="item.id || index"
          v-motion
          :initial="glassFade.initial"
          :visible-once="{ ...glassFade.visible, transition: { ...glassFade.visible.transition, delay: getDelay(index) } }"
          class="group flex items-start gap-3 rounded-xl p-3 transition-all hover:bg-white/5 dark:hover:bg-white/5 cursor-default border border-transparent hover:border-white/10 hover:shadow-lg"
        >
          <slot name="item" :item="item" :index="index" />
        </div>
      </div>
      <div v-else class="flex items-center justify-center h-full text-sm text-muted-foreground">
        {{ emptyText }}
      </div>
    </ScrollArea>
    
    <template v-if="$slots.footer" #footer>
      <slot name="footer" />
    </template>
  </WidgetCard>
</template>
