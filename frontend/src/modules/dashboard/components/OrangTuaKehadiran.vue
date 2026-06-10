<script setup lang="ts">
import { computed } from 'vue'
import { Download, ClipboardList } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { tableRowFade } from '@/config/motion'
import { kehadiranData as log } from '../data/orangTuaKehadiranData'

const props = defineProps({
  delay: { type: Number, default: 0 }
})

const auth = useAuthStore()
const computedDelay = computed(() => (auth.isJustLoggedIn ? 1400 : 0) + props.delay)

const badgeVariant = (s: string) =>
  s === 'Tepat Waktu' ? 'default'   :
  s === 'Terlambat'   ? 'secondary' :
  s === 'Izin'        ? 'outline'   : 'destructive'
</script>

<template>
  <WidgetCard
    title="Log Kehadiran"
    description="Rekap kehadiran siswa beberapa hari terakhir"
    :icon="ClipboardList"
    cardClass="flex flex-col"
    contentClass="p-0"
    footerClass="border-t pt-4 mt-auto"
    :delay="delay"
    illustration="school_bell"
  >
    <!-- Fixed header row -->
    <div class="border-b border-t bg-muted/50 px-6 py-2 grid grid-cols-3 text-xs font-medium text-muted-foreground">
      <span>Tanggal</span>
      <span>Jam & Keterangan</span>
      <span class="text-right">Status</span>
    </div>

    <!-- Scrollable content -->
    <div class="overflow-y-auto max-h-[300px] divide-y">
      <div
        v-for="(item, index) in log"
        :key="item.id"
        v-motion
        :initial="tableRowFade.initial"
        :visible-once="{ ...tableRowFade.visible, transition: { ...tableRowFade.visible.transition, delay: computedDelay + 100 + (index * 50) } }"
        class="grid grid-cols-3 items-center px-6 py-3 hover:bg-muted/30 transition-colors cursor-default"
      >
        <p class="text-sm font-semibold">{{ item.tanggal }}</p>
        <p class="text-xs font-medium text-primary">{{ item.jam }}<br/><span class="text-muted-foreground font-normal">{{ item.keterangan }}</span></p>
        <div class="flex justify-end">
          <Badge :variant="badgeVariant(item.status)" class="text-xs">
            {{ item.status }}
          </Badge>
        </div>
      </div>
    </div>

    <template #footer>
      <Button variant="outline" class="w-full gap-2 text-sm">
        <Download class="size-4" />
        Download Rekap
      </Button>
    </template>
  </WidgetCard>
</template>
