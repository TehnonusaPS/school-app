<script setup lang="ts">
import { BookOpen, ChevronRight } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import { tugasData as tugas } from '../data/siswaTugasData'

const props = defineProps({
  delay: { type: Number, default: 0 }
})

const statusVariant = (s: string) => s === 'Selesai' ? 'default' : 'secondary'
</script>

<template>
  <WidgetCard
    title="Tugas & Ujian Mendatang"
    description="Daftar tugas dan ujian yang perlu diselesaikan"
    :icon="BookOpen"
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
        <col style="width: 30%" />
        <col style="width: 20%" />
        <col style="width: 20%" />
        <col style="width: 15%" />
        <col style="width: 15%" />
      </colgroup>
      <thead>
        <tr class="border-b bg-muted/50">
          <th class="pl-6 py-3 text-left text-xs font-medium text-muted-foreground">Nama Tugas</th>
          <th class="py-3 text-left text-xs font-medium text-muted-foreground">Guru</th>
          <th class="py-3 text-center text-xs font-medium text-muted-foreground">Deadline</th>
          <th class="py-3 text-center text-xs font-medium text-muted-foreground">Status</th>
          <th class="pr-6 py-3 text-right text-xs font-medium text-muted-foreground">Action</th>
        </tr>
      </thead>
    </table>

    <!-- Body -->
    <table class="w-full text-sm">
      <colgroup>
        <col style="width: 30%" />
        <col style="width: 20%" />
        <col style="width: 20%" />
        <col style="width: 15%" />
        <col style="width: 15%" />
      </colgroup>
      <tbody>
        <tr
          v-for="(t, i) in tugas"
          :key="i"
          class="border-b transition-colors hover:bg-muted/50 cursor-default"
        >
          <td class="pl-6 py-3 font-semibold text-sm">{{ t.nama }}</td>
          <td class="py-3 text-muted-foreground text-sm">{{ t.guru }}</td>
          <td class="py-3 text-center text-sm text-muted-foreground">{{ t.deadline }}</td>
          <td class="py-3 text-center">
            <Badge :variant="statusVariant(t.status)" class="text-xs">
              {{ t.status }}
            </Badge>
          </td>
          <td class="pr-6 py-3 text-right">
            <Button size="sm" class="h-7 text-xs">
              {{ t.tipe === 'upload' ? 'Upload Tugas' : 'Buka Penilaian' }}
            </Button>
          </td>
        </tr>
      </tbody>
    </table>
  </WidgetCard>
</template>
