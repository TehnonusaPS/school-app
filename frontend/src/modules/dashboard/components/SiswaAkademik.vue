<script setup lang="ts">
import { TrendingUp, MessageCircle } from 'lucide-vue-next'
import { Progress } from '@/components/ui/progress'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import WidgetList from '@/components/dashboard-widget/WidgetList.vue'
import { akademikData as akademik, pesanData as pesan } from '../data/siswaAkademikData'
</script>

<template>
  <div class="lg:col-span-2 flex flex-col gap-4">
    <!-- Akademik -->
    <WidgetCard
      title="Akademik"
      :icon="TrendingUp"
      contentClass="space-y-4"
    >
      <div>
        <div class="flex items-baseline gap-1">
          <span class="text-4xl font-bold tracking-tight">{{ akademik.nilai }}</span>
          <span class="text-sm text-muted-foreground font-medium">/{{ akademik.max }}</span>
        </div>
        <p class="text-xs text-muted-foreground mt-1">Nilai Rata-rata Saat Ini</p>
        <p class="text-xs font-semibold text-emerald-500 mt-0.5">{{ akademik.naik }}</p>
      </div>
      <div class="space-y-1.5">
        <div class="flex items-center justify-between text-xs">
          <span class="text-muted-foreground">Kehadiran</span>
          <span class="font-semibold">{{ akademik.kehadiran }}%</span>
        </div>
        <Progress :model-value="akademik.kehadiran" class="h-2" />
      </div>
    </WidgetCard>

    <!-- Komunikasi Terakhir -->
    <WidgetList
      title="Komunikasi Terakhir"
      description="Pesan dari guru & wali kelas"
      :icon="MessageCircle"
      cardClass="flex-1"
      listClass="h-[250px] px-4"
      :items="pesan"
    >
      <template #item="{ item: p }">
        <div :class="['size-9 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0', p.warna]">
          {{ p.inisial }}
        </div>
        <div class="min-w-0 flex-1 ml-2">
          <p class="text-sm font-semibold leading-snug truncate">{{ p.nama }}</p>
          <p class="text-xs text-muted-foreground mt-0.5 line-clamp-1">{{ p.isi }}</p>
          <p class="text-[10px] text-muted-foreground/70 mt-0.5">{{ p.waktu }}</p>
        </div>
      </template>
    </WidgetList>
  </div>
</template>
