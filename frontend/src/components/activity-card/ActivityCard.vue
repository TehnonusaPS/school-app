<script setup>
/**
 * ActivityCard
 * ─────────────────────────────────────────────
 * Card aktivitas reusable yang mengikuti style glassmorphic dari tema.
 * Terdapat dua mode:
 * 1. Date Mode  — leading berupa lingkaran tanggal + bulan
 * 2. Icon Mode  — leading berupa ikon dalam kotak rounded
 *
 * Kedua mode memiliki height yang sama.
 * Warna soft hanya pada leading element (date circle / icon box).
 * Text mengikuti tema (foreground / muted-foreground) agar adaptif dark/light.
 *
 * @usage
 * <!-- Date Mode -->
 * <ActivityCard
 *   :date="18" month="Aug"
 *   title="Ujian Harian" description="7 Hari Lagi"
 *   :trailing-icon="ClipboardList" variant="green"
 * />
 *
 * <!-- Icon Mode -->
 * <ActivityCard
 *   :leading-icon="Play"
 *   title="Bab 1 : Aljabar" description="Diunduh Kemarin"
 *   :trailing-icon="Download" variant="green"
 * />
 */
import { computed } from 'vue'

const props = defineProps({
  // Mode tanggal (opsional)
  date: {
    type: [String, Number],
    default: null
  },
  month: {
    type: String,
    default: ''
  },
  // Mode icon (opsional) — icon di sisi kiri
  leadingIcon: {
    type: Object,
    default: null
  },
  // Konten
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  // Icon di sisi kanan
  trailingIcon: {
    type: Object,
    default: null
  },
  // Variant warna (hanya untuk leading element)
  variant: {
    type: String,
    default: 'green',
    validator: (v) => ['green', 'blue', 'purple', 'yellow', 'amber'].includes(v)
  }
})

// Warna soft hanya untuk leading element (date circle / icon bg)
const variantStyles = {
  green: {
    leadingBg: 'bg-green-100/70 dark:bg-green-900/25',
    leadingText: 'text-green-700 dark:text-green-400',
    iconBg: 'bg-green-500/15 dark:bg-green-500/20',
    iconColor: 'text-green-600 dark:text-green-400'
  },
  blue: {
    leadingBg: 'bg-blue-100/70 dark:bg-blue-900/25',
    leadingText: 'text-blue-700 dark:text-blue-400',
    iconBg: 'bg-blue-500/15 dark:bg-blue-500/20',
    iconColor: 'text-blue-600 dark:text-blue-400'
  },
  purple: {
    leadingBg: 'bg-purple-100/70 dark:bg-purple-900/25',
    leadingText: 'text-purple-700 dark:text-purple-400',
    iconBg: 'bg-purple-500/15 dark:bg-purple-500/20',
    iconColor: 'text-purple-600 dark:text-purple-400'
  },
  amber: {
    leadingBg: 'bg-amber-100/70 dark:bg-amber-900/25',
    leadingText: 'text-amber-700 dark:text-amber-400',
    iconBg: 'bg-amber-500/15 dark:bg-amber-500/20',
    iconColor: 'text-amber-600 dark:text-amber-400'
  },
}

const styles = computed(() => variantStyles[props.variant] || variantStyles.green)

// Tentukan mode: jika ada date → mode tanggal, jika ada leadingIcon → mode icon
const isDateMode = computed(() => props.date !== null)
</script>

<template>
  <div
    class="activity-card flex items-center gap-4 rounded-xl px-4 py-3 min-h-[60px]
           bg-muted/30 dark:bg-white/[0.04]
           border border-border/40 dark:border-white/[0.06]
           transition-all duration-200
           hover:shadow-md hover:-translate-y-0.5 hover:bg-muted/50 dark:hover:bg-white/[0.07]
           cursor-pointer"
  >
    <!-- Leading: Date Circle -->
    <div
      v-if="isDateMode"
      :class="[
        'flex flex-col items-center justify-center rounded-full w-11 h-11 shrink-0',
        styles.leadingBg
      ]"
    >
      <span :class="['text-sm font-bold leading-tight', styles.leadingText]">{{ date }}</span>
      <span :class="['text-[9px] font-semibold leading-tight uppercase', styles.leadingText]">{{ month }}</span>
    </div>

    <!-- Leading: Icon -->
    <div
      v-else-if="leadingIcon"
      :class="[
        'flex items-center justify-center rounded-lg w-11 h-11 shrink-0',
        styles.iconBg
      ]"
    >
      <component :is="leadingIcon" :class="['size-4', styles.iconColor]" />
    </div>

    <!-- Content -->
    <div class="flex-1 min-w-0">
      <div class="font-semibold text-sm truncate text-foreground">{{ title }}</div>
      <div v-if="description" class="text-xs truncate text-muted-foreground">{{ description }}</div>
    </div>

    <!-- Trailing Icon -->
    <div v-if="trailingIcon" class="shrink-0">
      <component :is="trailingIcon" class="size-5 text-muted-foreground" />
    </div>
  </div>
</template>
