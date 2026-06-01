<script setup lang="ts">
import { School, Users, Wallet } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card'
import { Progress } from '@/components/ui/progress'
import StatCard from '@/components/stat-card/StatCard.vue'
import { formatNumber, formatDelta } from '@/utils/formatNumber'
import { yayasanStatsData } from '../data/yayasanStats'
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <!-- Stat Cards biasa -->
    <StatCard label="Total Sekolah" :value="formatNumber(yayasanStatsData.sekolah.total)"
      sub="di bawah yayasan" :trend="`${formatDelta(yayasanStatsData.sekolah.growth)} semester ini`"
      :trendDirection="yayasanStatsData.sekolah.trendDirection" :icon="School" variant="violet" />

    <StatCard label="Total Guru & Staff" :value="formatNumber(yayasanStatsData.guru.total)"
      sub="tenaga pendidik aktif" :trend="`${formatDelta(yayasanStatsData.guru.growth)} bulan ini`"
      :trendDirection="yayasanStatsData.guru.trendDirection" :icon="Users" variant="blue" />

    <!-- Card Total Anggaran dengan Progress -->
    <Card class="transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
      <CardHeader class="pb-2">
        <div class="flex items-center justify-between">
          <CardDescription class="text-sm font-medium">Total Anggaran</CardDescription>
          <div class="rounded-lg p-2 bg-emerald-500/10">
            <Wallet class="size-4 text-emerald-500" />
          </div>
        </div>
      </CardHeader>
      <CardContent class="space-y-3">
        <div class="text-3xl font-bold tracking-tight">Rp {{ yayasanStatsData.anggaran.total }}M</div>
        <div class="space-y-1.5">
          <div class="flex items-center justify-between text-xs">
            <span class="text-muted-foreground">Realisasi</span>
            <span class="font-semibold text-emerald-500">{{ yayasanStatsData.anggaran.realisasi }}%</span>
          </div>
          <Progress :model-value="yayasanStatsData.anggaran.realisasi" class="h-2" />
        </div>
      </CardContent>
    </Card>
  </div>
</template>
