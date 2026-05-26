<script setup>
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
  // Variant warna
  variant: {
    type: String,
    default: 'green',
    validator: (v) => ['green', 'blue', 'purple', 'yellow'].includes(v)
  }
})

const variantStyles = {
  green: {
    bg: 'from-green-100/80 to-green-50/30',
    circleBg: 'bg-green-200/60',
    circleText: 'text-green-700',
    iconBg: 'bg-green-500',
    title: 'text-green-900',
    desc: 'text-green-600',
    trailingColor: 'text-green-700'
  },
  blue: {
    bg: 'from-blue-100/80 to-purple-50/30',
    circleBg: 'bg-blue-200/60',
    circleText: 'text-blue-700',
    iconBg: 'bg-blue-500',
    title: 'text-blue-900',
    desc: 'text-blue-600',
    trailingColor: 'text-blue-700'
  },
  purple: {
    bg: 'from-purple-100/80 to-pink-50/30',
    circleBg: 'bg-purple-200/60',
    circleText: 'text-purple-700',
    iconBg: 'bg-purple-500',
    title: 'text-purple-900',
    desc: 'text-purple-600',
    trailingColor: 'text-purple-700'
  },
  yellow: {
    bg: 'from-amber-100/80 to-amber-50/30',
    circleBg: 'bg-amber-200/60',
    circleText: 'text-amber-700',
    iconBg: 'bg-amber-500',
    title: 'text-amber-900',
    desc: 'text-amber-600',
    trailingColor: 'text-amber-700'
  }
}

const styles = computed(() => variantStyles[props.variant] || variantStyles.green)

// Tentukan mode: jika ada date → mode tanggal, jika ada leadingIcon → mode icon
const isDateMode = computed(() => props.date !== null)
</script>

<template>
  <div
    :class="[
      'flex items-center gap-4 rounded-xl px-4 py-3 bg-gradient-to-r transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 cursor-pointer',
      styles.bg
    ]"
  >
    <!-- Leading: Date Circle -->
    <div
      v-if="isDateMode"
      :class="[
        'flex flex-col items-center justify-center rounded-full w-12 h-12 shrink-0',
        styles.circleBg
      ]"
    >
      <span :class="['text-base font-bold leading-tight', styles.circleText]">{{ date }}</span>
      <span :class="['text-[10px] font-medium leading-tight', styles.circleText]">{{ month }}</span>
    </div>

    <!-- Leading: Icon -->
    <div
      v-else-if="leadingIcon"
      :class="['rounded-lg p-2 text-white shrink-0', styles.iconBg]"
    >
      <component :is="leadingIcon" class="size-4" />
    </div>

    <!-- Content -->
    <div class="flex-1 min-w-0">
      <div :class="['font-semibold text-sm truncate', styles.title]">{{ title }}</div>
      <div v-if="description" :class="['text-xs truncate', styles.desc]">{{ description }}</div>
    </div>

    <!-- Trailing Icon -->
    <div v-if="trailingIcon" class="shrink-0">
      <component :is="trailingIcon" :class="['size-5', styles.trailingColor]" />
    </div>
  </div>
</template>
