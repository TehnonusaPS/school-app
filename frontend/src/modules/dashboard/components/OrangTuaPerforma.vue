<script setup lang="ts">
import { BellRing, ChevronRight } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import { Progress } from '@/components/ui/progress'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import RollingNumber from '@/components/ui/rolling-number/RollingNumber.vue'
import { useAuthStore } from '@/stores/authStore'
import { tableRowFade } from '@/config/motion'
import { dataPerSemester, catatanGuruWali } from '../data/orangTuaPerformaData'

const props = defineProps({
  delay: { type: Number, default: 0 }
})

const auth = useAuthStore()
const computedDelay = computed(() => (auth.isJustLoggedIn ? 1400 : 0) + props.delay)

const semester = ref('ganjil-2026')

const mapel = computed(() => dataPerSemester[semester.value as keyof typeof dataPerSemester] ?? [])

const nilaiColor = (v: number) =>
  v >= 85 ? 'text-emerald-500' : v >= 75 ? 'text-amber-500' : 'text-rose-500'
</script>

<template>
  <WidgetCard
    title="Performa Akademik"
    description="Nilai per mata pelajaran semester ini"
    contentClass="space-y-4"
    footerClass="border-t pt-4"
    :delay="delay"
    illustration="star"
  >
    <template #header-action>
      <Select v-model="semester">
        <SelectTrigger class="h-8 text-xs w-auto min-w-[160px]">
          <SelectValue />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="ganjil-2026" class="text-xs">Semester Ganjil 2026</SelectItem>
          <SelectItem value="genap-2025"  class="text-xs">Semester Genap 2025</SelectItem>
          <SelectItem value="ganjil-2025" class="text-xs">Semester Ganjil 2025</SelectItem>
        </SelectContent>
      </Select>
    </template>

    <div
      v-for="(m, index) in mapel"
      :key="m.nama"
      v-motion
      :initial="tableRowFade.initial"
      :visible-once="{ ...tableRowFade.visible, transition: { ...tableRowFade.visible.transition, delay: computedDelay + 100 + (index * 50) } }"
      class="space-y-1.5 transition-all duration-300"
    >
      <div class="flex items-center justify-between text-sm">
        <span class="font-medium">{{ m.nama }}</span>
        <div class="flex items-center gap-2">
          <span :class="['font-bold tabular-nums text-xs', nilaiColor(m.nilai)]">
            <RollingNumber :value="m.nilai + '%'" :delay="computedDelay + 150 + (index * 50)" />
          </span>
          <ChevronRight class="size-3.5 text-muted-foreground" />
        </div>
      </div>
      <Progress :model-value="m.nilai" :delay="computedDelay + 250 + (index * 50)" class="h-2" />
    </div>

    <template #footer>
      <div class="flex items-start gap-3 rounded-lg bg-muted/50 p-3 w-full">
        <BellRing class="size-4 text-amber-500 shrink-0 mt-0.5" />
        <div>
          <p class="text-xs font-semibold text-foreground">Catatan Guru Wali</p>
          <p class="text-xs text-muted-foreground mt-0.5 leading-relaxed">{{ catatanGuruWali }}</p>
        </div>
      </div>
    </template>
  </WidgetCard>
</template>
