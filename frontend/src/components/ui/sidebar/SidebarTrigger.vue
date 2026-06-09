<script setup lang="ts">
import type { HTMLAttributes } from 'vue'

import { PanelLeftIcon, Menu } from 'lucide-vue-next'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { useSidebar } from './utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()

const { toggleSidebar, isMobile } = useSidebar()
</script>

<template>
  <Button
    data-sidebar="trigger"
    data-slot="sidebar-trigger"
    variant="ghost"
    size="icon-sm"
    :class="cn('', props.class)"
    @click="toggleSidebar"
  >
    <component :is="isMobile ? Menu : PanelLeftIcon" class="cn-rtl-flip" />
    <span class="sr-only">Toggle Sidebar</span>
  </Button>
</template>
