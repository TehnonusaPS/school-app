<script setup>
/**
 * ProgressCircle
 * ─────────────────────────────────────────────
 * Komponen reusable SVG circular progress indicator.
 * Track mengikuti warna tema (muted), progress menggunakan warna variant.
 *
 * @usage
 * <ProgressCircle :percentage="82.5" variant="blue" :size="140" :stroke-width="10">
 *   <span class="text-3xl font-bold">82,5</span>
 * </ProgressCircle>
 */
import { computed } from 'vue'

const props = defineProps({
  /** Persentase progress (0-100) */
  percentage: {
    type: Number,
    default: 0
  },
  /** Ukuran SVG (width & height) dalam px */
  size: {
    type: Number,
    default: 140
  },
  /** Ketebalan stroke */
  strokeWidth: {
    type: Number,
    default: 10
  },
  /** Variant warna progress */
  variant: {
    type: String,
    default: 'blue',
    validator: (v) => ['blue', 'green', 'purple', 'amber'].includes(v)
  }
})

const radius = computed(() => (props.size - props.strokeWidth) / 2)
const circumference = computed(() => 2 * Math.PI * radius.value)
const dashOffset = computed(() => {
  const pct = Math.max(0, Math.min(100, props.percentage))
  return circumference.value - (pct / 100) * circumference.value
})

const gradientId = computed(() => `progress-gradient-${props.variant}`)

const gradientColors = {
  blue: { start: '#60a5fa', end: '#3b82f6' },
  green: { start: '#4ade80', end: '#22c55e' },
  purple: { start: '#c084fc', end: '#a855f7' },
  amber: { start: '#fbbf24', end: '#f59e0b' }
}

const colors = computed(() => gradientColors[props.variant] || gradientColors.blue)
</script>

<template>
  <div class="relative inline-flex" :style="{ width: `${size}px`, height: `${size}px` }">
    <svg :width="size" :height="size" class="transform -rotate-90">
      <defs>
        <linearGradient :id="gradientId" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" :stop-color="colors.start" />
          <stop offset="100%" :stop-color="colors.end" />
        </linearGradient>
      </defs>

      <!-- Track (background circle) — uses theme muted color -->
      <circle
        :cx="size / 2"
        :cy="size / 2"
        :r="radius"
        fill="none"
        class="stroke-muted"
        :stroke-width="strokeWidth"
      />

      <!-- Progress arc -->
      <circle
        :cx="size / 2"
        :cy="size / 2"
        :r="radius"
        fill="none"
        :stroke="`url(#${gradientId})`"
        :stroke-width="strokeWidth"
        stroke-linecap="round"
        :stroke-dasharray="circumference"
        :stroke-dashoffset="dashOffset"
        class="transition-all duration-700 ease-out"
      />
    </svg>

    <!-- Center content (slot) -->
    <div class="absolute inset-0 flex flex-col items-center justify-center">
      <slot />
    </div>
  </div>
</template>
