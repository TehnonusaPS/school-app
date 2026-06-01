<script setup lang="ts">
import WidgetProgressList from '@/components/dashboard-widget/WidgetProgressList.vue'
import { kehadiranProgressData, kehadiranQuickStats } from '../data/kepsekKehadiranData'
</script>

<template>
  <WidgetProgressList
    title="Kehadiran Hari Ini"
    description="Rekap kehadiran siswa, guru, dan staff"
    cardClass="lg:col-span-3"
    contentClass="space-y-5"
    :items="kehadiranProgressData"
  >
    <template #value="{ item }">
      <span class="text-sm font-bold tabular-nums text-foreground">{{ item.value }}</span>
    </template>
    <template #item-footer="{ item }">
      <div class="flex items-center gap-4 text-xs text-muted-foreground mt-2">
        <span class="flex items-center gap-1">
          <span class="inline-block size-2 rounded-full bg-emerald-500"></span>
          Hadir ({{ item.stats.hadir_pct }}%)
        </span>
        <span class="flex items-center gap-1">
          <span class="inline-block size-2 rounded-full bg-rose-500"></span>
          Alpa ({{ item.stats.alpa_pct }}%)
        </span>
        <span class="flex items-center gap-1">
          <span class="inline-block size-2 rounded-full bg-amber-400"></span>
          Izin ({{ item.stats.izin_pct }}%)
        </span>
      </div>
      <div class="border-t my-4" />
    </template>

    <!-- Ringkasan -->
    <div class="mt-2">
      <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">Ringkasan Hari Ini</p>
      <div class="grid grid-cols-2 gap-3">
        <div
          v-for="stat in kehadiranQuickStats"
          :key="stat.label"
          class="rounded-lg bg-muted/50 p-3 space-y-0.5"
        >
          <p class="text-[10px] text-muted-foreground">{{ stat.label }}</p>
          <p class="text-sm font-bold" :class="stat.valueClass">{{ stat.value }}</p>
        </div>
      </div>
    </div>
  </WidgetProgressList>
</template>
