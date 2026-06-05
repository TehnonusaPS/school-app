<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { statCardDemosData } from './data/statCardDemos'
import { Users, UserCheck, Wallet } from 'lucide-vue-next'

const groupCardsData = [
  {
    label: 'Total Siswa',
    value: statCardDemosData.siswa.total,
    sub: statCardDemosData.siswa.sub,
    icon: Users,
    color: 'blue',
    variant: 'default'
  },
  {
    label: 'Absensi Hari Ini',
    value: statCardDemosData.absensi.total,
    sub: statCardDemosData.absensi.sub,
    trend: statCardDemosData.absensi.trend,
    trendDirection: statCardDemosData.absensi.trendDirection,
    icon: UserCheck,
    color: 'emerald',
    variant: 'up'
  },
  {
    label: 'Total Anggaran',
    value: statCardDemosData.anggaran.total,
    sub: statCardDemosData.anggaran.sub,
    progress: statCardDemosData.anggaran.progress,
    icon: Wallet,
    color: 'violet',
    variant: 'progress'
  },
  {
    label: 'Pendaftaran Baru',
    value: statCardDemosData.pendaftaran.total,
    sub: statCardDemosData.pendaftaran.sub,
    trend: statCardDemosData.pendaftaran.trend,
    trendDirection: statCardDemosData.pendaftaran.trendDirection,
    icon: Users,
    color: 'amber',
    variant: 'down'
  },
  {
    label: 'Tingkat Kelulusan',
    value: statCardDemosData.kelulusan.total,
    sub: statCardDemosData.kelulusan.sub,
    trend: statCardDemosData.kelulusan.trend,
    trendDirection: statCardDemosData.kelulusan.trendDirection,
    icon: Users,
    color: 'primary',
    variant: 'neutral'
  }
]
</script>

<template>
  <div class="space-y-10 w-full">
    <!-- Group of 1 Card -->
    <div class="space-y-3">
      <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Demo 1 Card (Full Width - Mobile, Tablet, & Desktop)
      </div>
      <StatCardGrid cols="1">
        <StatCard v-bind="groupCardsData[0]" />
      </StatCardGrid>
    </div>

    <!-- Group of 2 Cards -->
    <div class="space-y-3">
      <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Demo 2 Cards (2 Columns - Mobile, Tablet, & Desktop)
      </div>
      <StatCardGrid cols="2">
        <StatCard
          v-for="(card, i) in groupCardsData.slice(0, 2)"
          :key="i"
          v-bind="card"
        />
      </StatCardGrid>
    </div>

    <!-- Group of 3 Cards -->
    <div class="space-y-3">
      <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Demo 3 Cards (2 Columns on Mobile & Tablet with bottom card full width, 3 Columns on Desktop - Kustom Stagger Delay 500ms)
      </div>
      <StatCardGrid
        cols="3"
        :delay="500"
      >
        <StatCard
          v-for="(card, i) in groupCardsData.slice(0, 3)"
          :key="i"
          v-bind="card"
        />
      </StatCardGrid>
    </div>

    <!-- Group of 4 Cards -->
    <div class="space-y-3">
      <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Demo 4 Cards (2 Columns on Mobile & Tablet, 4 Columns on Desktop - Kustom Stagger Delay 300ms)
      </div>
      <StatCardGrid
        cols="4"
        :delay="300"
      >
        <StatCard
          v-for="(card, i) in groupCardsData.slice(0, 4)"
          :key="i"
          v-bind="card"
        />
      </StatCardGrid>
    </div>

    <!-- Group of 5 Cards -->
    <div class="space-y-3">
      <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Demo 5 Cards (2 Columns on Mobile & Tablet with bottom card full width, 5 Columns on Desktop)
      </div>
      <StatCardGrid cols="5">
        <StatCard
          v-for="(card, i) in groupCardsData"
          :key="i"
          v-bind="card"
        />
      </StatCardGrid>
    </div>
  </div>
</template>
