<script setup lang="ts">
import { ClipboardList, ChevronRight } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Progress } from '@/components/ui/progress'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import { penilaianData as tugas } from '../data/guruPenilaianData'

const props = defineProps({
  delay: { type: Number, default: 0 }
})

const statusVariant = (s: string) => s === 'Selesai' ? 'default' : 'secondary'
</script>

<template>
  <WidgetCard
    title="Penilaian Menunggu"
    description="Daftar tugas yang belum diberikan feedback"
    :icon="ClipboardList"
    contentClass="p-0"
    :delay="delay"
  >
    <template #header-action>
      <Button variant="ghost" size="sm" class="h-8 text-xs gap-1 text-muted-foreground hover:text-foreground">
        Lihat Semua Tugas
        <ChevronRight class="size-3.5" />
      </Button>
    </template>

    <!-- Fixed header -->
    <table class="w-full text-sm border-b">
      <colgroup>
        <col style="width: 32%" />
        <col style="width: 10%" />
        <col style="width: 26%" />
        <col style="width: 16%" />
        <col style="width: 16%" />
      </colgroup>
      <thead>
        <tr class="border-b bg-muted/50">
          <th class="pl-6 py-3 text-left text-xs font-medium text-muted-foreground">Nama Tugas</th>
          <th class="py-3 text-center text-xs font-medium text-muted-foreground">Kelas</th>
          <th class="py-3 text-left text-xs font-medium text-muted-foreground">Terkumpul</th>
          <th class="py-3 text-center text-xs font-medium text-muted-foreground">Status</th>
          <th class="pr-6 py-3 text-right text-xs font-medium text-muted-foreground">Action</th>
        </tr>
      </thead>
    </table>

    <!-- Body -->
    <table class="w-full text-sm">
      <colgroup>
        <col style="width: 32%" />
        <col style="width: 10%" />
        <col style="width: 26%" />
        <col style="width: 16%" />
        <col style="width: 16%" />
      </colgroup>
      <tbody>
        <tr
          v-for="(t, i) in tugas"
          :key="i"
          class="border-b transition-colors hover:bg-muted/50 cursor-default"
        >
          <td class="pl-6 py-3">
            <p class="font-semibold text-sm">{{ t.nama }}</p>
            <p class="text-[11px] text-muted-foreground mt-0.5">{{ t.sub }}</p>
          </td>
          <td class="py-3 text-center">
            <Badge variant="outline" class="text-xs font-bold">{{ t.kelas }}</Badge>
          </td>
          <td class="py-3 pr-4">
            <div class="flex items-center gap-2">
              <Progress :model-value="t.terkumpul" class="h-2 flex-1" />
              <span class="text-xs text-muted-foreground tabular-nums w-8 shrink-0">{{ t.terkumpul }}%</span>
            </div>
          </td>
          <td class="py-3 text-center">
            <Badge :variant="statusVariant(t.status)" class="text-xs">
              {{ t.status }}
            </Badge>
          </td>
          <td class="pr-6 py-3 text-right">
            <Button size="sm" class="h-7 text-xs">Buka Penilaian</Button>
          </td>
        </tr>
      </tbody>
    </table>
  </WidgetCard>
</template>
