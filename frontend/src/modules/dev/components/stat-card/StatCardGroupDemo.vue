<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { ShowcaseItem } from '../ShowcaseSection.vue'
import { statCardDemosData } from './data/statCardDemos'
import { Users, UserCheck, Wallet } from 'lucide-vue-next'
</script>

<template>
  <!-- Group of 1 Card -->
  <ShowcaseItem label="Varian Group - 1 Card (Full Width - Mobile, Tablet, & Desktop)">
    <StatCardGrid cols="1">
      <StatCard
        label="Total Siswa"
        :value="statCardDemosData.siswa.total"
        :sub="statCardDemosData.siswa.sub"
        :icon="Users"
        color="blue"
        variant="default"
      />
    </StatCardGrid>
  </ShowcaseItem>

  <!-- Group of 2 Cards -->
  <ShowcaseItem label="Varian Group - 2 Cards (2 Columns - Mobile, Tablet, & Desktop)">
    <StatCardGrid cols="2">
      <StatCard
        label="Total Siswa"
        :value="statCardDemosData.siswa.total"
        :sub="statCardDemosData.siswa.sub"
        :icon="Users"
        color="blue"
        variant="default"
      />

      <StatCard
        label="Absensi Hari Ini"
        :value="statCardDemosData.absensi.total"
        :sub="statCardDemosData.absensi.sub"
        :trend="statCardDemosData.absensi.trend"
        :trendDirection="statCardDemosData.absensi.trendDirection"
        :icon="UserCheck"
        color="emerald"
        variant="up"
      />
    </StatCardGrid>
  </ShowcaseItem>

  <!-- Group of 3 Cards -->
  <ShowcaseItem label="Varian Group - 3 Cards (2 Columns on Mobile & Tablet with bottom card full width, 3 Columns on Desktop - Kustom Stagger Delay 500ms)">
    <StatCardGrid
      cols="3"
      :delay="500"
    >
      <StatCard
        label="Total Siswa"
        :value="statCardDemosData.siswa.total"
        :sub="statCardDemosData.siswa.sub"
        :icon="Users"
        color="blue"
        variant="default"
      />

      <StatCard
        label="Absensi Hari Ini"
        :value="statCardDemosData.absensi.total"
        :sub="statCardDemosData.absensi.sub"
        :trend="statCardDemosData.absensi.trend"
        :trendDirection="statCardDemosData.absensi.trendDirection"
        :icon="UserCheck"
        color="emerald"
        variant="up"
      />

      <StatCard
        label="Total Anggaran"
        :value="statCardDemosData.anggaran.total"
        :sub="statCardDemosData.anggaran.sub"
        :progress="statCardDemosData.anggaran.progress"
        :icon="Wallet"
        color="violet"
        variant="progress"
      />
    </StatCardGrid>
  </ShowcaseItem>

  <!-- Group of 4 Cards -->
  <ShowcaseItem label="Varian Group - 4 Cards (2 Columns on Mobile & Tablet, 4 Columns on Desktop - Kustom Stagger Delay 300ms)">
    <StatCardGrid
      cols="4"
      :delay="300"
    >
      <StatCard
        label="Total Siswa"
        :value="statCardDemosData.siswa.total"
        :sub="statCardDemosData.siswa.sub"
        :icon="Users"
        color="blue"
        variant="default"
      />

      <StatCard
        label="Absensi Hari Ini"
        :value="statCardDemosData.absensi.total"
        :sub="statCardDemosData.absensi.sub"
        :trend="statCardDemosData.absensi.trend"
        :trendDirection="statCardDemosData.absensi.trendDirection"
        :icon="UserCheck"
        color="emerald"
        variant="up"
      />

      <StatCard
        label="Total Anggaran"
        :value="statCardDemosData.anggaran.total"
        :sub="statCardDemosData.anggaran.sub"
        :progress="statCardDemosData.anggaran.progress"
        :icon="Wallet"
        color="violet"
        variant="progress"
      />

      <StatCard
        label="Tingkat Kelulusan"
        :value="statCardDemosData.kelulusan.total"
        :sub="statCardDemosData.kelulusan.sub"
        :trend="statCardDemosData.kelulusan.trend"
        :trendDirection="statCardDemosData.kelulusan.trendDirection"
        :icon="Users"
        color="primary"
        variant="neutral"
      />
    </StatCardGrid>
  </ShowcaseItem>

  <!-- Group of 5 Cards -->
  <ShowcaseItem label="Varian Group - 5 Cards (2 Columns on Mobile & Tablet with bottom card full width, 5 Columns on Desktop)">
    <StatCardGrid cols="5">
      <StatCard
        label="Total Siswa"
        :value="statCardDemosData.siswa.total"
        :sub="statCardDemosData.siswa.sub"
        :icon="Users"
        color="blue"
        variant="default"
      />

      <StatCard
        label="Absensi Hari Ini"
        :value="statCardDemosData.absensi.total"
        :sub="statCardDemosData.absensi.sub"
        :trend="statCardDemosData.absensi.trend"
        :trendDirection="statCardDemosData.absensi.trendDirection"
        :icon="UserCheck"
        color="emerald"
        variant="up"
      />

      <StatCard
        label="Total Anggaran"
        :value="statCardDemosData.anggaran.total"
        :sub="statCardDemosData.anggaran.sub"
        :progress="statCardDemosData.anggaran.progress"
        :icon="Wallet"
        color="violet"
        variant="progress"
      />

      <StatCard
        label="Pendaftaran Baru"
        :value="statCardDemosData.pendaftaran.total"
        :sub="statCardDemosData.pendaftaran.sub"
        :trend="statCardDemosData.pendaftaran.trend"
        :trendDirection="statCardDemosData.pendaftaran.trendDirection"
        :icon="Users"
        color="amber"
        variant="down"
      />

      <StatCard
        label="Tingkat Kelulusan"
        :value="statCardDemosData.kelulusan.total"
        :sub="statCardDemosData.kelulusan.sub"
        :trend="statCardDemosData.kelulusan.trend"
        :trendDirection="statCardDemosData.kelulusan.trendDirection"
        :icon="Users"
        color="primary"
        variant="neutral"
      />
    </StatCardGrid>
  </ShowcaseItem>
</template>
