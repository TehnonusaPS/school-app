<script setup>
import { Card } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { formatNumber } from '@/utils/formatNumber'

defineProps({
  utsScore: {
    type: [Number, String],
    default: null
  },
  uasScore: {
    type: [Number, String],
    default: null
  },
  subjectName: {
    type: String,
    default: ''
  },
  semesterLabel: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <div class="grid gap-6 md:grid-cols-2">
    <!-- UTS Card -->
    <Card class="glass-ui p-5 rounded-2xl border border-white/10 shadow-md relative overflow-hidden flex flex-col items-center text-center">
      <div class="absolute top-2 right-2">
        <Badge :variant="utsScore !== null ? 'green' : 'gray'" class="font-bold text-[10px]">
          {{ utsScore !== null ? 'Tuntas' : 'Menunggu' }}
        </Badge>
      </div>
      <span class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">📝 Nilai UTS</span>
      <div class="text-4xl font-extrabold text-foreground tracking-tight my-1">
        {{ utsScore !== null ? formatNumber(utsScore) : 'Belum Ada Nilai' }}
      </div>
      <span class="text-[10px] text-muted-foreground mt-1">{{ subjectName }} &bull; Semester {{ semesterLabel }}</span>
    </Card>

    <!-- UAS Card -->
    <Card class="glass-ui p-5 rounded-2xl border border-white/10 shadow-md relative overflow-hidden flex flex-col items-center text-center">
      <div class="absolute top-2 right-2">
        <Badge :variant="uasScore !== null ? 'green' : 'gray'" class="font-bold text-[10px]">
          {{ uasScore !== null ? 'Tuntas' : 'Menunggu' }}
        </Badge>
      </div>
      <span class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">📝 Nilai UAS</span>
      <div class="text-4xl font-extrabold text-foreground tracking-tight my-1">
        {{ uasScore !== null ? formatNumber(uasScore) : 'Belum Ada Nilai' }}
      </div>
      <span class="text-[10px] text-muted-foreground mt-1">{{ subjectName }} &bull; Semester {{ semesterLabel }}</span>
    </Card>
  </div>
</template>
