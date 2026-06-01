<script setup lang="ts">
import { CalendarDays } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import { jadwalData as jadwal } from '../data/guruJadwalData'

const today = new Date().toLocaleDateString('id-ID', {
  weekday: 'long', day: '2-digit', month: 'short', year: 'numeric'
}).toUpperCase()
</script>

<template>
  <WidgetCard
    title="Jadwal Mengajar Hari Ini"
    :description="`${jadwal.length} sesi mengajar terjadwal hari ini`"
    :icon="CalendarDays"
    cardClass="lg:col-span-3"
    contentClass="space-y-2 pb-4"
  >
    <template #header-action>
      <Badge variant="outline" class="text-xs font-semibold text-primary">
        {{ today }}
      </Badge>
    </template>

    <div
      v-for="(j, i) in jadwal"
      :key="j.id"
      :class="[
        'flex items-center gap-4 rounded-xl border px-4 py-3 transition-colors',
        j.aktif ? 'bg-primary/5 border-primary/30' : 'hover:bg-muted/40'
      ]"
    >
      <!-- Waktu -->
      <div class="min-w-[70px] text-right">
        <p :class="['text-sm font-bold tabular-nums', j.aktif ? 'text-primary' : 'text-foreground']">
          {{ j.mulai }} WIB
        </p>
        <p class="text-[10px] text-muted-foreground tabular-nums">{{ j.selesai }} WIB</p>
      </div>

      <!-- Divider -->
      <div :class="['w-px self-stretch', j.aktif ? 'bg-primary/40' : 'bg-border']" />

      <!-- Info -->
      <div class="flex-1 min-w-0">
        <p :class="['text-sm font-semibold truncate', j.aktif ? 'text-primary' : '']">
          {{ j.mapel }}
        </p>
        <p class="text-xs text-muted-foreground">{{ j.kelas }}</p>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-2 shrink-0">
        <Button variant="outline" size="sm" class="h-8 text-xs">Materi</Button>
        <Button
          :variant="j.aktif ? 'default' : 'secondary'"
          size="sm"
          class="h-8 text-xs"
        >
          Presensi
        </Button>
      </div>
    </div>
  </WidgetCard>
</template>
