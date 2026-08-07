<script setup>
import { Card, CardContent } from '@/components/ui/card'

defineProps({
  icon: {
    type: Object,
    required: true
  },
  iconColor: {
    type: String,
    default: 'text-muted-foreground'
  },
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
  <Card class="border-border/60 shadow-2xs bg-card/60 backdrop-blur-md">
    <CardContent class="py-12 text-center space-y-2">
      <div class="size-10 rounded-full bg-muted/60 flex items-center justify-center mx-auto" :class="iconColor">
        <component :is="icon" class="size-5" />
      </div>
      <p class="text-xs font-bold text-foreground">{{ title }}</p>
      <p v-if="description" class="text-xs text-muted-foreground">{{ description }}</p>
    </CardContent>
  </Card>
</template>
