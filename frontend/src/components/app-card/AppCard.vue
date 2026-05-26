<script setup>
import {
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle
} from '@/components/ui/card'

defineProps({
  /** Judul card (opsional, bisa pakai slot #title) */
  title: {
    type: String,
    default: ''
  },
  /** Deskripsi card (opsional, bisa pakai slot #description) */
  description: {
    type: String,
    default: ''
  },
  /** Icon di samping title */
  icon: {
    type: Object,
    default: null
  },
  /** Ukuran card: 'default' | 'sm' */
  size: {
    type: String,
    default: 'default'
  },
  /** Class tambahan untuk Card wrapper */
  cardClass: {
    type: String,
    default: ''
  },
  /** Class tambahan untuk CardHeader */
  headerClass: {
    type: String,
    default: ''
  },
  /** Class tambahan untuk CardContent */
  contentClass: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <Card :size="size" :class="cardClass">
    <!--
      Header ditampilkan jika ada:
      title/description (prop atau slot), icon, slot #action, atau slot #header
    -->
    <CardHeader
      v-if="title || description || icon || $slots.title || $slots.description || $slots.action || $slots.header"
      :class="headerClass"
    >
      <!-- Slot #header untuk override seluruh isi header -->
      <slot v-if="$slots.header" name="header" />

      <template v-else>
        <!-- Title -->
        <CardTitle v-if="title || $slots.title">
          <div v-if="icon" class="flex items-center gap-2">
            <component :is="icon" class="size-4 text-primary" />
            <slot name="title">{{ title }}</slot>
          </div>
          <slot v-else name="title">{{ title }}</slot>
        </CardTitle>

        <!-- Description -->
        <CardDescription v-if="description || $slots.description">
          <slot name="description">{{ description }}</slot>
        </CardDescription>

        <!-- Action (kanan atas) -->
        <CardAction v-if="$slots.action">
          <slot name="action" />
        </CardAction>
      </template>
    </CardHeader>

    <!-- Content (default slot) -->
    <CardContent v-if="$slots.default" :class="contentClass">
      <slot />
    </CardContent>

    <!-- Footer -->
    <CardFooter v-if="$slots.footer">
      <slot name="footer" />
    </CardFooter>
  </Card>
</template>
