<script setup>
import { computed } from 'vue'

const props = defineProps({
  cols: {
    type: [String, Number],
    default: 4
  },
  delay: {
    type: Number,
    default: 100
  }
})

const gridColsClass = computed(() => {
  const c = String(props.cols)
  switch (c) {
    case '1':
      return 'grid-cols-1'
    case '2':
      return 'grid-cols-2'
    case '3':
      return 'grid-cols-2 lg:grid-cols-3'
    case '4':
      return 'grid-cols-2 lg:grid-cols-4'
    case '5':
      return 'grid-cols-2 lg:grid-cols-5'
    default:
      return 'grid-cols-2 lg:grid-cols-4'
  }
})

const isMoreThanTwo = computed(() => {
  const c = Number(props.cols)
  return c > 2
})
</script>

<template>
  <div
    :class="['grid gap-4 sm:gap-6 w-full stat-card-grid', gridColsClass, { 'cols-gt-2': isMoreThanTwo }]"
    :style="{ '--stagger-delay': `${delay}ms` }"
  >
    <slot />
  </div>
</template>

<style scoped>
/* Inject staggered delay indexes from 0 to 9 */
.stat-card-grid > :deep(*:nth-child(1)) { --delay-index: 0; }
.stat-card-grid > :deep(*:nth-child(2)) { --delay-index: 1; }
.stat-card-grid > :deep(*:nth-child(3)) { --delay-index: 2; }
.stat-card-grid > :deep(*:nth-child(4)) { --delay-index: 3; }
.stat-card-grid > :deep(*:nth-child(5)) { --delay-index: 4; }
.stat-card-grid > :deep(*:nth-child(6)) { --delay-index: 5; }
.stat-card-grid > :deep(*:nth-child(7)) { --delay-index: 6; }
.stat-card-grid > :deep(*:nth-child(8)) { --delay-index: 7; }
.stat-card-grid > :deep(*:nth-child(9)) { --delay-index: 8; }
.stat-card-grid > :deep(*:nth-child(10)) { --delay-index: 9; }

@media (max-width: 1023px) {
  /* On mobile and tablet, if grid has 3 or 5 columns on desktop, 
     an odd-numbered last child (e.g. 3rd child in a 3-card grid, or 5th child in a 5-card grid) 
     will span full-width (2 columns) to balance the layout. */
  .stat-card-grid.cols-gt-2 > :deep(*:nth-child(odd):last-child) {
    grid-column: span 2 / span 2;
  }
}
</style>
