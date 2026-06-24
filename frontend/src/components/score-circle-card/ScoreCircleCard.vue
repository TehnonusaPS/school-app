<script setup>
/**
 * ScoreCircleCard
 * ─────────────────────────────────────────────
 * Card glassmorphic (tanpa background warna) dengan circular progress.
 * Hanya progress ring yang berwarna sesuai variant.
 * Text presentase mengikuti tema:
 *   - Light mode: warna gelap dari variant
 *   - Dark mode: warna soft dari variant
 *
 * @usage
 * <ScoreCircleCard
 *   title="Rata-rata Nilai Matematika"
 *   score="82,5"
 *   label="Cukup Baik !"
 *   description="Anda berada di 5% teratas kelas"
 *   :percentage="82.5"
 *   variant="blue"
 * />
 */
import { computed } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { ProgressCircle } from '@/components/ui/progress-circle'

const props = defineProps({
  /** Judul card */
  title: {
    type: String,
    default: ''
  },
  /** Skor ditampilkan di tengah donut */
  score: {
    type: [String, Number],
    default: '0'
  },
  /** Label di bawah donut, misal "Cukup Baik !" */
  label: {
    type: String,
    default: ''
  },
  /** Deskripsi tambahan */
  description: {
    type: String,
    default: ''
  },
  /** Persentase progress (0-100) */
  percentage: {
    type: Number,
    default: 0
  },
  /** Variant warna progress: "blue" | "green" | "purple" | "amber" */
  variant: {
    type: String,
    default: 'blue',
    validator: (v) => ['blue', 'green', 'purple', 'amber'].includes(v)
  }
})

/**
 * Warna text presentase/skor sesuai tema:
 * Light mode → warna gelap dari variant
 * Dark mode → warna soft dari variant
 */
const variantTextColors = {
  blue: 'text-blue-700 dark:text-blue-400',
  green: 'text-green-700 dark:text-green-400',
  purple: 'text-purple-700 dark:text-purple-400',
  amber: 'text-amber-700 dark:text-amber-400'
}

const variantLabelColors = {
  blue: 'text-blue-600 dark:text-blue-300',
  green: 'text-green-600 dark:text-green-300',
  purple: 'text-purple-600 dark:text-purple-300',
  amber: 'text-amber-600 dark:text-amber-300'
}

const scoreColor = computed(() => variantTextColors[props.variant] || variantTextColors.blue)
const labelColor = computed(() => variantLabelColors[props.variant] || variantLabelColors.blue)
</script>

<template>
  <Card>
    <CardContent class="flex flex-col items-center text-center py-6 px-5 min-h-[280px] justify-center">
      <!-- Title -->
      <h3 class="text-sm font-bold mb-4 leading-snug text-foreground">{{ title }}</h3>

      <!-- Circular Progress -->
      <div class="mb-3">
        <ProgressCircle
          :percentage="percentage"
          :size="140"
          :stroke-width="10"
          :variant="variant"
        >
          <span :class="['text-3xl font-bold leading-none', scoreColor]">{{ score }}</span>
        </ProgressCircle>
      </div>

      <!-- Label -->
      <div v-if="label" :class="['text-sm font-bold mt-1', labelColor]">
        {{ label }}
      </div>

      <!-- Description -->
      <p v-if="description" class="text-xs text-muted-foreground mt-1 leading-relaxed max-w-[200px]">
        {{ description }}
      </p>
    </CardContent>
  </Card>
</template>
