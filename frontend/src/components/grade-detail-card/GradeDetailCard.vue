<script setup>
/**
 * GradeDetailCard
 * ─────────────────────────────────────────────
 * Menampilkan detail nilai mata pelajaran dengan progress bar per kategori.
 * Menggunakan Card dari ui/card sehingga otomatis mengikuti glassmorphic theme.
 *
 * @usage
 * <GradeDetailCard
 *   title="Detail Nilai Mata Pelajaran Matematika"
 *   :icon="BookOpen"
 *   :categories="gradeDetailCategories"
 * />
 */
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Progress } from '@/components/ui/progress'

defineProps({
  /** Judul card */
  title: {
    type: String,
    default: 'Detail Nilai'
  },
  /** Icon header */
  icon: {
    type: [Object, Function],
    default: null
  },
  /**
   * Array kategori nilai.
   * Format: [{ label, weight, score, maxScore, items: [{ label, value }] }]
   */
  categories: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <Card>
    <CardHeader class="pb-3">
      <CardTitle>
        <div class="flex items-center gap-2">
          <component v-if="icon" :is="icon" class="size-4 text-muted-foreground" />
          <span class="text-foreground">{{ title }}</span>
        </div>
      </CardTitle>
    </CardHeader>

    <CardContent class="space-y-5">
      <div
        v-for="(cat, idx) in categories"
        :key="idx"
        class="space-y-2"
      >
        <!-- Category Header: Label (Weight%) — Score/MaxScore -->
        <div class="flex items-center justify-between">
          <span class="text-sm font-semibold text-foreground">
            {{ cat.label }} ({{ cat.weight }}%)
          </span>
          <span class="text-sm font-bold text-foreground">
            {{ cat.score }}/{{ cat.maxScore }}
          </span>
        </div>

        <!-- Progress Bar -->
        <Progress :model-value="cat.score" class="h-2" />

        <!-- Detail Items -->
        <div class="space-y-1 pl-1">
          <div
            v-for="(item, i) in cat.items"
            :key="i"
            class="flex items-center justify-between text-xs"
          >
            <span class="text-muted-foreground">{{ item.label }}</span>
            <span class="font-medium text-foreground">{{ item.value }}</span>
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
